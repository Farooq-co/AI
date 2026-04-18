from sqlalchemy.ext.declarative import declarative_base

Base = declarative_base()

from .user import User
from .student import Student
from .teacher import Teacher
from .course import Course
from .class_model import Class
from .enrollment import Enrollment
from .attendance import Attendance
from .result import Result