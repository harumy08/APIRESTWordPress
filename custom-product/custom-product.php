<?php
/**
 * Plugin Name: Custom Product API
 * Description: A custom plugin to manage products via REST API with JWT authentication.
 * Version: 1.0
 * Author: Natalia H Muñoz Escartin
 */

// Aasegura que el plugin solo pueda ser ejecutado dentro del contexto de WordPress
if (!defined('ABSPATH')) {
    exit;
}

// Incluye el autoloader de Composer, ya que se usa firebase para manejar JWT
require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';

// Incluye archivos necesarios
include_once plugin_dir_path(__FILE__) . 'includes/jwt-auth.php'; //Archivo de Autenticación JWT
include_once plugin_dir_path(__FILE__) . 'includes/product-api.php'; //Archivo de API

// Registra el tipo de entrada personalizado "Productos"
function register_product_post_type() {
    register_post_type('product', array(
        'labels' => array(
            'name' => __('Productos'),
            'singular_name' => __('Producto')
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => array('title', 'editor', 'custom-fields'),
    ));
}

add_action('init', 'register_product_post_type');
