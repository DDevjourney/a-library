# 📚 A-Library

Biblioteca personal para llevar el control de tus lecturas: qué libros tienes, cuáles estás leyendo, cuáles has terminado (o abandonado) y qué géneros lees más.

Cada usuario tiene su propia biblioteca privada; los géneros son un catálogo compartido.

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3-06B6D4?logo=tailwindcss&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-3-8BC0D0?logo=alpinedotjs&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Aiven-4479A1?logo=mysql&logoColor=white)

**Producción:** https://a-library.onrender.com

---

## Funcionalidades

- **Cuentas de usuario** — registro, login, recuperación de contraseña y edición de perfil (Laravel Breeze)
- **Mis libros** — alta, edición y borrado de libros con título, autor, año de publicación, fechas de inicio y fin, calificación (1–5) y reseña
- **Estados de lectura** — *Por leer*, *Leyendo*, *Leído* y *Abandonado*
- **Portadas** — desde una URL o subiendo una imagen (JPG/PNG/GIF, máx. 2 MB); el archivo subido tiene prioridad sobre la URL
- **Filtros** — búsqueda por título o autor, filtro por estado y por género, y ordenación por fecha, título, autor o calificación
- **Géneros** — catálogo compartido entre usuarios; cada libro puede tener varios
- **Dashboard** — total de libros, leídos este año, calificación media, distribución por estado y géneros más leídos
- **Privacidad** — un usuario solo puede ver, editar o borrar sus propios libros (`BookPolicy`)
- **Responsive** — interfaz oscura que funciona en móvil y escritorio

---

## Stack técnico

| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 13 (PHP 8.3+) |
| Autenticación | Laravel Breeze |
| Vistas | Blade + Alpine.js |
| Estilos | Tailwind CSS 3 (`@tailwindcss/forms`) |
| Build de assets | Vite |
| Base de datos | SQLite en local · MySQL gestionado en Aiven (SSL) en producción |
| Tests | Pest 4 |
| Hosting | Render (Docker, PHP 8.4 + Apache) |

---

## Modelo de datos

```
users ──< books >──< book_genre >── genres
```

| Tabla | Campos principales |
|-------|-------------------|
| `users` | `name`, `email`, `password` |
| `books` | `user_id`, `title`, `author`, `published_year`, `cover_path`, `cover_url`, `status`, `rating`, `started_at`, `finished_at`, `review` |
| `genres` | `name` (único), `slug` (único) |
| `book_genre` | `book_id`, `genre_id` (pareja única) |

Al borrar un usuario se borran sus libros, y al borrar un libro o un género se borran sus relaciones (`cascadeOnDelete`).

---

## Rutas principales

| Ruta | Descripción |
|------|-------------|
| `/` | Redirige al dashboard si hay sesión iniciada, o al login si no |
| `/login`, `/register`, `/forgot-password` | Autenticación |
| `/dashboard` | Estadísticas de lectura |
| `/books` | Listado con filtros (`search`, `status`, `genre`, `sort`, `direction`) |
| `/books/create`, `/books/{id}`, `/books/{id}/edit` | CRUD de libros |
| `/genres` | CRUD de géneros |
| `/profile` | Datos de la cuenta, cambio de contraseña y borrado de cuenta |

Todas las rutas salvo las de autenticación requieren haber iniciado sesión.

---

## Instalación en local

**Requisitos:** PHP 8.3+, Composer y Node.js 20.19+ (lo pide Vite 8).

**1. Clonar el repo e instalar dependencias**

```bash
git clone https://github.com/DDevjourney/a-library.git
cd a-library
composer install
npm install
```

**2. Configurar el entorno**

```bash
cp .env.example .env
php artisan key:generate
```

El `.env.example` ya viene preparado para usar **SQLite** (`database/database.sqlite`), así que no hace falta montar ningún servidor de base de datos.

> ⚠️ No uses en local un `.env` con las credenciales de Aiven: trabajarías directamente sobre los datos de producción.

**3. Crear la base de datos con datos de ejemplo**

```bash
php artisan migrate --seed
php artisan storage:link
```

El seeder crea 10 géneros, un usuario de pruebas (`test@example.com`, con la contraseña por defecto de `database/factories/UserFactory.php`) con 15 libros y otros 3 usuarios con 10 libros cada uno.

**4. Arrancar**

```bash
composer run dev
```

Levanta a la vez el servidor de Laravel, la cola, los logs (Pail) y Vite. La app queda en http://127.0.0.1:8000.

En Windows, Pail no funciona porque necesita la extensión `pcntl`. En ese caso, arranca el servidor y Vite por separado:

```bash
php artisan serve
npm run dev
```

---

## Tests

```bash
composer test
```

Los tests usan SQLite en memoria (configurado en `phpunit.xml`) y cubren la autenticación, el perfil, los libros y los géneros.

---

## Despliegue

La app se despliega en **Render** como servicio Docker, con la base de datos en **Aiven** (MySQL con SSL). Cada `push` a la rama principal vuelve a desplegar.

En cada arranque, el contenedor crea el enlace de `storage`, ejecuta las migraciones y cachea la configuración (`docker/entrypoint.sh`).

La guía completa paso a paso está en **[DEPLOYMENT.md](DEPLOYMENT.md)**.

| Archivo | Función |
|---------|---------|
| `Dockerfile` | Imagen PHP 8.4 + Apache con los assets ya compilados |
| `docker/` | Virtual host de Apache, entrypoint y certificado CA de Aiven |
| `render.yaml` | Blueprint de Render con las variables de entorno |

---

## Estructura del proyecto

```
app/
├── Http/Controllers/    BookController, GenreController, DashboardController, ProfileController, Auth/
├── Http/Requests/       Validación de libros, géneros y perfil
├── Models/              User, Book, Genre
└── Policies/            BookPolicy (cada usuario solo accede a sus libros)
database/
├── factories/           Datos falsos para seeders y tests
├── migrations/
└── seeders/
resources/views/
├── books/  genres/  profile/  auth/
├── components/          Componentes Blade reutilizables
└── layouts/             app (zona privada) y guest (login/registro)
routes/
├── web.php              Rutas de la app
└── auth.php             Rutas de Breeze
```

---

## Licencia

MIT
