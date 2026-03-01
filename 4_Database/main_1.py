from fastapi import FastAPI, HTTPException
from pymongo import MongoClient
from dotenv import load_dotenv
import os

load_dotenv()

app = FastAPI()
def get_db():
    try:
        print(os.getenv("DB_URI"),"Database connection successful")
        return MongoClient(os.getenv("DB_URI"))
        
    except Exception as e:
        print(f"Error connecting to the database: {e}")
        raise HTTPException(status_code=500, detail="Database connection error")
MongoClient=get_db()
db = MongoClient["Fastapidb"]
@app.get("/items/{item_id}")
def read_item(item_id: int):
    item = db.items.find_one({"item_id": item_id})
    if item:
        return {"item_id": item["item_id"], "name": item["name"], "description": item["description"]}
    else:
        raise HTTPException(status_code=404, detail="Item not found")