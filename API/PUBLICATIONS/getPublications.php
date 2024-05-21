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

if($_SERVER["REQUEST_METHOD"] == "GET") {
    try{
        $key = "proyectoDavid";
        $headers = apache_request_headers();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if(!$authHeader){
            throw new Exception("No Authorization header provided");
        }

        $publications = $db->publicaciones->find();
        $publicationsArray = iterator_to_array($publications);

        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        echo json_encode(['publications' => $publicationsArray]);

    } catch (\Throwable $th) {
        header('Content-Type: application/json');
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['status' => 'error', 'message' => 'Failed to get publications: ' . $th->getMessage()]);
        exit;
    }
}
?>