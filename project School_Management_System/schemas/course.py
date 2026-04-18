from pydantic import BaseModel
from typing import Optional

class CourseBase(BaseModel):
    name: str
    description: Optional[str] = None

class CourseCreate(CourseBase):
    teacher_id: int

class Course(CourseBase):
    id: int
    teacher_id: int

    class Config:
        from_attributes = True