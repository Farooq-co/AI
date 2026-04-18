from sqlalchemy.orm import Session
from Database.models import Teacher, User
from schemas.teacher import TeacherCreate
from utils.hash import hash_password

def get_teacher(db: Session, teacher_id: int):
    return db.query(Teacher).filter(Teacher.id == teacher_id).first()

def get_teachers(db: Session, skip: int = 0, limit: int = 100):
    return db.query(Teacher).offset(skip).limit(limit).all()

def create_teacher(db: Session, teacher: TeacherCreate):
    hashed_password = hash_password(teacher.password)
    db_user = User(name=teacher.name, email=teacher.email, password=hashed_password, role="teacher")
    db.add(db_user)
    db.commit()
    db.refresh(db_user)
    db_teacher = Teacher(user_id=db_user.id)
    db.add(db_teacher)
    db.commit()
    db.refresh(db_teacher)
    return db_teacher