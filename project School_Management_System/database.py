from Database.config.database import SessionLocal
from Database.models import Base

# Dependency to get DB session
def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()