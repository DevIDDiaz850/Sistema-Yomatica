## 1. Requisitos Previos
Asegúrate de tener instalado lo siguiente en tu entorno local antes de continuar:
* **Docker Desktop** instalado y ejecutándose.
* **WSL2** (Windows Subsystem for Linux) configurado (Recomendado para máxima velocidad).
* **Git** para el control de versiones.
* *(Nota: El entorno utiliza PHP >= 8.2 y PostgreSQL, pero todo será gestionado internamente por Docker).*

---

## 2. Clonar el Repositorio
Abre tu terminal (WSL preferentemente) y descarga el código del proyecto navegando hasta la carpeta principal:

```bash
git clone [https://github.com/DevIDDiaz850/Sistema-Yomatica.git](https://github.com/DevIDDiaz850/Sistema-Yomatica.git)
cd Sistema-Yomatica
cd Yomatica 
```

---

## 3. Instalar Dependencias (El truco de Docker)
Como acabas de clonar el proyecto, no tienes la carpeta `vendor` ni el binario de Sail. Usaremos un contenedor temporal de Docker para instalar las librerías de PHP (Composer) sin necesidad de tenerlo instalado en tu Windows/WSL:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

---

## 4. Configurar el Archivo .env
Laravel no sube el archivo `.env` por seguridad. Crea uno a partir del archivo de ejemplo:

```bash
cp .env.example .env
```

Abre el archivo `.env` recién creado y configura los puertos para evitar conflictos con tu Windows, además de agregar tu llave de **Facturapi**:

```env
# Puerto web de la aplicación
APP_PORT=8080

# Configuración de PostgreSQL (Sail)
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432            # Puerto interno de Docker (NO CAMBIAR)
FORWARD_DB_PORT=5433    # Puerto externo para conectarse desde PhpStorm/DBeaver
DB_DATABASE=yomatica
DB_USERNAME=sail
DB_PASSWORD=password

# Configuración de Facturapi
FACTURAPI_KEY=sk_test_TU_LLAVE_SECRETA_AQUI
```

---

## 5. Configuración en la Terminal (WSL)
Antes de encender los contenedores, asegúrate de tener el atajo de Sail y los permisos correctos en el binario:

```bash
# 1. Crear el alias de Sail
echo "alias sail='./vendor/bin/sail'" >> ~/.bashrc
source ~/.bashrc

# 2. Dar permisos de ejecución al binario
chmod +x vendor/laravel/sail/bin/sail
```

---

## 6. Levantar Contenedores y Generar Llave
Con Sail configurado y el `.env` listo, levanta el entorno de Docker y genera la llave de encriptación de la aplicación:

```bash
# Levantar el proyecto (reemplaza a php artisan serve)
sail up -d

# Generar la llave criptográfica
sail artisan key:generate
```

---

## 7. Base de Datos, Enlaces y Migraciones
Con los contenedores corriendo, ejecuta las migraciones para crear la estructura de tablas y crea el enlace simbólico para poder ver los archivos públicos (como los PDFs de las facturas):

```bash
# Migrar las tablas
sail artisan migrate

# Crear enlace simbólico para archivos (storage)
sail artisan storage:link
```

---

## 8. Configuración de Administrador y Filament Shield
El panel utiliza `Filament Shield` para el manejo de roles y permisos. Sigue estos pasos para crear tu usuario administrador:

### A. Verificar el Modelo User
Asegúrate de que el modelo `app/Models/User.php` tenga el trait `HasRoles`:
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    // ...
}
```

### B. Instalar Shield y Crear el Super Admin
Ejecuta los comandos para generar los permisos y crear el usuario administrador (esto reemplaza el comando tradicional `make:filament-user` y te otorga control total):

```bash
# 1. Instalar Shield (Presiona 'yes' a todas las confirmaciones)
sail artisan shield:install

# 2. Crea tu usuario administrador para Filament
sail artisan shield:super-admin
```

---

## 9. Acceso a la Aplicación y Comandos Útiles

* **Sitio Web:** http://localhost:8080
* **Panel de Administración (Filament):** http://localhost:8080/admin

**Comandos del Día a Día:**
* `sail up -d` : Enciende el proyecto en segundo plano.
* `sail stop` : Apaga el proyecto.
* `sail artisan tinker` : Abre la consola interactiva de PHP.
* `sail artisan migrate` : Ejecuta nuevas migraciones si descargas cambios de GitHub.
