#!/bin/bash

# Script para probar la API de Jobberwocky
# Uso: ./examples/test_api.sh

BASE_URL="http://localhost:8080"
API_URL="$BASE_URL/api/v1"

echo "======================================"
echo "🚀 Jobberwocky API Test Script"
echo "======================================"
echo ""

# Health Check
echo "1️⃣  Health Check"
curl -s "$BASE_URL/health" | jq .
echo -e "\n"

# Crear Jobs Internos
echo "2️⃣  Creando jobs internos..."
echo ""

JOB1=$(curl -s -X POST "$API_URL/jobs" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Senior PHP Developer",
    "company": "TechCorp Argentina",
    "description": "Buscamos desarrollador PHP senior con experiencia en Laravel",
    "location": "Buenos Aires, Argentina",
    "salary": "150k-200k USD",
    "skills": ["PHP", "Laravel", "MySQL", "Docker", "AWS"]
  }')

echo "✅ Job 1 creado:"
echo "$JOB1" | jq .
echo ""

JOB2=$(curl -s -X POST "$API_URL/jobs" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Python Data Scientist",
    "company": "DataCo",
    "description": "Machine Learning y análisis de datos",
    "location": "Remote",
    "salary": "120k-180k USD",
    "skills": ["Python", "TensorFlow", "Pandas", "Scikit-learn"]
  }')

echo "✅ Job 2 creado:"
echo "$JOB2" | jq .
echo ""

JOB3=$(curl -s -X POST "$API_URL/jobs" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "DevOps Engineer",
    "company": "CloudTech",
    "description": "Gestión de infraestructura cloud",
    "location": "Spain",
    "salary": "100k-150k USD",
    "skills": ["Docker", "Kubernetes", "AWS", "Terraform", "CI/CD"]
  }')

echo "✅ Job 3 creado:"
echo "$JOB3" | jq .
echo ""

# Listar todos los jobs
echo "3️⃣  Listando todos los jobs internos..."
curl -s "$API_URL/jobs" | jq .
echo ""

# Buscar jobs por patrón
echo "4️⃣  Buscando jobs con 'python'..."
curl -s "$API_URL/jobs/search?q=python&sources=internal" | jq .
echo ""

# Buscar en AMBAS fuentes (interno + externo)
echo "5️⃣  Buscando 'engineer' en AMBAS fuentes (interno + externo)..."
echo "⚠️  Requiere que el servicio externo esté corriendo en localhost:8081"
curl -s "$API_URL/jobs/search?q=engineer&sources=all" | jq .
echo ""

# Buscar por ubicación
echo "6️⃣  Buscando jobs en 'Argentina'..."
curl -s "$API_URL/jobs/search?location=Argentina&sources=internal" | jq .
echo ""

# # Suscribirse a alertas
# echo "7️⃣  Suscribiéndose a alertas de 'python'..."
# ALERT=$(curl -s -X POST "$API_URL/alerts/subscribe" \
#   -H "Content-Type: application/json" \
#   -d '{
#     "email": "developer@example.com",
#     "searchPattern": "python remote"
#   }')

# echo "$ALERT" | jq .
# ALERT_ID=$(echo "$ALERT" | jq -r '.data.id')
# echo ""

# # Listar alertas
# echo "8️⃣  Listando todas las alertas..."
# curl -s "$API_URL/alerts" | jq .
# echo ""

# # Crear un job que coincida con la alerta
# echo "9️⃣  Creando job que coincide con la alerta (python remote)..."
# curl -s -X POST "$API_URL/jobs" \
#   -H "Content-Type: application/json" \
#   -d '{
#     "title": "Python Backend Developer Remote",
#     "company": "StartupXYZ",
#     "location": "Remote",
#     "salary": "140k USD",
#     "skills": ["Python", "FastAPI", "PostgreSQL"]
#   }' | jq .
# echo ""
# echo "📧 Se debería haber enviado una notificación a developer@example.com"
# echo ""

# Obtener un job específico
echo "🔟 Obteniendo detalles de un job específico..."
JOB_ID=$(echo "$JOB1" | jq -r '.data.id')
curl -s "$API_URL/jobs/$JOB_ID" | jq .
echo ""

# Buscar jobs por empresa
echo "1️⃣1️⃣  Buscando jobs de 'TechCorp'..."
curl -s "$API_URL/jobs/search?company=TechCorp&sources=internal" | jq .
echo ""

# Búsqueda avanzada con múltiples filtros
echo "1️⃣2️⃣  Búsqueda avanzada: 'developer' + 'remote' + fuentes externas..."
curl -s "$API_URL/jobs/search?q=developer&location=remote&sources=all" | jq .
echo ""

# # Cancelar suscripción
# echo "1️⃣3️⃣  Cancelando suscripción de alerta..."
# curl -s -X DELETE "$API_URL/alerts/$ALERT_ID" | jq .
# echo ""

# # Eliminar un job
# echo "1️⃣4️⃣  Eliminando job..."
# curl -s -X DELETE "$API_URL/jobs/$JOB_ID" | jq .
# echo ""

echo "======================================"
echo "✅ Tests completados!"
echo "======================================"
echo ""
echo "📊 Resumen:"
echo "- Jobs creados: 4"
echo "- Búsquedas realizadas: 6"
# echo "- Alertas: 1 creada y eliminada"
# echo "- Jobs eliminados: 1"
# echo ""
echo "🔗 Endpoints disponibles:"
echo "- Docs: $BASE_URL/"
echo "- Health: $BASE_URL/health"
echo "- Jobs: $API_URL/jobs"
echo "- Search: $API_URL/jobs/search"
# echo "- Alerts: $API_URL/alerts"