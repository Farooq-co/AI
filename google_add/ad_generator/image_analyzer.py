"""
Image Analyzer

Handles image processing and analysis using Ollama's vision capabilities.
Extracts visual features, text, colors, and objects from product images.
"""

import os
from typing import List, Dict, Any, Optional
from PIL import Image
import io
import base64


class ImageAnalyzer:
    """
    Analyzes product images using vision-capable AI models.
    
    Capabilities:
    - Object detection and description
    - Color extraction
    - Text recognition (OCR-like)
    - Scene understanding
    - Product feature extraction
    """
    
    def __init__(self, ollama_client):
        """
        Initialize the image analyzer.
        
        Args:
            ollama_client: An instance of OllamaClient
        """
        self.ollama_client = ollama_client
    
    async def analyze_single_image(
        self,
        image_path: str,
        analysis_type: str = "comprehensive"
    ) -> Dict[str, Any]:
        """
        Analyze a single image.
        
        Args:
            image_path: Path to the image file
            analysis_type: Type of analysis (comprehensive, quick, product)
            
        Returns:
            Dictionary containing analysis results
        """
        # Validate image exists
        if not os.path.exists(image_path):
            raise FileNotFoundError(f"Image not found: {image_path}")
        
        # Get image info
        image_info = self._get_image_info(image_path)
        
        # Choose analysis prompt based on type
        prompts = {
            "comprehensive": """Analyze this product image comprehensively. 
Describe: 1) All visible objects/products, 2) Colors and visual style, 
3) Any text/logo visible, 4) Setting/background, 5) Product quality/appearance.
Be very detailed and descriptive.""",
            
            "quick": """Quickly describe this image. What product is shown? 
What are the main colors? Any text visible?""",
            
            "product": """Analyze this as a product/ecommerce image.
Focus on: 1) Product type and category, 2) Key features visible, 
3) Packaging if any, 4) Brand elements, 5) Style/aesthetic.
This will be used for advertising purposes."""
        }
        
        prompt = prompts.get(analysis_type, prompts["comprehensive"])
        
        # Analyze with Ollama vision
        analysis = await self.ollama_client.analyze_image(
            image_path=image_path,
            prompt=prompt
        )
        
        # Extract colors
        colors = self._extract_colors(image_path)
        
        return {
            "image_path": image_path,
            "image_info": image_info,
            "analysis": analysis,
            "colors": colors,
            "analysis_type": analysis_type
        }
    
    async def analyze_multiple_images(
        self,
        image_paths: List[str],
        analysis_type: str = "product"
    ) -> List[Dict[str, Any]]:
        """
        Analyze multiple images.
        
        Args:
            image_paths: List of paths to images
            analysis_type: Type of analysis to perform
            
        Returns:
            List of analysis results for each image
        """
        results = []
        for image_path in image_paths:
            try:
                result = await self.analyze_single_image(image_path, analysis_type)
                results.append(result)
            except Exception as e:
                results.append({
                    "image_path": image_path,
                    "error": str(e)
                })
        
        return results
    
    async def generate_image_summary(
        self,
        analyses: List[Dict[str, Any]]
    ) -> str:
        """
        Generate a summary from multiple image analyses.
        
        Args:
            analyses: List of image analysis results
            
        Returns:
            Combined summary text
        """
        if not analyses:
            return "No images to analyze."
        
        # Extract key information
        all_analyses = [a.get("analysis", "") for a in analyses if a.get("analysis")]
        all_colors = []
        for a in analyses:
            if a.get("colors"):
                all_colors.extend(a.get("colors", []))
        
        # Create summary prompt
        prompt = f"""Based on the following image analyses, create a concise summary 
of the product visuals for advertising purposes:

Image Analyses:
{chr(10).join([f"Image {i+1}: {a}" for i, a in enumerate(all_analyses)])}

Colors Found: {', '.join(set(all_colors))}

Provide a 2-3 sentence summary that captures the visual essence of the product."""
        
        summary = await self.ollama_client.generate_text(
            prompt=prompt,
            temperature=0.7,
            max_tokens=200
        )
        
        return summary
    
    def _get_image_info(self, image_path: str) -> Dict[str, Any]:
        """
        Get basic image information.
        
        Args:
            image_path: Path to image
            
        Returns:
            Dictionary with image metadata
        """
        try:
            with Image.open(image_path) as img:
                return {
                    "format": img.format,
                    "mode": img.mode,
                    "size": img.size,
                    "width": img.width,
                    "height": img.height,
                    "aspect_ratio": round(img.width / img.height, 2) if img.height > 0 else 0
                }
        except Exception as e:
            return {"error": str(e)}
    
    def _extract_colors(self, image_path: str, num_colors: int = 5) -> List[str]:
        """
        Extract dominant colors from image.
        
        Args:
            image_path: Path to image
            num_colors: Number of colors to extract
            
        Returns:
            List of color names
        """
        try:
            with Image.open(image_path) as img:
                # Convert to RGB if necessary
                if img.mode != "RGB":
                    img = img.convert("RGB")
                
                # Resize for faster processing
                img = img.resize((100, 100))
                
                # Get pixel data
                pixels = list(img.getdata())
                
                # Simple color quantization
                from collections import Counter
                color_counts = Counter(pixels)
                
                # Get top colors
                top_colors = color_counts.most_common(num_colors)
                
                # Convert to color names
                color_names = []
                for rgb, count in top_colors:
                    color_names.append(self._rgb_to_color_name(rgb))
                
                return color_names
        except Exception:
            return []
    
    def _rgb_to_color_name(self, rgb: tuple) -> str:
        """
        Convert RGB tuple to color name.
        
        Args:
            rgb: (R, G, B) tuple
            
        Returns:
            Color name string
        """
        r, g, b = rgb
        
        # Define color ranges
        colors = {
            "red": (200, 0, 0),
            "green": (0, 200, 0),
            "blue": (0, 0, 200),
            "yellow": (200, 200, 0),
            "cyan": (0, 200, 200),
            "magenta": (200, 0, 200),
            "white": (200, 200, 200),
            "black": (50, 50, 50),
            "gray": (128, 128, 128),
            "orange": (200, 100, 0),
            "pink": (200, 100, 100),
            "purple": (100, 0, 200),
            "brown": (100, 50, 0),
            "beige": (200, 180, 150),
            "navy": (0, 0, 100),
            "teal": (0, 100, 100)
        }
        
        # Find closest color
        min_distance = float("inf")
        closest_color = "unknown"
        
        for color_name, target_rgb in colors.items():
            distance = (
                (r - target_rgb[0]) ** 2 +
                (g - target_rgb[1]) ** 2 +
                (b - target_rgb[2]) ** 2
            ) ** 0.5
            if distance < min_distance:
                min_distance = distance
                closest_color = color_name
        
        return closest_color
    
    def validate_image(self, image_path: str) -> Dict[str, Any]:
        """
        Validate an image file.
        
        Args:
            image_path: Path to image
            
        Returns:
            Validation result dictionary
        """
        result = {
            "valid": False,
            "errors": [],
            "warnings": []
        }
        
        # Check file exists
        if not os.path.exists(image_path):
            result["errors"].append("File does not exist")
            return result
        
        # Check file size
        file_size = os.path.getsize(image_path)
        if file_size > 10 * 1024 * 1024:  # 10MB
            result["warnings"].append("File size > 10MB, may be slow to process")
        
        # Check format
        try:
            with Image.open(image_path) as img:
                if img.format not in ["JPEG", "PNG", "JPG", "WEBP", "GIF"]:
                    result["warnings"].append(f"Uncommon format: {img.format}")
                
                # Check dimensions
                if img.width < 100 or img.height < 100:
                    result["warnings"].append("Image is very small, may not analyze well")
                
                result["valid"] = True
                result["image_info"] = self._get_image_info(image_path)
        except Exception as e:
            result["errors"].append(f"Cannot read image: {str(e)}")
        
        return result
