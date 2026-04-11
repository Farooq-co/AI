"""
FastAPI Application - Agentic AI Google Ads Generator
"""

import os
from typing import List, Optional
from fastapi import FastAPI, File, UploadFile, HTTPException, Form
from fastapi.middleware.cors import CORSMiddleware

from ad_generator import OllamaClient, ImageAnalyzer, AdGenerator, GoogleAdsClient
from models import (
    GenerateAdsRequest,
    CreateCampaignRequest,
    CreateCampaignFromAIRequest,
    SystemStatusResponse,
    HealthCheckResponse,
    ImageAnalysisRequest,
    HeadlineRequest,
    KeywordsRequest,
    TagsRequest,
)

app = FastAPI(
    title="Agentic AI Google Ads Generator",
    description="Generate Google Ads campaigns from product descriptions and images using AI",
    version="0.1.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

_ollama_client = None
_ad_generator = None
_google_ads_client = None


def get_ollama_client():
    global _ollama_client
    if _ollama_client is None:
        ollama_url = os.getenv("OLLAMA_URL", "http://localhost:11434")
        model = os.getenv("OLLAMA_MODEL", "llava")
        text_model = os.getenv("OLLAMA_TEXT_MODEL", "llama3.2:1b")
        _ollama_client = OllamaClient(base_url=ollama_url, model=model, text_model=text_model)
    return _ollama_client


def get_ad_generator():
    global _ad_generator
    if _ad_generator is None:
        ollama_url = os.getenv("OLLAMA_URL", "http://localhost:11434")
        model = os.getenv("OLLAMA_MODEL", "llava")
        text_model = os.getenv("OLLAMA_TEXT_MODEL", "llama3.2:1b")
        _ad_generator = AdGenerator(ollama_url=ollama_url, model=model, text_model=text_model)
    return _ad_generator


def get_google_ads_client():
    global _google_ads_client
    if _google_ads_client is None:
        mock_mode = os.getenv("GOOGLE_ADS_MOCK_MODE", "true").lower() == "true"
        _google_ads_client = GoogleAdsClient(mock_mode=mock_mode)
    return _google_ads_client


@app.get("/")
def read_root():
    return {"message": "Agentic AI Google Ads Generator API", "version": "0.1.0", "docs": "/docs"}


@app.get("/health", response_model=HealthCheckResponse)
async def health_check():
    ollama_client = get_ollama_client()
    ollama_health = await ollama_client.check_health()
    return HealthCheckResponse(
        status="healthy" if ollama_health else "degraded",
        services={"ollama": ollama_health, "google_ads": True},
        version="0.1.0"
    )


@app.get("/status", response_model=SystemStatusResponse)
async def get_status():
    ollama_client = get_ollama_client()
    google_ads_client = get_google_ads_client()
    ollama_status = await ollama_client.check_health()
    return SystemStatusResponse(
        ollama_connected=ollama_status,
        ready=ollama_status,
        model=ollama_client.model,
        google_ads_mode="mock" if google_ads_client.mock_mode else "live",
        google_ads_status=google_ads_client.get_status()
    )


@app.post("/analyze/image")
async def analyze_image(request: ImageAnalysisRequest):
    analyzer = ImageAnalyzer(get_ollama_client())
    try:
        result = await analyzer.analyze_single_image(
            image_path=request.image_path,
            analysis_type=request.analysis_type
        )
        return result
    except FileNotFoundError as e:
        raise HTTPException(status_code=404, detail=str(e))
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Analysis failed: {str(e)}")


@app.post("/generate/headlines")
async def generate_headlines(request: HeadlineRequest):
    generator = get_ad_generator()
    try:
        headlines = await generator.generate_headlines(
            product_description=request.product_description,
            image_analysis=request.image_analysis,
            count=request.count
        )
        return {"headlines": headlines, "count": len(headlines)}
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Generation failed: {str(e)}")


@app.post("/generate/keywords")
async def generate_keywords(request: KeywordsRequest):
    generator = get_ad_generator()
    try:
        keywords = await generator.generate_keywords(
            product_description=request.product_description,
            target_audience=request.target_audience,
            count=request.count
        )
        return {"keywords": keywords, "count": len(keywords)}
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Generation failed: {str(e)}")


@app.post("/generate/tags")
async def generate_tags(request: TagsRequest):
    generator = get_ad_generator()
    try:
        tags = await generator.generate_tags(
            product_description=request.product_description,
            image_analysis=request.image_analysis,
            count=request.count
        )
        return {"tags": tags, "count": len(tags)}
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Generation failed: {str(e)}")


@app.post("/generate/ads")
async def generate_ads(request: GenerateAdsRequest):
    generator = get_ad_generator()
    try:
        campaign = await generator.generate_ads(
            product_description=request.product_description,
            image_paths=request.image_paths,
            ad_type=request.ad_type.value,
            campaign_name=request.campaign_name,
            target_audience=request.target_audience,
            business_name=request.business_name
        )
        return campaign
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Ad generation failed: {str(e)}")


@app.post("/upload/image")
async def upload_image(file: UploadFile = File(...)):
    upload_dir = "uploads"
    os.makedirs(upload_dir, exist_ok=True)
    file_path = os.path.join(upload_dir, file.filename)
    try:
        content = await file.read()
        with open(file_path, "wb") as f:
            f.write(content)
        return {"filename": file.filename, "path": file_path, "size": len(content)}
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Upload failed: {str(e)}")


@app.post("/generate/ads-with-upload")
async def generate_ads_with_upload(
    product_description: str = Form(...),
    campaign_name: Optional[str] = Form(None),
    target_audience: Optional[str] = Form(None),
    business_name: Optional[str] = Form(None),
    ad_type: str = Form("search"),
    files: List[UploadFile] = File(None)
):
    generator = get_ad_generator()
    image_paths = []
    if files:
        upload_dir = "uploads"
        os.makedirs(upload_dir, exist_ok=True)
        for file in files:
            file_path = os.path.join(upload_dir, file.filename)
            content = await file.read()
            with open(file_path, "wb") as f:
                f.write(content)
            image_paths.append(file_path)
    try:
        campaign = await generator.generate_ads(
            product_description=product_description,
            image_paths=image_paths if image_paths else None,
            ad_type=ad_type,
            campaign_name=campaign_name,
            target_audience=target_audience,
            business_name=business_name
        )
        return campaign
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Ad generation failed: {str(e)}")


@app.post("/campaign/create")
async def create_campaign(request: CreateCampaignRequest):
    google_ads = get_google_ads_client()
    try:
        result = await google_ads.create_campaign(
            campaign_name=request.campaign_name,
            budget=request.budget,
            bidding_strategy=request.bidding_strategy.value,
            networks=request.networks,
            start_date=request.start_date,
            end_date=request.end_date
        )
        return result
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Campaign creation failed: {str(e)}")


@app.post("/campaign/create-from-ai")
async def create_campaign_from_ai(request: CreateCampaignFromAIRequest):
    google_ads = get_google_ads_client()
    try:
        result = await google_ads.create_campaign_from_ai(
            campaign_data=request.campaign_data,
            budget=request.budget,
            final_url=request.final_url
        )
        return result
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Campaign creation failed: {str(e)}")


@app.get("/campaigns")
async def list_campaigns():
    google_ads = get_google_ads_client()
    return {"campaigns": google_ads.get_campaigns(), "count": len(google_ads.get_campaigns())}


@app.post("/complete")
async def complete_ad_workflow(
    product_description: str = Form(...),
    campaign_name: Optional[str] = Form(None),
    target_audience: Optional[str] = Form(None),
    business_name: Optional[str] = Form(None),
    final_url: str = Form(""),
    budget: float = Form(100.0),
    files: List[UploadFile] = File(None)
):
    generator = get_ad_generator()
    google_ads = get_google_ads_client()
    image_paths = []
    if files:
        upload_dir = "uploads"
        os.makedirs(upload_dir, exist_ok=True)
        for file in files:
            file_path = os.path.join(upload_dir, file.filename)
            content = await file.read()
            with open(file_path, "wb") as f:
                f.write(content)
            image_paths.append(file_path)
    try:
        campaign_data = await generator.generate_ads(
            product_description=product_description,
            image_paths=image_paths if image_paths else None,
            campaign_name=campaign_name,
            target_audience=target_audience,
            business_name=business_name
        )
        campaign_result = await google_ads.create_campaign_from_ai(
            campaign_data=campaign_data,
            budget=budget,
            final_url=final_url
        )
        return {"generated_content": campaign_data, "campaign_result": campaign_result}
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Workflow failed: {str(e)}")


if __name__ == "__main__":
    import uvicorn
    port = int(os.getenv("PORT", "8000"))
    host = os.getenv("HOST", "0.0.0.0")
    uvicorn.run(app, host=host, port=port)
