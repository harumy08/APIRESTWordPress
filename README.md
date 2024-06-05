# APIRESTWordPress
Desarrollo una API REST utilizando la API REST de WordPress, implementando autenticación JWT y operaciones CRUD.

Un plugin de WordPress para manejar productos usando la API REST y Composer.

## Instalación

1. Clonar el repositorio en la carpeta `wp-content/plugins` de tu instalación de WordPress:

    ```sh
    cd wp-content/plugins
    git clone https://github.com/harumy08/APIRESTWordPress.git
    ```
2. Instalar las dependencias de Composer:

    ```sh
    cd custom-product
    composer install
    ```
3. Activar el plugin en la administración de WordPress.

## Uso

1. Asegúrate de que el plugin esté en la carpeta wp-content/plugins/custom-product-api de tu instalación de WordPress.
2. Activa el plugin desde el panel de administración de WordPress (Plugins > Installed Plugins).

### Obtener un Token JWT

1. El plugin contiene una función temporal en el archivo principal, para general un token.
2. Inicia sesión en el panel de administración de WordPress como administrador.
3. Verás el token JWT impreso en la parte superior de la pantalla.

###  Probar la API REST
Utiliza una herramienta como Postman para enviar solicitudes a la API REST del plugin.

Configurar Postman: Se incluye la configuración de Postman, solo se requiere cambiar el valor del token y el dominio de tu sitio en WordPress.

### Crear un Producto

   Request creacionProducto.

     URL: http://tusitio.com/wp-json/custom/v1/products
     **Método: POST**
     **Headers:**
     **Authorization: Bearer <tu-token-jwt>**

     La solicitud POST debería devolver el ID del nuevo producto creado.
   
  ### Obtener todos los productos

   Request ObtenciónProductos.

     URL: http://tusitio.com/wp-json/custom/v1/products
     **Método: GET**
     **Headers:**
     **Authorization: Bearer <tu-token-jwt>**

     La solicitud GET debería devolver una lista de productos.

  ### Obtener un producto por su ID

      Request ObtenciónProductoEspecífico.

     URL: http://tusitio.com/wp-json/custom/v1/products/1 (Reemplaza 1 con el ID del producto)
     **Método: GET**
     **Headers:**
     **Authorization: Bearer <tu-token-jwt>**

     La solicitud GET debería devolver un producto específico.

  ### Actualizar un producto existente

     Request ActualizaciónProducto.

     URL: http://tusitio.com/wp-json/custom/v1/products/1 (Reemplaza 1 con el ID del producto)
     **Método: PUT**
     **Headers:**
     **Authorization: Bearer <tu-token-jwt>**

     La solicitud PUT debería devolver el ID del producto actualizado.
     
### Eliminar un producto existente

     Request EliminaciónProducto.

     URL: http://tusitio.com/wp-json/custom/v1/products/1 (Reemplaza 1 con el ID del producto)
     **Método: DELETE**
     **Headers:**
     **Authorization: Bearer <tu-token-jwt>**

     La solicitud DELETE debería devolver una confirmación de que el producto fue eliminado.

