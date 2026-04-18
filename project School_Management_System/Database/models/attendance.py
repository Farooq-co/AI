from sqlalchemy import Column, Integer, ForeignKey, DateTime, Boolean
from sqlalchemy.orm import relationship
from datetime import datetime
from . import Base

class Attendance(Base):
    __tablename__ = "attendance"

    id = Column(Integer, primary_key=True, index=True)
    class_id = Column(Integer, ForeignKey("classes.id"), nullable=False)
    student_id = Column(Integer, ForeignKey("students.id"), nullable=False)
    date = Column(DateTime, default=datetime.utcnow)
    present = Column(Boolean, default=False)

    class_ = relationship("Class", back_populates="attendance")
    student = relationship("Student", back_populates="attendance")