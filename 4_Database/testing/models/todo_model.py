from sqlalchemy import Column, Integer, String, Boolean, DateTime,ForeignKey,relationship
from sqlalchemy.ext.declarative import declarative_base
import datetime
Base=declarative_base()

# class address(Base):
#     __tablename__='addresses'
#     id=Column(Integer,primary_key=True) 
#     zip_code=Column(String(50),nullable=False)
#     street=Column(String(100),nullable=False)
#     city=Column(String(255),nullable=False)
#     user_id=Column(Integer,ForeignKey('users.id'), onupdate="CASCADE", ondelete="CASCADE")

class user(Base):
    __tablename__='users'
    id=Column(Integer,primary_key=True) 
    username=Column(String(50),unique=True,nullable=False)
    email=Column(String(100),unique=True,nullable=False)
    password_hash=Column(String(255),nullable=False)

class Todo(Base):
    __tablename__='todos'
    id=Column(Integer,primary_key=True)
    title=Column(String(100),nullable=False)
    description=Column(String(255))
    completed=Column(Boolean,default=False)  
    created_at=Column(DateTime,default=datetime.datetime.utcnow)
    user_id=Column(Integer,ForeignKey('users.id'), onupdate="CASCADE", ondelete="CASCADE")
    user=relationship("user",back_populates="todos") 