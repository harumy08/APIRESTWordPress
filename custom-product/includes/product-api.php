<?php
if (!defined('ABSPATH')) {
    exit;
}
/**Esta función registra las rutas REST para operaciones CRUD en productos. 
 * Cada ruta tiene un método HTTP asociado (GET, POST, PUT, DELETE),
 *  un callback que se ejecutará cuando se acceda a esa ruta, y un permission_callback que verifica si el usuario 
 * está autenticado. */
function register_product_routes() {
    register_rest_route('custom/v1', '/products', array(
        'methods' => 'GET',
        'callback' => 'get_products',
        'permission_callback' => 'is_user_logged_in',
    ));
    register_rest_route('custom/v1', '/products/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'get_product',
        'permission_callback' => 'is_user_logged_in',
    ));
    register_rest_route('custom/v1', '/products', array(
        'methods' => 'POST',
        'callback' => 'create_product',
        'permission_callback' => 'is_user_logged_in',
    ));
    register_rest_route('custom/v1', '/products/(?P<id>\d+)', array(
        'methods' => 'PUT',
        'callback' => 'update_product',
        'permission_callback' => 'is_user_logged_in',
    ));
    register_rest_route('custom/v1', '/products/(?P<id>\d+)', array(
        'methods' => 'DELETE',
        'callback' => 'delete_product',
        'permission_callback' => 'is_user_logged_in',
    ));
}
add_action('rest_api_init', 'register_product_routes');

/**
 * Esta función obtiene todos los productos existentes. 
 * Utiliza get_posts() para recuperar los productos y devuelve una respuesta JSON con los productos.
 */
function get_products() {
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'numberposts' => -1,
    );
    $products = get_posts($args);
    return rest_ensure_response($products);
}

/**
 * Esta función obtiene un producto específico según su ID. 
 * Utiliza get_post() para obtener el producto y devuelve una respuesta JSON con el producto si se encuentra. 
 * Si no se encuentra el producto, devuelve un error 404.
 */

function get_product($request) {
    $id = $request['id'];
    $product = get_post($id);
    if (!$product) {
        return new WP_Error('no_product', 'Product not found', array('status' => 404));
    }
    return rest_ensure_response($product);
}

/**
 * Esta función crea un nuevo producto. 
 * Extrae los parámetros del cuerpo de la solicitud JSON, los sanitiza 
 * y luego utiliza wp_insert_post() para insertar el nuevo producto en la base de datos.
 * Si la inserción falla, devuelve un error 500.
 */

function create_product($request) {
    $params = $request->get_json_params();
    $title = sanitize_text_field($params['title']);
    $content = sanitize_textarea_field($params['content']);

    $new_product = array(
        'post_type' => 'product',
        'post_title' => $title,
        'post_content' => $content,
        'post_status' => 'publish',
    );
    $product_id = wp_insert_post($new_product);
    if (is_wp_error($product_id)) {
        return new WP_Error('create_failed', 'Error al crear el producto', array('status' => 500));
    }
    return rest_ensure_response(
        array(
            'message' => 'Producto creado correctamente',
            'producto_id' => $product_id
        ), 201);
}

/**
 * Esta función actualiza un producto existente.
 *  Extrae los parámetros del cuerpo de la solicitud JSON, los sanitiza y 
 * luego utiliza wp_update_post() para actualizar el producto en la base de datos. 
 * Si la actualización falla, devuelve un error 500.
 */

function update_product($request) {
    $id = $request['id'];
    $params = $request->get_json_params();
    $title = sanitize_text_field($params['title']);
    $content = sanitize_textarea_field($params['content']);

    $updated_product = array(
        'ID' => $id,
        'post_title' => $title,
        'post_content' => $content,
    );
    $result = wp_update_post($updated_product);
    if (is_wp_error($result)) {
        return new WP_Error('update_failed', 'Failed to update product', array('status' => 500));
    }
    return rest_ensure_response(array(
        'message' => 'Producto editado correctamente',
        'producto_id' => $result
    ), 201);
}

/**
 * Esta función elimina un producto existente según su ID.
 *  Utiliza wp_delete_post() para eliminar el producto de la base de datos y devuelve una respuesta JSON 
 * con el resultado. 
 * Si la eliminación falla, devuelve un error 500.
 */

function delete_product($request) {
    $id = $request['id'];
    $result = wp_delete_post($id);
    if (!$result) {
        return new WP_Error('delete_failed', 'Failed to delete product', array('status' => 500));
    }
    return rest_ensure_response($result);
}
