from fastapi import FastAPI, HTTPException
from pymongo import MongoClient
from dotenv import load_dotenv
import os

load_dotenv()

app = FastAPI()

# helper to create a connected client; called during startup

def make_client():
    uri = os.getenv("DB_URI")
    if not uri:
        raise HTTPException(status_code=500, detail="DB_URI not configured")
    try:
        client = MongoClient(uri, serverSelectionTimeoutMS=5000)
        # perform quick ping to force resolution/connect
        client.admin.command("ping")
        return client
    except Exception as e:
        print(f"Error connecting to the database: {e}")
        raise HTTPException(status_code=500, detail="Database connection error")


@app.on_event("startup")
def startup():
    app.mongodb_client = make_client()
    app.db = app.mongodb_client["fastapidb"]


@app.on_event("shutdown")
def shutdown():
    if hasattr(app, "mongodb_client"):
        app.mongodb_client.close()

@app.get("/items/{item_id}")
def read_item(item_id: int):
    item = app.db.items.find_one({"item_id": item_id})
    if item:
        return {"item_id": item["item_id"], "name": item["name"], "description": item["description"]}
    else:
        raise HTTPException(status_code=404, detail="Item not found")