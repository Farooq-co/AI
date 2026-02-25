from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from fastapi.responses import JSONResponse
from fastapi.exceptions import RequestValidationError
from fastapi import Request

class CustomHTTPException(HTTPException):
    def __init__(self, status_code: int, detail: str):
        super().__init__(status_code=status_code, detail=detail)
app = FastAPI()
@app.exception_handler(RequestValidationError)
async def custom_http_exception_handler(request, exc: RequestValidationError):
    error_details = exc.errors()
    for error in error_details:
        if error["type"] == "value_error.missing":
            return JSONResponse(
                status_code=400,
                content={"message": "Missing required field", 
                         "details": error},
            )
    return JSONResponse(
        status_code=400,
        content={
            "message": "Validation error", 
            "details": exc.errors(),
            "error": str(exc)
            },
    )

@app.get("/uploadfile/")
async def upload_file(file: str = None):
    if not file:
        raise CustomHTTPException(status_code=400, detail="No file provided")
    else:
        return {"filename": file}

class Item(BaseModel):
    name: str
    description: str = None
    price: float
    tax: float = None   
@app.get("/items/{item_id}")
async def read_item(item_id: int, q: str, Item: Item):
    if item_id == 0:
        raise CustomHTTPException(status_code=400, detail="Item ID must be a positive integer", info={"item_id": item_id}, headers={"X-Error": "There goes my error"}, media_type="application/json", background=None)
    return {"item_id": item_id}

@app.get("/cause_error")
async def cause_error():
    raise CustomHTTPException(status_code=500, detail="This is a custom error message")
@app.exception_handler(CustomHTTPException)
async def custom_exception_handler(request: Request, exc: CustomHTTPException):
    return JSONResponse(
        status_code=exc.status_code,
        content={"message": exc.detail},
    )