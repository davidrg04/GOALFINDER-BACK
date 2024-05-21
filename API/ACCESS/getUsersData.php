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

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
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

        $usuario = $db->usuarios->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        if (!$usuario) {
            throw new Exception("User not found");
        }

        $userData = array(
            'id' => (string) $usuario->_id,
            'name' => $usuario->name,
            'surnames' => $usuario->surnames,
            'username' => $usuario->username,
            'birthDate' => $usuario->birthDate ?? "",
            'footballRole' => $usuario->footballRole ?? "", 
            'position' => $usuario->position ?? "", 
            'club' => $usuario->club ?? "",
            'profilePicture' => $usuario->profilePicture,
            'community' => $usuario->community ?? "",
            'city' => $usuario->city ?? "",
            'saves' => $usuario->save ?? [],
            'description' => $usuario->description ?? "",
            'followers' => $usuario->followers ?? [],
            'following' => $usuario->following ?? [],
        );

        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        echo json_encode($userData);
    } catch (Exception $e) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(array('error' => $e->getMessage()));
    }
}

?>