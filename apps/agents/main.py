from fastapi import FastAPI

app = FastAPI()

@app.get("/")
def root():
    return {"message": "Agente IA de Authentic activo"}
