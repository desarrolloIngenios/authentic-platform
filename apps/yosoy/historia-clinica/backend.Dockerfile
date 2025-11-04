FROM node:18-alpine

WORKDIR /app

# Instalar dependencias del sistema
RUN apk add --no-cache sqlite

# Copiar archivos de configuración
COPY package-backend.json package.json

# Instalar dependencias
RUN npm install --production

# Copiar código fuente
COPY . .

# Crear directorio para la base de datos
RUN mkdir -p data && chmod 777 data

# Exponer puerto
EXPOSE 3001

# Comando de inicio
CMD ["node", "server.js"]