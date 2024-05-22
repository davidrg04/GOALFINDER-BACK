<?php
require_once('../CONEXION/conexion.php');

$con = new Conexion();
$db = $con->getDb();

// Encabezados CORS
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // Cache por 1 día
}
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Manejar solicitudes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
 

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $data['username'];
    $password = $data['password'];

    $collection = $db->usuarios;

    $user = $collection->findOne(['username' => $username]);
    require_once('./jwtGenerator.php');
    $jwt = generateJWT($user['username'], $user['role'], (string) $user['_id'], $user['completeProfile']);
    if($user){
        if(password_verify($password, $user['password'])){
            header('Content-Type: application/json');
            header('HTTP/1.1 200 OK');
            echo json_encode(["jwt" => $jwt]);
        }else{
            header('Content-Type: application/json');
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error' => 'password']);
        }
    }else{
        header('Content-Type: application/json');
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['error' => 'username']);
    }
}
?>