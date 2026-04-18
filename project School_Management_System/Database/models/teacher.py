from sqlalchemy import Column, Integer, ForeignKey, String
from sqlalchemy.orm import relationship
from . import Base

class Teacher(Base):
    __tablename__ = "teachers"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"), unique=True, nullable=False)
    # Additional fields like department, etc.
    name = Column(String, nullable=False)
    email = Column(String, nullable=False)
    user = relationship("User", back_populates="teacher")
    courses = relationship("Course", back_populates="teacher")