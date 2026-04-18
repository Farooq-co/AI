from pydantic import BaseModel
from typing import Optional

class TeacherBase(BaseModel):
    name: str
    email: str

class TeacherCreate(TeacherBase):
    password: str

class Teacher(TeacherBase):
    id: int
    user_id: int

    class Config:
        from_attributes = True