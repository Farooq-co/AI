from fastapi import FastAPI,APIRouter
from pydantic import BaseModel
from fastapi import FastAPI, HTTPException
from fastapi import Path, Query, Body
from fastapi import FastAPI, HTTPException, status
app = FastAPI()
@app.get("/student/")
def read_root():   
     return {"Welcome": "user"}
@app.get("/student/{student_id}")
def read_student(student_id: int):    
     return {"student_id": student_id}
@app.get("/student/{student_id}/course/{course_id}")
def read_course(student_id: int, course_id: int):    
     return {"student_id": student_id, "course_id": course_id}
@app.delete("/student/{student_id}")
def delete_student(student_id: int):    
     return {"student_id": student_id, "message": "Student deleted"}
@app.post("/student/")
def create_student(student_id: int, name: str):    
     return {"student_id": student_id, "name": name, "message": "Student created"}
@app.put("/student/")
def update_student(student_id: int, name: str):    
     return {"student_id": student_id, "name": name, "message": "Student updated"}
@app.patch("/student/")
def partial_update_student(student_id: int, name: str):  
     return {"student_id": student_id, "name": name, "message": "Student partially updated"}
@app.head("/student/")
def head_student():    
     return {"message": "Head for student endpoint"} 

postRouter=APIRouter()
@postRouter.post("/post/")
def create_post(post_id: int, title: str):    
     return {"post_id": post_id, "title": title, "message": "Post created"}
@postRouter.get("/post/{post_id}")
def read_post(post_id: int):    
     return {"post_id": post_id, "message": "Post details"}
@postRouter.delete("/post/{post_id}")
def delete_post(post_id: int):    
     return {"post_id": post_id, "message": "Post deleted"}
@postRouter.put("/post/")
def update_post(post_id: int, title: str):    
     return {"post_id": post_id, "title": title, "message": "Post updated"}
@postRouter.patch("/post/")
def partial_update_post(post_id: int, title: str):  
     return {"post_id": post_id, "title": title, "message": "Post partially updated"}
app.include_router(postRouter,prefix="/posts",tags=["posts"])
