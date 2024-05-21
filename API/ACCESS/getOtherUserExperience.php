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

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $key = "proyectoDavid";
        $headers = apache_request_headers();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if (!$authHeader) {
            header('Content-Type: application/json');
            header('HTTP/1.0 401 Unauthorized');
            echo json_encode(array(
                'error' => 'No Authorization header provided'
            ));
            throw new Exception("No Authorization header provided");
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['idUser'];

        $usuario = $db->usuarios->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        if (!$usuario) {
            header('Content-Type: application/json');
            header('HTTP/1.0 404 Not Found');
            echo json_encode(array(
                'error' => 'User not found'
            ));
            throw new Exception("User not found");
        }

        $userExperience = $usuario->experience;

        header('Content-Type: application/json');
        header('HTTP/1.0 200 OK');
        echo json_encode($userExperience);
    } catch (Exception $e) {
        header('HTTP/1.0 500 Internal Server Error');
        echo json_encode(array(
            'error' => $e->getMessage()
        ));
    }
} 

?>