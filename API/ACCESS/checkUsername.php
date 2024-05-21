<?php
require_once('../CONEXION/conexion.php');
require "../../../vendor/autoload.php"; 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$con = new Conexion();
$db = $con->getDb();

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
    }

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }

    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        if (isset($_GET['username'])) {
            $username = $_GET['username'];
            $key = "proyectoDavid";
            $headers = apache_request_headers();
            $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;
    
            if (!$authHeader) {
                throw new Exception("No Authorization header provided");
            }
    
            list(, $jwt) = explode(' ', $authHeader);
            $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));
            $id = (string) $jwtDecoded->id;
    
            $collection = $db->usuarios;
            $user = $collection->findOne(['username' => $username]);
    
            if ($user) {
                if ((string) $user->_id === $id) {
                    echo json_encode(['exists' => false]);
                } else {
                    echo json_encode(['exists' => true]);
                }
            } else {
                echo json_encode(['exists' => false]);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'El nombre de usuario es requerido']);
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
