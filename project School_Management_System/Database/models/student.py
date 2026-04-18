from sqlalchemy import Column, Integer, ForeignKey
from sqlalchemy.orm import relationship
from . import Base

class Student(Base):
    __tablename__ = "students"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"), unique=True, nullable=False)
    # Additional fields can be added here, e.g., student_id, grade, etc.

    user = relationship("User", back_populates="student")
    enrollments = relationship("Enrollment", back_populates="student")
    attendance = relationship("Attendance", back_populates="student")
    results = relationship("Result", back_populates="student")