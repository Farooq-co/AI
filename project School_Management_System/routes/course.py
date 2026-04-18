from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from database import get_db
from crud import course as crud_course
from schemas.course import Course, CourseCreate

router = APIRouter()

@router.post("/", response_model=Course)
def create_course(course: CourseCreate, db: Session = Depends(get_db)):
    return crud_course.create_course(db=db, course=course)

@router.get("/{course_id}", response_model=Course)
def read_course(course_id: int, db: Session = Depends(get_db)):
    db_course = crud_course.get_course(db, course_id=course_id)
    if db_course is None:
        raise HTTPException(status_code=404, detail="Course not found")
    return db_course

@router.get("/", response_model=list[Course])
def read_courses(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    courses = crud_course.get_courses(db, skip=skip, limit=limit)
    return courses