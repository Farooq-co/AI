from fastapi import FastAPI, Depends, HTTPException
from sqlalchemy.orm import Session
from config.database import SessionLocal, engine
from models.todo_model import Base, Todo
from pydantic import BaseModel
from typing import List
# create the database tables
Base.metadata.create_all(bind=engine)
app = FastAPI()
class TodoCreate(BaseModel):
    title: str
    description: str = None
    completed: bool = False
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
class TodoResponse(BaseModel):
    id: int
    title: str
    description: str = None
    completed: bool
    credit_at: str
    class Config:
        orm_mode = True
@app.post("/todos/", response_model=TodoResponse)
def create_todo(todo: TodoCreate, db: Session = Depends(get_db)):
    db_todo = Todo(title=todo.title, description=todo.description, completed=todo.completed)
    db.add(db_todo)
    db.commit()
    db.refresh(db_todo)
    return db_todo
@app.get("/todos/", response_model=List[TodoResponse])
def read_todos(skip: int = 0, limit: int = 10, db   : Session = Depends(get_db)):
    todos = db.query(Todo).offset(skip).limit(limit).all()
    return todos 
@app.get("/todos/{todo_id}", response_model=TodoResponse)
def read_todo(todo_id: int, db: Session = Depends(get_db)):
    todo = db.query(Todo).filter(Todo.id == todo_id).first()
    if todo is None:
        raise HTTPException(status_code=404, detail="Todo not found")
    return todo
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
@app.delete("/todos/{todo_id}")
def delete_todo(todo_id: int, db: Session = Depends(get_db)):
    db_todo = db.query(Todo).filter(Todo.id == todo_id).first()
    if db_todo is None:
        raise HTTPException(status_code=404, detail="Todo not found")
    db.delete(db_todo)
    db.commit()
    return {"detail": "Todo deleted"}
