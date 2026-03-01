from fastapi import FastAPI, File, UploadFile, Form
from fastapi.staticfiles import StaticFiles
             
import shutil
import os

UPLOAD_FOLDER = "uploads"
os.makedirs(UPLOAD_FOLDER, exist_ok=True)
app = FastAPI()
app.mount("/static", StaticFiles(directory="uploads"), name="static")
@app.post("/uploadfile/")
async def upload_file(file: UploadFile = File(...)):
    file_location = os.path.join(UPLOAD_FOLDER, file.filename)
    if file_Size := file.spool_max_size:
        if file_Size > 10 * 1024 * 1024:  # Limit to 10 MB
            return {"error": "File size exceeds the limit of 10 MB"}
        else:
            with open(file_location, "wb") as buffer:
                shutil.copyfileobj(file.file, buffer)
    file_url = f"/static/{file.filename}"
    return {"filename": file.filename, "url": f"/static/{file.filename}"}

@app.post("/uploadfiles/")
async def create_upload_files(files: list[UploadFile] = File(...)):
    file_urls = []
    for file in files:
        file_location = os.path.join(UPLOAD_FOLDER, file.filename)
        with open(file_location, "wb") as buffer:
            shutil.copyfileobj(file.file, buffer)
        file_urls.append(f"/static/{file.filename}")
    return {"filenames": [file.filename for file in files], "urls": file_urls}
