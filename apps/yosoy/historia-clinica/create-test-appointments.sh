#!/bin/bash

# Obtener token
TOKEN=$(curl -s -X POST "http://localhost:3001/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}' | \
  jq -r '.token')

echo "Token obtenido: ${TOKEN:0:50}..."

# Crear cita de prueba
curl -X POST "http://localhost:3001/api/citas" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "pacienteId": 1,
    "paciente": "Juan Pérez",
    "fecha": "2025-11-04",
    "hora": "10:00",
    "motivo": "Consulta general",
    "tipo": "virtual",
    "medicoId": 2
  }'

echo -e "\n"

# Crear segunda cita
curl -X POST "http://localhost:3001/api/citas" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "pacienteId": 1,
    "paciente": "María García", 
    "fecha": "2025-11-04",
    "hora": "14:30",
    "motivo": "Control de rutina",
    "tipo": "presencial",
    "medicoId": 2
  }'