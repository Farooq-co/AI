import asyncio
import sys
sys.path.insert(0, r"F:\AI_and_Data_Science\google_add")

from ad_generator import OllamaClient

async def test():
    client = OllamaClient(base_url="http://localhost:11434", model="llava")
    
    # Check health
    health = await client.check_health()
    print(f"Ollama health: {health}")
    
    # Try to analyze image
    try:
        result = await client.analyze_image(
            image_path=r"C:\Users\HP\Downloads\1.jpg",
            prompt="Describe this image"
        )
        print(f"Result: {result}")
    except Exception as e:
        print(f"Error: {e}")
        import traceback
        traceback.print_exc()

asyncio.run(test())
