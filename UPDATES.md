# Guía de Actualización del Proyecto

Este documento describe el procedimiento paso a paso para actualizar los repositorios, generar las distribuciones, desplegar los cambios tanto en el backend como en el frontend, y limpiar la caché del cliente y del servidor.  

## Índice

1. [Actualizar Repositorios](#1-actualizar-repositorios)  
   1.1 [Repositorio Backend](#11-repositorio-backend)  
   1.2 [Repositorio Frontend](#12-repositorio-frontend)  
2. [Generar Distribución del Lanzamiento](#2-generar-distribución-del-lanzamiento)  
   2.1 [Repositorio Backend](#21-repositorio-backend)  
   2.2 [Repositorio Frontend](#22-repositorio-frontend)  
3. [Actualizar el Backend en Producción](#3-actualizar-el-backend-en-producción)  
4. [Actualizar el Frontend](#4-actualizar-el-frontend)  
5. [Borrar Caché del Cliente](#5-borrar-caché-del-cliente)

---

## 1. Actualizar Repositorios

### 1.1 Repositorio Backend

1. Abrir una terminal.
2. Navegar a la raíz del proyecto backend:

   ```bash
   cd /ruta/al/proyecto/backend
   ```

3. Subir los cambios al repositorio remoto:

   ```bash
   git push
   ```

### 1.2 Repositorio Frontend

1. Abrir una terminal.
2. Navegar a la raíz del proyecto frontend:

   ```bash
   cd /ruta/al/proyecto/frontend
   ```

3. Subir los cambios al repositorio remoto:

   ```bash
   git push
   ```

---

## 2. Generar Distribución del Lanzamiento

### 2.1 Repositorio Backend

1. Navegar al home del proyecto backend:

   ```bash
   cd /ruta/al/proyecto/backend
   ```

2. Ejecutar el comando de compilación:

   ```bash
   npm run build
   ```

3. Este comando generará un directorio llamado `public/build` que contiene los archivos listos para producción.

### 2.2 Repositorio Frontend

1. Navegar al home del proyecto frontend:

   ```bash
   cd /ruta/al/proyecto/frontend
   ```

2. Ejecutar el comando de compilación:

   ```bash
   ng build
   ```

3. Este comando generará un directorio `dist/` con los archivos de producción del frontend.

---

## 3. Actualizar el Backend en Producción

### 3.1 Obtener los cambios

1. Acceder al servidor de producción.
2. Navegar al directorio raíz del proyecto Laravel:

   ```bash
   cd /var/www/domains/store.siliconcsa.com/store/
   ```

3. Obtener los últimos cambios del repositorio:

   ```bash
   git pull
   ```

### 3.2 Verificar migraciones pendientes

Ejecutar el siguiente comando para revisar el estado de las migraciones:

```bash
php83 artisan migrate:status
```

> ⚠️ Asegúrate de que las migraciones no vayan a eliminar datos o tablas necesarias para el funcionamiento del sistema.

### 3.3 Ejecutar migraciones

Si todo está correcto, ejecuta las migraciones:

```bash
php83 artisan migrate
```

### 3.4 Subir los archivos del build

Copiar (y reemplazar) el contenido del directorio `public/build` generado en local al servidor de producción, dentro del mismo directorio `public/build`.

### 3.5 Regenerar la caché de Laravel

Puedes limpiar y regenerar la caché con los siguientes comandos:

```bash
php83 artisan cache:clear
php83 artisan config:clear
php83 artisan route:clear
php83 artisan view:clear

php83 artisan config:cache
php83 artisan route:cache
php83 artisan view:cache
```

O ejecutarlos todos en una sola línea:

```bash
php83 artisan cache:clear; php83 artisan config:clear; php83 artisan route:clear; php83 artisan view:clear; php83 artisan config:cache; php83 artisan route:cache; php83 artisan view:cache
```

---

## 4. Actualizar el Frontend

1. Comprimir el contenido del directorio `dist/` en un archivo `.zip` para asegurar la integridad de los archivos al transferirlos:

   ```bash
   zip -r dist.zip dist/
   ```

2. Subir el archivo `dist.zip` al servidor, al directorio `public`.

3. Una vez en el servidor, descomprimir el archivo:

   ```bash
   unzip dist.zip
   ```

4. Asegúrate de que los archivos se ubiquen correctamente dentro del directorio `public`.

---

## 5. Borrar Caché del Cliente

Pide a los usuarios que hagan una de las siguientes acciones:

- **Hard refresh** del navegador:  
  En la mayoría de navegadores es con `Ctrl + F5` (Windows/Linux) o `Cmd + Shift + R` (Mac).

- **Limpiar caché manualmente** desde la configuración del navegador.

También puedes incluir un parámetro único en los archivos estáticos (por ejemplo, `style.css?v=123`) para forzar la actualización automática.

---
