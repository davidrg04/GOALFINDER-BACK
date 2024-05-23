<?php
require "../../vendor/autoload.php";

use MongoDB\Client as MongoClient;

class Conexion {
    private $client;
    private $db;

    public function __construct() {
        try {
            
            $this->client = new MongoClient("mongodb+srv://davidrg:memoriatfg@goalfinder.86b73rt.mongodb.net/?retryWrites=true&w=majority&tls=true&tlsAllowInvalidCertificates=true");
            $this->db = $this->client->goalfinder;
        } catch (Exception $e) {
            error_log($e->getMessage()); 
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    public function getDb() {
        return $this->db;
    }
}
?>
