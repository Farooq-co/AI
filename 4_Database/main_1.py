from fastapi import FastAPI, HTTPException
from pymongo import MongoClient
from dotenv import load_dotenv
import os

load_dotenv()

app = FastAPI()

def get_db():
    uri = os.getenv("DB_URI")
    if not uri:
        raise HTTPException(status_code=500, detail="DB_URI not configured")
    try:
        client = MongoClient(uri, serverSelectionTimeoutMS=5000)
        client.admin.command("ping")
        print(uri, "Database connection successful")
        return client
    except Exception as e:
        print(f"Error connecting to the database: {e}")
        raise HTTPException(status_code=500, detail="Database connection error")

@app.on_event("startup")
def startup():
    app.mongodb_client = get_db()
    app.db = app.mongodb_client["Fastapidb"]

@app.on_event("shutdown")
def shutdown():
    if hasattr(app, "mongodb_client"):
        app.mongodb_client.close()
@app.get("/")
def read_root():
    return {"message": "Welcome to the FastAPI MongoDB example!"}
@app.get("/todos")
def read_todos():
    try:
        todos = list(app.db.todos.find())
        # Convert ObjectId to string for JSON serialization
        for todo in todos:
            if "_id" in todo:
                todo["_id"] = str(todo["_id"])
        return {"data": todos,
                "message": "Todos fetched successfully",
                "status_code": "success"
                }
    except Exception as e:
        print(f"Error fetching todos: {e}")
        return {
                "data": [],
                "error": "Error fetching todos",
                "details": str(e),
                "message": "An error occurred while fetching todos from the database.",
                "status_code": 500
                }
