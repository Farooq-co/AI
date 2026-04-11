"""
Pydantic Models

Request and response models for the Ad Generator API.
"""

from typing import List, Optional, Dict, Any
from pydantic import BaseModel, Field
from enum import Enum


class AdType(str, Enum):
    """Types of Google Ads."""
    SEARCH = "search"
    DISPLAY = "display"
    SHOPPING = "shopping"
    VIDEO = "video"


class BiddingStrategy(str, Enum):
    """Bidding strategies for campaigns."""
    CLICK = "CLICK"  # CPC
    CONVERSION = "CONVERSION"
    MANUAL_CPC = "MANUAL_CPC"
    TARGET_CPA = "TARGET_CPA"
    TARGET_ROAS = "TARGET_ROAS"


# Request Models
class ImageAnalysisRequest(BaseModel):
    """Request model for image analysis."""
    image_path: str = Field(..., description="Path to the image file")
    analysis_type: str = Field(
        default="product",
        description="Type of analysis: comprehensive, quick, or product"
    )


class HeadlineRequest(BaseModel):
    """Request model for headline generation."""
    product_description: str = Field(..., description="Description of the product")
    image_analysis: str = Field(default="", description="Image analysis results")
    count: int = Field(default=5, ge=1, le=15, description="Number of headlines to generate")


class KeywordsRequest(BaseModel):
    """Request model for keyword generation."""
    product_description: str = Field(..., description="Description of the product")
    target_audience: str = Field(default="", description="Target audience description")
    count: int = Field(default=10, ge=1, le=50, description="Number of keywords")


class TagsRequest(BaseModel):
    """Request model for tag generation."""
    product_description: str = Field(..., description="Description of the product")
    image_analysis: str = Field(default="", description="Image analysis results")
    count: int = Field(default=10, ge=1, le=30, description="Number of tags")


class GenerateAdsRequest(BaseModel):
    """Request model for complete ad generation."""
    product_description: str = Field(..., description="Description of the product/service")
    image_paths: Optional[List[str]] = Field(
        default=None,
        description="List of paths to product images"
    )
    ad_type: AdType = Field(default=AdType.SEARCH, description="Type of ad to generate")
    campaign_name: Optional[str] = Field(
        default=None,
        description="Name for the campaign"
    )
    target_audience: Optional[str] = Field(
        default=None,
        description="Target audience description"
    )
    business_name: Optional[str] = Field(
        default=None,
        description="Name of the business"
    )
    final_url: Optional[str] = Field(
        default=None,
        description="Landing page URL for the ads"
    )


class CreateCampaignRequest(BaseModel):
    """Request model for creating a Google Ads campaign."""
    campaign_name: str = Field(..., description="Name for the campaign")
    budget: float = Field(default=100.0, ge=0.01, description="Daily budget")
    bidding_strategy: BiddingStrategy = Field(
        default=BiddingStrategy.CLICK,
        description="Bidding strategy"
    )
    networks: List[str] = Field(
        default=["SEARCH"],
        description="Networks to target: SEARCH, DISPLAY, YOUTUBE"
    )
    start_date: Optional[str] = Field(
        default=None,
        description="Start date (YYYY-MM-DD)"
    )
    end_date: Optional[str] = Field(
        default=None,
        description="End date (YYYY-MM-DD)"
    )


class CreateCampaignFromAIRequest(BaseModel):
    """Request model for creating campaign from AI-generated content."""
    campaign_data: Dict[str, Any] = Field(
        ...,
        description="Campaign data from AdGenerator"
    )
    budget: float = Field(default=100.0, ge=0.01, description="Campaign budget")
    final_url: str = Field(default="", description="Landing page URL")


# Response Models
class ImageAnalysisResponse(BaseModel):
    """Response model for image analysis."""
    image_path: str
    analysis: str
    colors: List[str]
    image_info: Dict[str, Any]
    analysis_type: str


class HeadlineResponse(BaseModel):
    """Response model for headline generation."""
    headlines: List[str]
    count: int


class KeywordsResponse(BaseModel):
    """Response model for keyword generation."""
    keywords: List[str]
    count: int


class TagsResponse(BaseModel):
    """Response model for tag generation."""
    tags: List[str]
    count: int


class GeneratedAd(BaseModel):
    """Model for a single generated ad."""
    headline_1: str
    headline_2: str
    headline_3: str
    description_1: str
    description_2: str
    path_1: str
    path_2: str


class AdGroupContent(BaseModel):
    """Model for an ad group."""
    name: str
    ads: List[GeneratedAd]


class CampaignContent(BaseModel):
    """Model for generated campaign content."""
    campaign_name: str
    ad_type: str
    business_name: Optional[str]
    target_audience: Optional[str]
    ad_groups: List[AdGroupContent]
    keywords: List[str]
    tags: List[str]


class GenerateAdsResponse(BaseModel):
    """Response model for complete ad generation."""
    campaign: CampaignContent
    image_analyses: Optional[List[Dict[str, Any]]] = None


class CampaignResponse(BaseModel):
    """Response model for campaign creation."""
    success: bool
    campaign: Optional[Dict[str, Any]] = None
    mode: str = "mock"
    error: Optional[str] = None


class CampaignFromAIResponse(BaseModel):
    """Response model for campaign creation from AI content."""
    status: str
    campaigns: List[Dict[str, Any]]
    ad_groups: List[Dict[str, Any]]
    ads: List[Dict[str, Any]]
    errors: List[str]


class SystemStatusResponse(BaseModel):
    """Response model for system status check."""
    ollama_connected: bool
    ready: bool
    model: str
    google_ads_mode: str
    google_ads_status: Dict[str, Any]


class HealthCheckResponse(BaseModel):
    """Response model for health check."""
    status: str
    services: Dict[str, bool]
    version: str


# Export all models for easy imports
__all__ = [
    # Enums
    "AdType",
    "BiddingStrategy",
    # Request models
    "ImageAnalysisRequest",
    "HeadlineRequest",
    "KeywordsRequest",
    "TagsRequest",
    "GenerateAdsRequest",
    "CreateCampaignRequest",
    "CreateCampaignFromAIRequest",
    # Response models
    "ImageAnalysisResponse",
    "HeadlineResponse",
    "KeywordsResponse",
    "TagsResponse",
    "GeneratedAd",
    "AdGroupContent",
    "CampaignContent",
    "GenerateAdsResponse",
    "CampaignResponse",
    "CampaignFromAIResponse",
    "SystemStatusResponse",
    "HealthCheckResponse",
]
