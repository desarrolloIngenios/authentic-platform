FROM nginx:alpine

# Copiar el archivo HTML actualizado
COPY index.html /usr/share/nginx/html/index.html

# Configuración de nginx (usando configuración por defecto)
# COPY nginx.conf /etc/nginx/nginx.conf

# Exponer puerto 80
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]