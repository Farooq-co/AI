# Agentic AI Google Ads Generator - Implementation Plan

## Task List

### Phase 1: Project Setup
- [x] Create `ad_generator/` directory structure
- [x] Add required dependencies to requirements.txt
- [x] Create environment configuration (.env.example)

### Phase 2: Core Modules
- [x] Create `ad_generator/__init__.py` - Package initialization
- [x] Create `ad_generator/ollama_client.py` - Ollama API client
- [x] Create `ad_generator/image_analyzer.py` - Image analysis
- [x] Create `ad_generator/ad_generator.py` - Main ad generation logic
- [x] Create `ad_generator/google_ads_client.py` - Google Ads API

### Phase 3: Models & API
- [x] Create `models.py` - Pydantic models
- [x] Update `main_2.py` - FastAPI endpoints integration

### Phase 4: Documentation
- [ ] Create README.md with setup instructions (optional)

## Dependencies Required
- aiohttp>=3.8.0
- Pillow>=10.0.0
- google-ads>=22.0.0

## Environment Setup Required

### 1. Install Ollama (for local AI)
- Download from: https://ollama.ai
- Pull vision model: `ollama pull llava`

### 2. Install Python Dependencies
```
bash
pip install -r requirements.txt
```

### 3. Configure Environment
```
bash
cp .env.example .env
# Edit .env with your settings
```

### 4. Run the Server
```
bash
python main_2.py
# Or: uvicorn main_2:app --reload --port 8000
```

## API Endpoints

### Health Check
- `GET /` - Root endpoint
- `GET /health` - Health check
- `GET /status` - System status

### Image Analysis
- `POST /analyze/image` - Analyze single image

### Ad Generation
- `POST /generate/headlines` - Generate headlines
- `POST /generate/keywords` - Generate keywords
- `POST /generate/tags` - Generate tags
- `POST /generate/ads` - Generate complete ads

### File Upload
- `POST /upload/image` - Upload image
- `POST /generate/ads-with-upload` - Generate ads with images

### Google Ads
- `POST /campaign/create` - Create campaign
- `POST /campaign/create-from-ai` - Create from AI content
- `GET /campaigns` - List campaigns

### Complete Workflow
- `POST /complete` - Full workflow (generate + create campaign)
