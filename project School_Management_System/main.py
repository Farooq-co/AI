from fastapi import FastAPI
from routes import student, teacher, course, attendance, result, auth

app = FastAPI(title="School Management System", version="1.0.0")

app.include_router(auth.router, prefix="/auth", tags=["Authentication"])
app.include_router(student.router, prefix="/students", tags=["Students"])
app.include_router(teacher.router, prefix="/teachers", tags=["Teachers"])
app.include_router(course.router, prefix="/courses", tags=["Courses"])
app.include_router(attendance.router, prefix="/attendance", tags=["Attendance"])
app.include_router(result.router, prefix="/results", tags=["Results"])

@app.get("/")
def read_root():
    return {"message": "Welcome to School Management System"}