<?php 
if (!defined('rootsec')) {
    die('Direct access not permitted');
}

class Helper {
    private $db; 

    public function __construct($db) {
        $this->db = $db;
    }

    public function sendResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    public function sanitizeInput($data) {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    public function sanitizeRequestData($data) {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = $this->sanitizeInput($value);
            } elseif (is_array($value)) {
                $data[$key] = $this->sanitizeRequestData($value);
            } else {
                $data[$key] = null;
            }
        }
        return $data;
    }

    public function currentDomain() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $domain = $_SERVER['HTTP_HOST'];
        
        return $protocol."://".$domain;
    }

    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
?>