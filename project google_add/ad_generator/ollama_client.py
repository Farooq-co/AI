"""
Ollama Client

Handles communication with local Ollama API for text and vision-based AI tasks.
Supports various models including LLaVA for vision capabilities.
"""

import base64
import json
from typing import Optional, List, Dict, Any
import aiohttp
import asyncio


class OllamaClient:
    """
    Client for interacting with Ollama API.
    
    Supports:
    - Text generation (uses fast model by default)
    - Vision analysis (uses LLaVA model for images)
    - Multiple model selection
    """
    
    def __init__(
        self,
        base_url: str = "http://localhost:11434",
        model: str = "llava",
        text_model: str = "llama3.2:1b",  # Fast model for text generation
        timeout: int = 300
    ):
        """
        Initialize the Ollama client.
        
        Args:
            base_url: Base URL for Ollama API (default: http://localhost:11434)
            model: Vision model name for image analysis (default: llava)
            text_model: Fast model name for text generation (default: llama3.2:1b)
            timeout: Request timeout in seconds
        """
        self.base_url = base_url.rstrip("/")
        self.model = model  # Vision model (for images)
        self.text_model = text_model  # Fast model (for text)
        self.timeout = timeout
    
    async def generate_text(
        self,
        prompt: str,
        system_prompt: Optional[str] = None,
        temperature: float = 0.7,
        max_tokens: int = 500,
        use_fast_model: bool = True
    ) -> str:
        """
        Generate text using Ollama API.
        
        Args:
            prompt: User prompt
            system_prompt: Optional system prompt
            temperature: Sampling temperature (0.0 to 1.0)
            max_tokens: Maximum tokens to generate
            use_fast_model: If True, uses fast text model; if False, uses vision model
            
        Returns:
            Generated text response
        """
        # Use fast text model for text generation, vision model only when explicitly needed
        model = self.text_model if use_fast_model else self.model
        
        payload = {
            "model": model,
            "prompt": prompt,
            "stream": False,
            "options": {
                "temperature": temperature,
                "num_predict": max_tokens
            }
        }
        
        if system_prompt:
            payload["system"] = system_prompt
        
        async with aiohttp.ClientSession() as session:
            async with session.post(
                f"{self.base_url}/api/generate",
                json=payload,
                timeout=aiohttp.ClientTimeout(total=self.timeout)
            ) as response:
                if response.status != 200:
                    error_text = await response.text()
                    raise Exception(f"Ollama API error: {response.status} - {error_text}")
                
                result = await response.json()
                return result.get("response", "")
    
    async def analyze_image(
        self,
        image_path: str,
        prompt: str = "Describe this image in detail, focusing on objects, colors, text, and any notable features."
    ) -> str:
        """
        Analyze an image using vision-capable model.
        
        Args:
            image_path: Path to the image file
            prompt: Question/prompt about the image
            
        Returns:
            Text description of the image
        """
        # Read and encode image to base64
        with open(image_path, "rb") as image_file:
            image_data = base64.b64encode(image_file.read()).decode("utf-8")
        
        payload = {
            "model": self.model,
            "prompt": prompt,
            "images": [image_data],
            "stream": False,
            "options": {
                "temperature": 0.7,
                "num_predict": 500
            }
        }
        
        async with aiohttp.ClientSession() as session:
            async with session.post(
                f"{self.base_url}/api/generate",
                json=payload,
                timeout=aiohttp.ClientTimeout(total=self.timeout)
            ) as response:
                if response.status != 200:
                    error_text = await response.text()
                    raise Exception(f"Ollama image analysis error: {response.status} - {error_text}")
                
                result = await response.json()
                return result.get("response", "")
    
    async def generate_ad_content(
        self,
        product_description: str,
        image_analysis: str,
        ad_type: str = "search"
    ) -> Dict[str, Any]:
        """
        Generate ad content based on product description and image analysis.
        
        Args:
            product_description: Description of the product
            image_analysis: Analysis results from image
            ad_type: Type of ad (search, display, etc.)
            
        Returns:
            Dictionary with generated ad content
        """
        system_prompt = f"""You are an expert Google Ads copywriter. 
Generate compelling ad content based on the product information provided.
Create {ad_type} ads that are engaging, relevant, and follow Google Ads best practices.

Return your response as a JSON object with the following structure:
{{
    "headlines": ["headline1", "headline2", "headline3", "headline4", "headline5"],
    "descriptions": ["description1", "description2"],
    "keywords": ["keyword1", "keyword2", "keyword3", "keyword4", "keyword5"],
    "tags": ["tag1", "tag2", "tag3"],
    "call_to_action": " CTA text"
}}

Requirements:
- Headlines: 30 characters max each
- Descriptions: 90 characters max each
- Keywords: Relevant search terms
- Tags: Product categories and attributes
- CTA: Compelling call to action
"""
        
        user_prompt = f"""Product Description:
{product_description}

Image Analysis:
{image_analysis}

Generate the ad content now:"""
        
        response = await self.generate_text(
            prompt=user_prompt,
            system_prompt=system_prompt,
            temperature=0.8,
            max_tokens=800
        )
        
        # Parse JSON response
        try:
            # Find JSON in response (in case there's any extra text)
            start_idx = response.find("{")
            end_idx = response.rfind("}") + 1
            if start_idx >= 0 and end_idx > start_idx:
                json_str = response[start_idx:end_idx]
                return json.loads(json_str)
        except json.JSONDecodeError:
            pass
        
        # Fallback: return raw response
        return {
            "headlines": [],
            "descriptions": [response],
            "keywords": [],
            "tags": [],
            "call_to_action": "",
            "raw_response": response
        }
    
    async def check_health(self) -> bool:
        """
        Check if Ollama service is running and accessible.
        
        Returns:
            True if service is healthy, False otherwise
        """
        try:
            async with aiohttp.ClientSession() as session:
                async with session.get(
                    f"{self.base_url}/api/tags",
                    timeout=aiohttp.ClientTimeout(total=5)
                ) as response:
                    return response.status == 200
        except Exception:
            return False
    
    def list_models(self) -> List[Dict[str, Any]]:
        """
        List available models in Ollama.
        
        Returns:
            List of available models
        """
        # Note: This is a sync wrapper for the async method
        try:
            loop = asyncio.get_event_loop()
            if loop.is_running():
                # If already in async context, create new loop in thread
                import concurrent.futures
                with concurrent.futures.ThreadPoolExecutor() as executor:
                    future = executor.submit(asyncio.run, self._list_models_async())
                    return future.result()
            else:
                return loop.run_until_complete(self._list_models_async())
        except Exception:
            return []
    
    async def _list_models_async(self) -> List[Dict[str, Any]]:
        """Internal async method to list models."""
        try:
            async with aiohttp.ClientSession() as session:
                async with session.get(
                    f"{self.base_url}/api/tags",
                    timeout=aiohttp.ClientTimeout(total=5)
                ) as response:
                    if response.status == 200:
                        data = await response.json()
                        return data.get("models", [])
                    return []
        except Exception:
            return []
