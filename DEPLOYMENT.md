# Despliegue de A-Library en Render + Aiven

La aplicación se aloja en **Render** (como servicio web Docker) y usa **Aiven** como
**MySQL gestionado con SSL**. Todos los archivos necesarios ya están en el repo:

| Archivo | Función |
|---|---|
| `Dockerfile` | Imagen PHP 8.4 + Apache, con assets compilados por Vite |
| `docker/vhost.conf` | Apache sirviendo desde `/public` |
| `docker/entrypoint.sh` | Ajusta el puerto, migra y cachea config en cada arranque |
| `render.yaml` | Blueprint de Render (servicio web + variables de entorno) |
| `.dockerignore` | Excluye dependencias, secretos y caché del contexto de build |

---

## 1. Crear la base de datos en Aiven

1. En [console.aiven.io](https://console.aiven.io) crea un servicio **MySQL**.
2. Elige plan y **región** (apunta la región para usar la misma/cercana en Render).
3. Cuando el servicio esté *Running*, ve a **Connection information** y anota:
   - **Host** → `mysql-xxxx.aivencloud.com`
   - **Port** → p.ej. `12345`
   - **User** → `avnadmin`
   - **Password**
   - **Database name** → `defaultdb`
4. Descarga el **CA Certificate** (`ca.pem`). Lo necesitarás para el SSL.

> Aiven exige conexiones SSL. El `config/database.php` ya lee la ruta del CA desde
> la variable `MYSQL_ATTR_SSL_CA`.

---

## 2. Subir el código a GitHub

```bash
git add .
git commit -m "Configura despliegue Docker para Render + Aiven"
git push origin main
```

Render desplegará desde este repositorio.

---

## 3. Crear el servicio web en Render

### Opción A — Blueprint (recomendado, usa `render.yaml`)

1. En [dashboard.render.com](https://dashboard.render.com) → **New → Blueprint**.
2. Conecta el repositorio. Render detecta `render.yaml` y crea el servicio `a-library`.
3. Te pedirá rellenar las variables marcadas como secretas (paso 4).

### Opción B — Manual

1. **New → Web Service** → conecta el repo.
2. **Runtime**: Docker. **Region**: la de Aiven. **Plan**: Free (o superior).
3. **Health Check Path**: `/login`.

---

## 4. Configurar las variables de entorno

En **Environment** del servicio, define:

| Variable | Valor |
|---|---|
| `APP_NAME` | `A-Library` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | *(ver abajo)* |
| `APP_URL` | `https://a-library.onrender.com` (tu URL real) |
| `LOG_CHANNEL` | `stderr` |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | host de Aiven |
| `DB_PORT` | puerto de Aiven |
| `DB_DATABASE` | `defaultdb` |
| `DB_USERNAME` | `avnadmin` |
| `DB_PASSWORD` | contraseña de Aiven |
| `MYSQL_ATTR_SSL_CA` | `/etc/secrets/aiven-ca.pem` |
| `SESSION_DRIVER` | `database` |
| `CACHE_STORE` | `database` |
| `QUEUE_CONNECTION` | `database` |

**Generar `APP_KEY`** (en tu máquina, dentro del proyecto):

```bash
php artisan key:generate --show
```

Copia el valor completo (`base64:...`) en la variable `APP_KEY`.

---

## 5. Añadir el certificado CA de Aiven (Secret File)

En **Environment → Secret Files → Add Secret File**:

- **Filename**: `aiven-ca.pem`
- **Contents**: pega el contenido del `ca.pem` descargado de Aiven.

Render lo monta en `/etc/secrets/aiven-ca.pem`, que es justo lo que apunta
`MYSQL_ATTR_SSL_CA`.

---

## 6. Desplegar y verificar

1. Lanza el deploy (**Manual Deploy → Deploy latest commit** o push a `main`).
2. En los **Logs** verás el arranque del `entrypoint.sh`:
   - `Running migrations.` → las tablas se crean en Aiven.
   - `Configuration cached` / `Routes cached` / `Blade templates cached`.
   - `Apache/2.4 ... resuming normal operations`.
3. Abre `https://<tu-servicio>.onrender.com/register`, crea una cuenta y prueba.

---

## Notas importantes

- **Migraciones automáticas**: `entrypoint.sh` ejecuta `php artisan migrate --force`
  en cada arranque (idempotente). Los seeders **no** se ejecutan en producción; si
  quieres datos de ejemplo, lánzalos una vez desde el **Shell** de Render:
  `php artisan db:seed --force`.

- **Portadas subidas y almacenamiento efímero**: el disco de Render se **reinicia en
  cada deploy** (no hay disco persistente en el plan Free). Las portadas subidas como
  archivo (`storage/app/public/covers`) se perderán al redesplegar. Por eso, en
  producción conviene **usar portadas por URL** (ya soportado) o, si necesitas
  subidas persistentes, añadir un **disco persistente** (planes de pago) o un bucket
  **S3** configurando `FILESYSTEM_DISK=s3`.

- **Plan Free de Render**: el servicio se *duerme* tras inactividad y tarda unos
  segundos en despertar en la primera petición. Para evitarlo, usa un plan de pago.

- **HTTPS**: Render provee TLS automático. Como `APP_URL` es `https://`, considera
  mantener `SESSION_SECURE_COOKIE=true` (ya incluido en `render.yaml`).

- **Logs**: con `LOG_CHANNEL=stderr` los logs de Laravel aparecen directamente en el
  panel de **Logs** de Render.
