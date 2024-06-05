<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!defined('ABSPATH')) {
    exit;
}


/** Esta función genera un token JWT utilizando la biblioteca Firebase JWT.
 *  Toma el ID de usuario como parámetro y crea un token con una fecha de emisión (iat), 
 * una fecha de expiración (exp) y el ID de usuario como subyacente (sub). 
 * Si está definida una clave secreta JWT (JWT_SECRET_KEY), se utiliza; de lo contrario, se usa una clave predeterminada. */
function generate_jwt_token($user_id) {
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600;  // Token válido por 1 hora
    $payload = array(
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'sub' => $user_id,
    );

    $jwt_secret_key = defined('JWT_SECRET_KEY') ? JWT_SECRET_KEY : 'qguxxJh#OV(Wzx#@xLWT5#gY6B%-2jNw#rrHL_v';
    return JWT::encode($payload, $jwt_secret_key, 'HS256');
}


/**
 * Esta función verifica la validez de un token JWT dado. 
 * Utiliza la clave secreta JWT para verificar la firma del token. 
 * Si el token es válido, devuelve el ID de usuario asociado; de lo contrario, devuelve null.
 */
function verify_jwt_token($token) {
    try {
        $jwt_secret_key = defined('JWT_SECRET_KEY') ? JWT_SECRET_KEY : 'qguxxJh#OV(Wzx#@xLWT5#gY6B%-2jNw#rrHL_v';
        $decoded = JWT::decode($token, new Key($jwt_secret_key, 'HS256'));
        return $decoded->sub;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Esta función es un controlador para la autenticación JWT en las solicitudes REST.
 *  Verifica si hay un encabezado de autorización en la solicitud HTTP y extrae el token JWT. 
 * Luego, utiliza verify_jwt_token() para verificar la validez del token y obtener el ID de usuario asociado. 
 * Si el usuario está autenticado, se establece como el usuario actual en WordPress. 
 * Si no, se devuelve el resultado original.
 */


 /**error_log() para registrar información en los logs. 
  * Desactivarlo en un entorno de producción. */
function rest_api_jwt_auth_handler($result) {
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        error_log('Authorization header found');
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'];
        list($token) = sscanf($auth_header, 'Bearer %s');

        if ($token) {
            error_log('Token found: ' . $token);
            $user_id = verify_jwt_token($token);
            if ($user_id) {
                error_log('User ID from token: ' . $user_id);
                $user = get_user_by('id', $user_id);
                if ($user) {
                    return $user->ID;
                }
            } else {
                error_log('Invalid token');
            }
        } else {
            error_log('Token not found in Authorization header');
        }
    } else {
        error_log('Authorization header not found');
    }
    return $result;
}
add_filter('determine_current_user', 'rest_api_jwt_auth_handler', 20);
add_filter('rest_authentication_errors', function ($result) {
    if (!empty($result)) {
        return $result;
    }
    if (!is_user_logged_in()) {
        return new WP_Error('jwt_auth_not_logged_in', 'You are not logged in.', array('status' => 403));
    }
    return $result;
});

/** Filtro que maneja los errores de autenticación en las solicitudes REST, 
 * devolviendo un error si el usuario no está autenticado. */

/**
 * Esta función genera un token JWT de prueba y lo muestra como un aviso de administrador.
 *  Solo está disponible para los usuarios con el rol de administrador.
 */

function generate_test_jwt_token() {
    if (current_user_can('administrator')) {
        $user_id = get_current_user_id(); // Obtener el ID del usuario actual
        $token = generate_jwt_token($user_id);
        echo 'Token JWT: ' . $token;
    }
}
add_action('admin_notices', 'generate_test_jwt_token');
