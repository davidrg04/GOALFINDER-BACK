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
        if (isset($_FILES['profilePicture'])) {
            $key = "proyectoDavid";
            $headers = apache_request_headers();
            $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

            list(, $jwt) = explode(' ', $authHeader);
            $jwtDecoded = JWT::decode($jwt, new Key($key, 'HS256'));
            $id = (string) $jwtDecoded->id;
            $uniqueId = uniqid();
            $fotoPerfil = basename($_FILES['profilePicture']['name']);
            $extension = pathinfo($fotoPerfil, PATHINFO_EXTENSION);
            $fotoPerfilConUUID = pathinfo($fotoPerfil, PATHINFO_FILENAME) . '-' . $uniqueId . '.' . $extension;
            try {
                $usuario = $db->usuarios->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
                $fotoAntigua = $usuario->profilePicture;
                unlink("./users/user$id/" . $fotoAntigua);

                $collection = $db->usuarios;
                $collection->updateOne(
                    ['_id' => new MongoDB\BSON\ObjectId($id)],
                    ['$set' => ['profilePicture' => $fotoPerfilConUUID]]
                );
                move_uploaded_file($_FILES['profilePicture']['tmp_name'], "./users/user$id/" . $fotoPerfilConUUID);
                $url = "http://localhost/GOALFINDER/src/API/ACCESS/users/user$id/$fotoPerfilConUUID";
                header('Content-Type: application/json');
                header('HTTP/1.1 200 OK');
                echo json_encode(['status' => 'success', 'message' => 'Image uploaded', 'url' => $url]);

            } catch (\Throwable $th) {
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload image: ' . $th->getMessage()]);
                exit;
            }

        }
    }else{
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
        exit;
    }
?>