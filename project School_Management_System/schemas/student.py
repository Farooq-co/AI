from pydantic import BaseModel
from typing import Optional

class StudentBase(BaseModel):
    name: str
    email: str

class StudentCreate(StudentBase):
    password: str

class Student(StudentBase):
    id: int
    user_id: int

    class Config:
        from_attributes = True