from sqlalchemy import Column, Integer, String, Boolean, DateTime
from sqlalchemy.ext.declarative import declarative_base
import datetime
Base=declarative_base()
class Todo(Base):
    __tablename__='todos'
    id=Column(Integer,primary_key=True)
    title=Column(String(100),nullable=False)
    description=Column(String(255))
    completed=Column(Boolean,default=False)