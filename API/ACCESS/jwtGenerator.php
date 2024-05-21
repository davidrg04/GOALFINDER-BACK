<?php
require "../../../vendor/autoload.php"; 
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;


    function generateJWT($username, $rol, $id, $completeProfile){
        $key = "proyectoDavid";

        $payload = [
            'iat' => time(),
            'rol' => $rol,
            'id' => $id,
            'username' => $username,
            'completeProfile' => $completeProfile,
            'exp' => time()+3600
        ];

        $jwt = JWT::encode($payload, $key, 'HS256');

        return $jwt;

    }
?>