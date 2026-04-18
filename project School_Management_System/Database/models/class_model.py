from sqlalchemy import Column, Integer, String, DateTime, ForeignKey
from sqlalchemy.orm import relationship
from . import Base

class Class(Base):
    __tablename__ = "classes"

    id = Column(Integer, primary_key=True, index=True)
    course_id = Column(Integer, ForeignKey("courses.id"), nullable=False)
    name = Column(String, nullable=False)  # e.g., "Lecture 1"
    schedule = Column(DateTime, nullable=False)  # Date and time of the class

    course = relationship("Course", back_populates="classes")
    attendance = relationship("Attendance", back_populates="class_")