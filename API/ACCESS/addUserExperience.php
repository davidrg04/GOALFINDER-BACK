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
            throw new Exception("No Authorization header provided");
        }

        list(, $jwt) = explode(' ', $authHeader);
        $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $id = (string) $jwtDecoded->id;

        $data = json_decode(file_get_contents('php://input'), true);

    
        $newUserExperience = [
            'idExperience' => (string) new MongoDB\BSON\ObjectId(),
            'club' => $data['club'],
            'footballRole' => isset($data['footballRole']) ? $data['footballRole'] : null,
            'position' => isset($data['position']) ? $data['position'] : null,            
            'startDate' => $data['startDate'],
            'endDate' => $data['endDate'],
            'description' => isset($data['description']) ? $data['description'] : "",
        ];

        $usuario = $db->usuarios->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        if (!$usuario) {
            throw new Exception("Usuario no encontrado");
        }

        if (isset($data['isCurrentClub']) && $data['isCurrentClub'] === true) {
            $updateResult = $db->usuarios->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($id)],
                ['$set' => [
                    'club' => $data['club'],
                ]]
            );
        }

        $updateResult = $db->usuarios->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($id)],
            ['$push' => [
                'experience' => $newUserExperience
            ]]
        );

        if ($updateResult->getModifiedCount() == 1) {
            header('Content-Type: application/json');
            header('HTTP/1.1 200 OK');
            echo json_encode(array('message' => 'User experience added successfully'));
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(array('error' => $e->getMessage()));            
        }

    } catch (Exception $e) {
        header('Content-Type: application/json');
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(array('error' => $e->getMessage()));
    }
}
?>