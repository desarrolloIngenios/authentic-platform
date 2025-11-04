# Usar imagen oficial de Node.js 18 Alpine
FROM node:18-alpine

# Información de la imagen
LABEL maintainer="YoSoy Sistema de Historias Clínicas"
LABEL version="1.0.0"
LABEL description="Backend API para YoSoy Historia Clínica"

# Crear directorio de trabajo
WORKDIR /app

# Crear usuario no-root para seguridad
RUN addgroup -g 1001 -S nodejs
RUN adduser -S nodejs -u 1001

# Copiar archivos de dependencias
COPY package*.json ./

# Instalar dependencias de producción
RUN npm ci --only=production && npm cache clean --force

# Copiar código fuente
COPY --chown=nodejs:nodejs . .

# Cambiar a usuario no-root
USER nodejs

# Exponer puerto
EXPOSE 3001

# Variables de entorno por defecto
ENV NODE_ENV=production
ENV PORT=3001

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD node -e "const http = require('http'); \
  const options = { hostname: 'localhost', port: process.env.PORT || 3001, path: '/health', timeout: 2000 }; \
  const req = http.request(options, (res) => { \
    if (res.statusCode === 200) { console.log('Health check passed'); process.exit(0); } \
    else { console.log('Health check failed'); process.exit(1); } \
  }); \
  req.on('error', () => { console.log('Health check failed'); process.exit(1); }); \
  req.end();"

# Comando para iniciar la aplicación
CMD ["node", "server.js"]