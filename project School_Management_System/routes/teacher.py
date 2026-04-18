from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session
from database import get_db
from crud import teacher as crud_teacher
from schemas.teacher import Teacher, TeacherCreate

router = APIRouter()

@router.post("/", response_model=Teacher)
def create_teacher(teacher: TeacherCreate, db: Session = Depends(get_db)):
    return crud_teacher.create_teacher(db=db, teacher=teacher)

@router.get("/{teacher_id}", response_model=Teacher)
def read_teacher(teacher_id: int, db: Session = Depends(get_db)):
    db_teacher = crud_teacher.get_teacher(db, teacher_id=teacher_id)
    if db_teacher is None:
        raise HTTPException(status_code=404, detail="Teacher not found")
    return db_teacher

@router.get("/", response_model=list[Teacher])
def read_teachers(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    teachers = crud_teacher.get_teachers(db, skip=skip, limit=limit)
    return teachers