# Sistema Yomatica

Este proyecto es un sistema de administración automatizado e integrado con **Facturapi** para el timbrado de facturas bajo el esquema de **CFDI 4.0**, construido sobre el framework **Laravel** y el panel administrativo **Filament**.

---

## Requisitos Previos

Asegúrate de tener instalado lo siguiente en tu entorno local antes de continuar:

* **PHP** >= 8.2 (Recomendado para la versión actual)
* **Composer**
* **SQLITE** (o tu motor de base de datos preferido)
* **Git**

---
## Árbol del proyecto
```bash
example-app/
├── app/
│   ├── Filament/
│   │   └── Resources/
│   │       ├── FacturaResource.php            
│   │       └── Facturas/                      
│   │           ├── Pages/                    
│   │           │   ├── CreateFactura.php      
│   │           │   ├── EditFactura.php        
│   │           │   └── ListFacturas.php       
│   │           └── Tables/
│   │               └── FacturasTable.php      
│   │
│   ├── Http/
│   │   └── Controllers/
│   │       └── FacturaDownloadController.php  
│   │
│   └── Services/
│       └── FacturapiFacturaService.php      
│
├── database/
│   └── migrations/                            
│
├── routes/
│   └── web.php                              
│
├── storage/
│   └── app/public/                           
│
├── .env                                       
└── README.md                                  
```
## Instalación Paso a Paso

Sigue estos pasos para levantar el proyecto en tu entorno local desde cero:

### 1. Clonar el repositorio
Abre tu terminal y descarga el código del proyecto:
```bash
git clone https://github.com/DevIDDiaz850/Sistema-Yomatica.git
cd sistema-yomatica
cd example-app 
```
### 2. instalar dependencias
Abre tu terminal e instala las dependencias:
```bash
composer install
```
### 3. Crear el archivo de configuración de entorno
Abre tu terminal e instala las dependencias:
```bash
cp .env.example .env
```
### 4. Conectar a tu base de datos local 
```bash
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_yomatica
DB_USERNAME=root
DB_PASSWORD=  
FACTURAPI_KEY=sk_test_TU_LLAVE_SECRETA_AQUI   
```
### 5. Migrar las tablas 
```bash
php artisan migrate   
```
### 6. Crear el enlace simbólico para archivos
```bash
php artisan storage:link
```
### 6. Crea tu usuario administrador para filament
```bash
php artisan make:filament-user
```
### 7. Correr el proyecto
```bash
php artisan serve
```



