    <?php
        require_once('../CONEXION/conexion.php');

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

        $data = json_decode(file_get_contents("php://input"), true);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $data['name'];
            $surnames = $data['surnames'];
            $username = $data['username'];
            $email = $data['email'];
            $password = $data['password'];
            $role = $data['role'];
            

            $collection = $db->usuarios;

            $emailExists = $collection->findOne(['email' => $email]);
            $hashPassword = password_hash($password, PASSWORD_BCRYPT);
            if ($emailExists) {
                header('Content-Type: application/json');
                header('HTTP/1.1 409 Conflict');
                echo json_encode(['error' => 'email']);
                exit;
            }

            $usernameExists = $collection->findOne(['username' => $username]);
            if ($usernameExists) {
                header('Content-Type: application/json');
                header('HTTP/1.1 409 Conflict');
                echo json_encode(['error' => 'username']);
                exit;
            }

            $newUser = [
                'name' => $name,
                'surnames' => $surnames,
                'username' => $username,
                'email' => $email,
                'password' => $hashPassword,
                'role' => $role,
                'followers' => [],
                'following' => [],
                'inscribes' => [],
                'footballRole' => '',
                'position' => '', 
                'club' => '',
                'city' => '',
                'community' => '',
                'birthDate' => '', 
                'experience' => [] ,
                'profilePicture' => 'perfil.png',
                'completeProfile' => false,
                'save' => [], 
                'description' => '',
            ];

            if ($role == 'club') {
                $newUser['offers'] = [];
            }

            $result = $collection->insertOne($newUser);

            $userId = $result->getInsertedId();
            $userCarpeta = './users/user' . $userId;

            if (!file_exists($userCarpeta)) {
                mkdir($userCarpeta, 0777, true);
                mkdir($userCarpeta . "/photo", 0755, true);
                $fotoPredeterminada = "./users/perfil.png";
                $fotoDestino = $userCarpeta . "/perfil.png";
                copy($fotoPredeterminada, $fotoDestino);
            }

            if ($result->getInsertedCount() == 1) {
                header('Content-Type: application/json');
                header("HTTP/1.1 201 Created"); 
                echo json_encode(['message' => 'Usuario registrado con éxito.']);
            } else {
                header('Content-Type: application/json');
                header("HTTP/1.1 500 Internal Server Error"); 
                echo json_encode(['error' => 'Error al registrar el usuario.']);
            }
            
        }

    ?>