from typing import Annotated    
from fastapi import FastAPI, HTTPException
from sqlmodel import Field, SQLModel, create_engine, Session, select