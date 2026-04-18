from sqlalchemy import Column, Integer, ForeignKey, Float, String
from sqlalchemy.orm import relationship
from . import Base

class Result(Base):
    __tablename__ = "results"

    id = Column(Integer, primary_key=True, index=True)
    student_id = Column(Integer, ForeignKey("students.id"), nullable=False)
    course_id = Column(Integer, ForeignKey("courses.id"), nullable=False)
    grade = Column(Float)  # e.g., 85.5
    grade_letter = Column(String(2))  # e.g., "A", "B+"

    student = relationship("Student", back_populates="results")
    course = relationship("Course", back_populates="results")