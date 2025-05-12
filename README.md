# 🛍️ Silicon Store

**Silicon Store** es una plataforma de comercio electrónico compuesta por dos proyectos principales:

- **Frontend**: Aplicación web desarrollada con Angular.
- **Backend**: API RESTful desarrollada con Laravel.

---

## 📁 Repositorios del proyecto

- **Frontend (Angular)**: [Silicon_store_Fronted](https://github.com/felipe3-A/Silicon_store_Fronted.git)
- **Backend (Laravel)**: [Silicon_Store_Laravel](https://github.com/felipe3-A/Silicon_Store_Laravel.git)

---

## 🚀 Tecnologías utilizadas

### Frontend

- **Framework**: Angular
- **Lenguaje principal**: TypeScript
- **Estilos**: CSS / SCSS
- **Gestor de paquetes**: npm
- **Herramientas de desarrollo**:
  - Webpack
  - Karma (pruebas unitarias)
  - Protractor (pruebas end-to-end)
- **Configuraciones destacadas**:
  - `angular.json`
  - `tsconfig.json`
  - `karma.conf.js`
  - `protractor.conf.js`
  - `webpack.config.js`
  - `proxy.conf.json`
  - `genezio.yaml`

### Backend

- **Framework**: Laravel
- **Lenguaje principal**: PHP
- **Base de datos**: MySQL
- **Gestor de dependencias**: Composer
- **Herramientas de desarrollo**:
  - PHPUnit (pruebas)
  - Vite (gestión de assets)
  - Tailwind CSS (estilos)
- **Configuraciones destacadas**:
  - `.env`
  - `composer.json`
  - `phpunit.xml`
  - `vite.config.js`
  - `tailwind.config.js`

---

## 📂 Estructura del proyecto

### Frontend

```
Silicon_store_Fronted/
├── src/                   # Código fuente de Angular
├── e2e/                   # Pruebas end-to-end
├── angular.json           # Configuración principal de Angular
├── tsconfig.json          # Configuración de TypeScript
├── karma.conf.js          # Configuración de pruebas unitarias
├── protractor.conf.js     # Configuración de pruebas E2E
├── webpack.config.js      # Configuración de Webpack
└── proxy.conf.json        # Proxy para peticiones HTTP
```

### Backend

```
Silicon_Store_Laravel/
├── app/                   # Lógica principal de la aplicación
├── routes/                # Definición de rutas web y API
├── database/              # Migraciones y semillas
├── config/                # Archivos de configuración
├── resources/             # Vistas y archivos estáticos
├── tests/                 # Pruebas con PHPUnit
├── .env                   # Variables de entorno
├── composer.json          # Dependencias PHP
└── vite.config.js         # Configuración de Vite y Tailwind
```

---

## ⚙️ Instalación y ejecución

### 🖥️ Frontend

1. Clonar el repositorio:

```bash
git clone https://github.com/felipe3-A/Silicon_store_Fronted.git
cd Silicon_store_Fronted
```

2. Instalar dependencias:

```bash
npm install
```

3. Ejecutar en modo desarrollo:

```bash
ng serve
```

---

### 🖥️ Backend

1. Clonar el repositorio:

```bash
git clone https://github.com/felipe3-A/Silicon_Store_Laravel.git
cd Silicon_Store_Laravel
```

2. Instalar dependencias:

```bash
composer install
```

3. Crear el archivo de entorno:

```bash
cp .env.example .env
```

4. Generar la clave de la aplicación:

```bash
php artisan key:generate
```

5. Configurar la base de datos y ejecutar migraciones:

```bash
php artisan migrate
```

6. Ejecutar el servidor de desarrollo:

```bash
php artisan serve
```

Accede a la API desde: [http://localhost:8000](http://localhost:8000)

---

## 🧪 Pruebas

### Frontend

- Ejecutar pruebas unitarias:

```bash
ng test
```

- Ejecutar pruebas end-to-end:

```bash
ng e2e
```

### Backend

- Ejecutar pruebas:

```bash
php artisan test
```

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Consulta el archivo `LICENSE.md` para más detalles.

---

## 👤 Autor

**Felipe3-A**  
GitHub: [https://github.com/felipe3-A](https://github.com/felipe3-A)

---

## ✅ Estado del proyecto

Proyecto en desarrollo activo.  
Se planean nuevas funcionalidades, mejoras en la interfaz, integración de pasarelas de pago y optimización del rendimiento general.