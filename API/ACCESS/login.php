<?php
require_once('../CONEXION/conexion.php');

header("Access-Control-Allow-Origin: https://goalfinder-front.vercel.app");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");


if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    header("HTTP/1.1 200 OK");
    exit;
}

$con = new Conexion();
$db = $con->getDb();

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
