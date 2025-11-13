#!/bin/bash
# Script para mover Proyecto-H a /var/www/html y configurar permisos
# Ahora reemplaza todo en caso de conflicto usando rsync

# 1. Definir variables
PROJECT_DIR="/var/www/html/Proyecto-H"
APACHE_ROOT="/var/www/html"

# 2. Mover todo el contenido del proyecto al root de Apache usando rsync
echo "Moviendo y reemplazando archivos de $PROJECT_DIR a $APACHE_ROOT..."
sudo rsync -av --ignore-errors $PROJECT_DIR/ $APACHE_ROOT/

# 3. Borrar la carpeta vacía del proyecto
echo "Eliminando carpeta $PROJECT_DIR..."
sudo rm -rf $PROJECT_DIR

# 4. Ajustar permisos para Apache
echo "Ajustando permisos para Apache..."
sudo chown -R apache:apache $APACHE_ROOT
sudo chmod -R 755 $APACHE_ROOT

# 5. Reiniciar Apache
echo "Reiniciando Apache..."
sudo systemctl restart httpd

echo "¡Listo! Tu proyecto ahora está en $APACHE_ROOT y Apache lo sirve correctamente."
