# CRUD de Productos

Proyecto de CRUD (Create, Read, Update, Delete) para la gestión de productos.

## Requisitos

PHP 8.2.12

Composer 2.8.1

## Instalación

1. Clonar el repositorio

`git clone https://github.com/FerF527/crud_productos.git`

2. Instalar las dependencias con Composer

`cd crud_productos`

`composer install`

3. Crear un archivo `.env` a partir del archivo `.env.example` y configurar las variables de entorno.

`cp .env.example .env`

`php artisan key:generate`

## Pruebas Unitarias

1. Pruebas de funciones del controlador:

`./vendor/bin/phpunit tests/Feature/TestController.php`

2. Pruebas de funciones del módelo:

`./vendor/bin/phpunit tests/Unit/TestModel.php`

## Uso

Levantar la aplicación mediante el comando  `php artisan serve`

Luego, accede a la aplicación a través de la URL: http://127.0.0.1:8000