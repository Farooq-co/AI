# Import required SQLAlchemy components for database table creation
from sqlalchemy import Column, Integer, String, Boolean, DateTime, ForeignKey
from sqlalchemy.ext.declarative import declarative_base
import datetime

# Create a base class for all models (tables)
Base = declarative_base()


# User table model
class User(Base):
    __tablename__ = 'users'  # Table name in database

    # Primary key (unique ID for each user)
    id = Column(Integer, primary_key=True)

    # Username field (must be unique and not empty)
    username = Column(String(50), unique=True, nullable=False)

    # Email field (must be unique and not empty)
    email = Column(String(100), unique=True, nullable=False)

    # Password hash field (stored securely, not plain password)
    password_hash = Column(String(255), nullable=False)


# Todo table model
class Todo(Base):
    __tablename__ = 'todos'  # Table name in database

    # Primary key (unique ID for each todo item)
    id = Column(Integer, primary_key=True)

    # Title of the todo task
    title = Column(String(100), nullable=False)

    # Optional description of the task
    description = Column(String(255))

    # Status of task (True = completed, False = not completed)
    completed = Column(Boolean, default=False)

    # Timestamp when todo is created (auto set current time)
    created_at = Column(DateTime, default=datetime.datetime.utcnow)

    # Foreign key linking todo to a specific user
    user_id = Column(Integer, ForeignKey('users.id'), nullable=False)