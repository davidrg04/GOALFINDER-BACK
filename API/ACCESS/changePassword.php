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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $key = "proyectoDavid";
        $headers = apache_request_headers();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if (!$authHeader) {
            throw new Exception("No Authorization header provided");
        }

        list(, $jwt) = explode(' ', $authHeader);
        $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $id = (string) $jwtDecoded->id;

        $data = json_decode(file_get_contents('php://input'), true);

        $usuario = $db->usuarios->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        if (!$usuario) {
            throw new Exception("User not found");
        }

        $oldPassword = $data['oldPassword'];
        $newPassword = $data['newPassword'];
        $passVerify = false;
        
        if (password_verify($oldPassword, $usuario->password)) {
            $passVerify = true;
        }

        if(!$passVerify){
            header('Content-Type: application/json');
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['status' => 'error', 'message' => 'Old password is incorrect']);
            exit;
        }else{
            $db->usuarios->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($id)],
                ['$set' => ['password' => password_hash($newPassword, PASSWORD_BCRYPT)]]
            );

            header('Content-Type: application/json');
            header('HTTP/1.1 200 OK');
            echo json_encode(['status' => 'success', 'message' => 'Password changed successfully']);
        }
    } catch (\Throwable $th) {
        header('Content-Type: application/json');
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['status' => 'error', 'message' => 'Failed to change password: ' . $th->getMessage()]);
    }
}
?>
