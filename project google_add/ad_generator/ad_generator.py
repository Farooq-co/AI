"""
Ad Generator

Main module for generating Google Ads content using AI.
Combines image analysis and text generation to create ad campaigns.
"""

from typing import List, Dict, Any, Optional
import os
from .ollama_client import OllamaClient
from .image_analyzer import ImageAnalyzer


class AdGenerator:
    """
    Main ad generation engine.
    
    Generates Google Ads content (headlines, descriptions, keywords, tags)
    from product descriptions and images using AI.
    """
    
    def __init__(
        self,
        ollama_url: str = "http://localhost:11434",
        model: str = "llava",
        text_model: str = "llama3.2:1b"
    ):
        """
        Initialize the ad generator.
        
        Args:
            ollama_url: URL for Ollama API
            model: Vision model for image analysis (default: llava)
            text_model: Fast text model for text generation (default: llama3.2:1b)
        """
        self.ollama_client = OllamaClient(
            base_url=ollama_url,
            model=model,
            text_model=text_model
        )
        self.image_analyzer = ImageAnalyzer(self.ollama_client)
    
    async def generate_ads(
        self,
        product_description: str,
        image_paths: Optional[List[str]] = None,
        ad_type: str = "search",
        campaign_name: Optional[str] = None,
        target_audience: Optional[str] = None,
        business_name: Optional[str] = None
    ) -> Dict[str, Any]:
        """
        Generate complete ad campaign content.
        
        Args:
            product_description: Description of the product/service
            image_paths: List of paths to product images
            ad_type: Type of ad (search, display, shopping)
            campaign_name: Name for the campaign
            target_audience: Target audience description
            business_name: Name of the business
            
        Returns:
            Dictionary with generated ad content
        """
        # Analyze images if provided
        image_analysis = ""
        if image_paths and len(image_paths) > 0:
            # Validate and filter existing images
            valid_images = []
            for path in image_paths:
                if os.path.exists(path):
                    validation = self.image_analyzer.validate_image(path)
                    if validation["valid"]:
                        valid_images.append(path)
            
            if valid_images:
                # Analyze all valid images
                analyses = await self.image_analyzer.analyze_multiple_images(
                    valid_images,
                    analysis_type="product"
                )
                
                # Generate summary
                image_analysis = await self.image_analyzer.generate_image_summary(analyses)
        
        # Enhance description with image analysis
        enhanced_description = product_description
        if image_analysis:
            enhanced_description += f"\n\nVisual Features: {image_analysis}"
        
        # Generate ad content using Ollama
        ad_content = await self.ollama_client.generate_ad_content(
            product_description=enhanced_description,
            image_analysis=image_analysis,
            ad_type=ad_type
        )
        
        # Build campaign structure
        campaign = {
            "campaign_name": campaign_name or self._generate_campaign_name(product_description),
            "ad_type": ad_type,
            "business_name": business_name,
            "target_audience": target_audience,
            "ad_groups": [
                {
                    "name": f"{campaign_name or 'Ad Group'}_General" if campaign_name else "Ad Group - General",
                    "ads": self._create_ads(ad_content, business_name)
                }
            ],
            "keywords": ad_content.get("keywords", []),
            "tags": ad_content.get("tags", []),
            "raw_ai_response": ad_content
        }
        
        return campaign
    
    async def generate_headlines(
        self,
        product_description: str,
        image_analysis: str = "",
        count: int = 5
    ) -> List[str]:
        """
        Generate just headlines for ads.
        
        Args:
            product_description: Product description
            image_analysis: Image analysis results
            count: Number of headlines to generate
            
        Returns:
            List of headlines
        """
        prompt = f"""Generate {count} compelling Google Ads headlines (30 characters or less each)
for a product with this description:

Product: {product_description}
{image_analysis}

Return only a JSON array of strings, nothing else. Example: ["Headline 1", "Headline 2"]"""
        
        result = await self.ollama_client.generate_text(
            prompt=prompt,
            temperature=0.8,
            max_tokens=200
        )
        
        # Parse response
        try:
            import json
            headlines = json.loads(result)
            if isinstance(headlines, list):
                return headlines[:count]
        except:
            pass
        
        # Fallback
        return [result[:30]] if result else []
    
    async def generate_descriptions(
        self,
        product_description: str,
        image_analysis: str = "",
        count: int = 2
    ) -> List[str]:
        """
        Generate ad descriptions.
        
        Args:
            product_description: Product description
            image_analysis: Image analysis results
            count: Number of descriptions to generate
            
        Returns:
            List of descriptions
        """
        prompt = f"""Generate {count} Google Ads descriptions (90 characters or less each)
for a product with this description:

Product: {product_description}
{image_analysis}

Return only a JSON array of strings, nothing else."""
        
        result = await self.ollama_client.generate_text(
            prompt=prompt,
            temperature=0.8,
            max_tokens=300
        )
        
        # Parse response
        try:
            import json
            descriptions = json.loads(result)
            if isinstance(descriptions, list):
                return descriptions[:count]
        except:
            pass
        
        return [result[:90]] if result else []
    
    async def generate_keywords(
        self,
        product_description: str,
        target_audience: str = "",
        count: int = 10
    ) -> List[str]:
        """
        Generate keywords for ad targeting.
        
        Args:
            product_description: Product description
            target_audience: Target audience
            count: Number of keywords
            
        Returns:
            List of keywords
        """
        prompt = f"""Generate {count} relevant Google Ads keywords for:

Product: {product_description}
Target Audience: {target_audience}

Provide keywords as a simple comma-separated list, one keyword per line. Just the keywords, no numbering or extra text.
Example:
keyword1
keyword2
keyword3"""
        
        result = await self.ollama_client.generate_text(
            prompt=prompt,
            temperature=0.7,
            max_tokens=300
        )
        
        # Parse the response - try multiple approaches
        keywords = []
        
        # Try splitting by newlines first
        lines = result.strip().split('\n')
        for line in lines:
            line = line.strip().strip('0123456789.')  # Remove numbering if any
            if line and len(line) < 50:  # Filter out long lines
                keywords.append(line)
        
        # If that didn't work, try comma separation
        if not keywords:
            parts = result.split(',')
            for part in parts:
                cleaned = part.strip().strip('0123456789.[]"')
                if cleaned and len(cleaned) < 50:
                    keywords.append(cleaned)
        
        # If still empty, return the raw result as fallback
        if not keywords and result.strip():
            keywords = [result.strip()[:50]]
        
        return keywords[:count]
    
    async def generate_tags(
        self,
        product_description: str,
        image_analysis: str = "",
        count: int = 10
    ) -> List[str]:
        """
        Generate tags/labels for the product.
        
        Args:
            product_description: Product description
            image_analysis: Image analysis
            count: Number of tags
            
        Returns:
            List of tags
        """
        prompt = f"""Generate {count} relevant tags/categories for this product:

Product: {product_description}
Visual Features: {image_analysis}

Return only a JSON array of strings."""
        
        result = await self.ollama_client.generate_text(
            prompt=prompt,
            temperature=0.7,
            max_tokens=150
        )
        
        try:
            import json
            tags = json.loads(result)
            if isinstance(tags, list):
                return tags[:count]
        except:
            pass
        
        return []
    
    def _create_ads(
        self,
        ad_content: Dict[str, Any],
        business_name: Optional[str] = None
    ) -> List[Dict[str, Any]]:
        """
        Create ad objects from generated content.
        
        Args:
            ad_content: Generated ad content
            business_name: Business name
            
        Returns:
            List of ad dictionaries
        """
        headlines = ad_content.get("headlines", [])
        descriptions = ad_content.get("descriptions", [])
        
        ads = []
        
        # Create combinations of headlines and descriptions
        for i in range(min(len(headlines), 3)):
            ad = {
                "headline_1": headlines[i] if i < len(headlines) else "",
                "headline_2": headlines[i + 1] if i + 1 < len(headlines) else "",
                "headline_3": headlines[i + 2] if i + 2 < len(headlines) else "",
                "description_1": descriptions[0] if len(descriptions) > 0 else "",
                "description_2": descriptions[1] if len(descriptions) > 1 else "",
                "path_1": business_name.lower().replace(" ", "-") if business_name else "product",
                "path_2": "ad"
            }
            ads.append(ad)
        
        return ads
    
    def _generate_campaign_name(self, product_description: str) -> str:
        """
        Generate a campaign name from product description.
        
        Args:
            product_description: Product description
            
        Returns:
            Campaign name
        """
        # Use first few words
        words = product_description.split()[:3]
        campaign_name = "_".join(words).lower().replace(",", "").replace(".", "")
        return f"campaign_{campaign_name}"
    
    async def check_system_ready(self) -> Dict[str, Any]:
        """
        Check if the system is ready for ad generation.
        
        Returns:
            Status dictionary
        """
        # Check Ollama
        ollama_ready = await self.ollama_client.check_health()
        
        return {
            "ollama_connected": ollama_ready,
            "ready": ollama_ready,
            "model": self.ollama_client.model
        }
