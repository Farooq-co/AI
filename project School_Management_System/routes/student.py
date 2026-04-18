from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from database import get_db
from crud import student as crud_student
from schemas.student import Student, StudentCreate

router = APIRouter()

@router.post("/", response_model=Student)
def create_student(student: StudentCreate, db: Session = Depends(get_db)):
    return crud_student.create_student(db=db, student=student)

@router.get("/{student_id}", response_model=Student)
def read_student(student_id: int, db: Session = Depends(get_db)):
    db_student = crud_student.get_student(db, student_id=student_id)
    if db_student is None:
        raise HTTPException(status_code=404, detail="Student not found")
    return db_student

@router.get("/", response_model=list[Student])
def read_students(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    students = crud_student.get_students(db, skip=skip, limit=limit)
    return students