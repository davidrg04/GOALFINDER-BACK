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
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}


if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
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

        $idExperience = $data['idExperience'];

        $usuario = $db->usuarios->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        if (!$usuario) {
            throw new Exception("Usuario no encontrado");
        }

        $deleteResult = $db->usuarios->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$pull' => ['experience' => ['idExperience' => $idExperience]]]
        );

        if ($deleteResult->getModifiedCount() === 0) {
            throw new Exception("No se ha eliminado ninguna experiencia");
        }

        header('Content-Type: application/json');
        header('HTTP/1.1 200 OK');
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => $e->getMessage()]);
    }
}

?>