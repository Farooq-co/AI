from datetime import datetime
from fastapi import FastAPI, Depends, HTTPException
from sqlalchemy.orm import Session
from config.database import SessionLocal, engine
from models.todo_model import Base, Todo, User
from pydantic import BaseModel
from typing import List
# create the database tables
import hashlib
from passlib.context import CryptContext

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

def hash_password(password: str):
    # ensure safe length BEFORE bcrypt
    password = hashlib.sha256(password.encode()).hexdigest()
    return pwd_context.hash(password)
class TodoCreate(BaseModel):
    title: str
    description: str = None
    completed: bool = False

app = FastAPI()
class TodoUpdate(BaseModel):
    title: str = None
    description: str = None
    completed: bool = None


# Dependency to get DB session
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()


class TodoResponse(TodoCreate):
    id: int
    title: str
    description: str = None
    completed: bool
    created_at: datetime
    class Config:
        orm_mode = True


class UserCreate(BaseModel):
    username: str
    email: str
    password: str


class UserResponse(UserCreate):
    id: int
    username: str
    email: str
    created_at: datetime
    class Config:
        orm_mode = True


class Userupdate(BaseModel):
    username: str = None
    email: str = None
    password: str = None


# API endpoint to create a new todo item
@app.post("/todos/{User_id}", response_model=TodoResponse)
def create_todo(User_id: int, todo: TodoCreate, db: Session = Depends(get_db)):
    db_todo = Todo(title=todo.title, description=todo.description, completed=todo.completed, user_id=User_id)
    db.add(db_todo)
    db.commit()
    db.refresh(db_todo)
    return db_todo

# API endpoint to create a new user
@app.post("/create_User/")
def create_user(user: UserCreate, db: Session = Depends(get_db)):
    db_user = User(
        username=user.username,
        email=user.email,
        password_hash=hash_password(user.password)   # also fix this
    )
    db.add(db_user)
    db.commit()
    db.refresh(db_user)
    return db_user


# API endpoint to update an existing user
@app.put("/update_User/{User_id}")
def update_user(user_id: int, user: Userupdate, db: Session = Depends(get_db)):
    db_user = db.query(User).filter(User.id == user_id).first()
    if db_user is None:
        raise HTTPException(status_code=404, detail="User not found")
    if User.username is not None:
        db_user.username = User.username
    if User.email is not None:
        db_user.email = User.email
    if User.password is not None:
        db_user.password = User.password
    db.commit()
    db.refresh(db_user)
    return db_user


# API endpoint to read all todo items with pagination
@app.get("/todos/", response_model=List[TodoResponse])
def read_todos(skip: int = 0, limit: int = 10, db   : Session = Depends(get_db)):
    try:
        todos = db.query(Todo).offset(skip).limit(limit).all()
        return todos
    except Exception as e:
        print(f"Error reading todos: {e}")


# API endpoint to read all users with pagination
@app.get("/users/", response_model=List[UserResponse])
def read_users(skip: int = 0, limit: int = 10, db: Session = Depends(get_db)):
    try:
        users = db.query(User).offset(skip).limit(limit).all()
        return users
    except Exception as e:
        print(f"Error reading users: {e}")


# API endpoint to read a specific todo item by ID
@app.get("/todos/{todo_id}", response_model=TodoResponse)
def read_todo(todo_id: int, db: Session = Depends(get_db)):
    db_todo = db.query(Todo).filter(Todo.id == todo_id).first()
    if db_todo is None:
        raise HTTPException(status_code=404, detail="Todo not found")
    return db_todo


# API endpoint to read a specific user by ID
@app.get("/users/{user_id}", response_model=UserResponse)
def read_user(user_id: int, db: Session = Depends(get_db)):
    db_user = db.query(User).filter(User.id == user_id).first()
    if db_user is None:
        raise HTTPException(status_code=404, detail="User not found")
    return db_user


# API endpoint to update an existing todo item
@app.put("/todos/{todo_id}", response_model=TodoResponse)
def update_todo(todo_id: int, todo: TodoUpdate, db: Session = Depends
(get_db)):
    db_todo = db.query(Todo).filter(Todo.id == todo_id).first()
    if db_todo is None:
        raise HTTPException(status_code=404, detail="Todo not found")
    if todo.title is not None:
        db_todo.title = todo.title
    if todo.description is not None:
        db_todo.description = todo.description
    if todo.completed is not None:
        db_todo.completed = todo.completed
    db.commit()
    db.refresh(db_todo)
    return db_todo


# API endpoint to delete a todo item by ID
@app.delete("/todos/{todo_id}")
def delete_todo(todo_id: int, db: Session = Depends(get_db)):
    db_todo = db.query(Todo).filter(Todo.id == todo_id).first()
    if db_todo is None:
        raise HTTPException(status_code=404, detail="Todo not found")
    db.delete(db_todo)
    db.commit()
    return {"detail": "Todo deleted"}
@app.delete("/users/{user_id}")
def delete_user(user_id: int, db: Session = Depends(get_db)):
    db_user = db.query(User).filter(User.id == user_id).first()
    if db_user is None:
        raise HTTPException(status_code=404, detail="User not found")
    db.delete(db_user)
    db.commit()
    return {"detail": "User deleted"}