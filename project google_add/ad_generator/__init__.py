"""
Agentic AI Google Ads Generator

A backend service that generates Google Ads campaigns from product descriptions
and images using local AI models (Ollama) and Google Ads API integration.
"""

from .ollama_client import OllamaClient
from .image_analyzer import ImageAnalyzer
from .ad_generator import AdGenerator
from .google_ads_client import GoogleAdsClient

__version__ = "0.1.0"
__all__ = [
    "OllamaClient",
    "ImageAnalyzer", 
    "AdGenerator",
    "GoogleAdsClient",
]
