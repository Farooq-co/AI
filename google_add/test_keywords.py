import asyncio
import sys
sys.path.insert(0, r"F:\AI_and_Data_Science\google_add")

from ad_generator import AdGenerator

async def test():
    generator = AdGenerator(ollama_url="http://localhost:11434", model="llava")
    
    # Check health
    status = await generator.check_system_ready()
    print(f"System status: {status}")
    
    # Try to generate keywords
    try:
        keywords = await generator.generate_keywords(
            product_description="watercolors which is being used for the painting",
            target_audience="10 years old kid",
            count=10
        )
        print(f"Keywords: {keywords}")
    except Exception as e:
        print(f"Error: {e}")
        import traceback
        traceback.print_exc()

asyncio.run(test())
