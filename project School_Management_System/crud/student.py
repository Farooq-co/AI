from sqlalchemy.orm import Session
from Database.models import Student, User
from schemas.student import StudentCreate
from utils.hash import hash_password

def get_student(db: Session, student_id: int):
    return db.query(Student).filter(Student.id == student_id).first()

def get_students(db: Session, skip: int = 0, limit: int = 100):
    return db.query(Student).offset(skip).limit(limit).all()

def create_student(db: Session, student: StudentCreate):
    hashed_password = hash_password(student.password)
    db_user = User(name=student.name, email=student.email, password=hashed_password, role="student")
    db.add(db_user)
    db.commit()
    db.refresh(db_user)
    db_student = Student(user_id=db_user.id)
    db.add(db_student)
    db.commit()
    db.refresh(db_student)
    return db_student