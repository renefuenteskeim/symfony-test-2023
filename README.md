# symfony-test-2023

Pre requisitos

•	Symfony CLI: https://symfony.com/download
•	PHP 8.2 (cli).
•	Composer: https://getcomposer.org/download/
•	Postgres
•	Postman


Clonar repositorio <REPOSITORIO>
Ejecutar el siguiente comando: 

• composer update

Modificar archivo .env con las credenciales de la base de datos
-

Ejecutar el siguiente comando:

• <Crear tablas>
• symfony server:start


Rutas de la aplicación:

| Ruta | Descripcion |
| --- | --- |
| /product | Lista de productos |
| /product/add | Agregar productos |
| /product/update | Actualiza productos |
| /product/delete/{sku} | Elimina un producto a partir del sku |


• Lista de productos (GET) 

    http://127.0.0.1:8000/product

• Agregar Productos (Post)

    http://127.0.0.1:8000/product/add


    Json de ejemplo:


    [
        {
            "sku": "1",
            "name": "PC",
            "description": "Computador lenovox"
        }
    ]

• Actualizar Productos (PUT)

http://127.0.0.1:8000/product/update

    [
        {
            "sku": "8",
            "name": "PC",
            "description": "pc"
        }
    ]

• Eliminar Productos (DELETE)

http://127.0.0.1:8000/product/delete/{sku}

ej : http://127.0.0.1:8000/product/delete/8

