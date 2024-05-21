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

if($_SERVER["REQUEST_METHOD"] == "POST") {
    try{
        $key = "proyectoDavid";
        $headers = apache_request_headers();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if(!$authHeader){
            throw new Exception("No Authorization header provided");
        }

        list(, $jwt) = explode(' ', $authHeader);
        $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $id = (string) $jwtDecoded->id;
        $idPublication = (string) new MongoDB\BSON\ObjectId();
        if(isset($_FILES['file'])){
            $uniqueId = uniqid();
            $ficheroPublicacion = basename($_FILES['file']['name']);
            $extension = pathinfo($ficheroPublicacion, PATHINFO_EXTENSION);
            $ficheroPublicacionConUUID = pathinfo($ficheroPublicacion, PATHINFO_FILENAME) . '-' . $uniqueId . '.' . $extension;
            if (!is_dir("../ACCESS/users/user$id/publications")) {
                mkdir("../ACCESS/users/user$id/publications", 0755, true);
            }
            mkdir("../ACCESS/users/user$id/publications/".$idPublication, 0755, true);

            move_uploaded_file($_FILES['file']['tmp_name'], "../ACCESS/users/user$id/publications/".$idPublication."/" . $ficheroPublicacionConUUID);
        
            if ($extension === 'mp4') {
                $mediaType = 'video';
            } elseif (in_array($extension, ['jpeg', 'png'])) {
                $mediaType = 'photo';
            }
        }

        $user = $db->usuarios->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        
        $newPublication = [
            'idPublication' => $idPublication,
            'idUser' => $id,
            'username' => $user->username,
            'completeName' => $user->name . ' ' . $user->surnames,
            'description' => $_POST['content'],
            'file' => isset($ficheroPublicacion) ? $ficheroPublicacionConUUID : null,
            'date' => date('d-m-Y'),
            'likes' => [],
            'comments' => [],
            'mediaType' => isset($mediaType) ? $mediaType : null
        ];

        $insertOneResult = $db->publicaciones->insertOne($newPublication);

        header('Content-Type: application/json');
        header('HTTP/1.1 201 Created');
        echo json_encode(array('message' => 'Publication created successfully'));

    }catch(Exception $e){
        header('Content-Type: application/json');
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(array('error' => $e->getMessage()));
    }
}

?>