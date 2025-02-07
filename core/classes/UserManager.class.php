<?php
if (!defined('rootsec')) {
    die('Direct access not permitted');
}

class UserManager {
    private $db;
    private $recaptchaSecret;
    private $lang;

    public function __construct($db, $lang, $recaptchaSecret) {
        $this->db = $db;
        $this->LANG = $lang;
        $this->recaptchaSecret = $recaptchaSecret;
    }

    private function verifyRecaptcha($recaptchaResponse) {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $this->recaptchaSecret,
            'response' => $recaptchaResponse
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result, true);

        return isset($response['success']) && $response['success'];
    }

    public function register($username, $email, $password, $recaptchaResponse) {

        if (!$this->verifyRecaptcha($recaptchaResponse)) {
            return ['success' => false, 'message' => $this->LANG["general"]["invcaptcha"]];
            die();
        }
    
        if (empty($username) || empty($email) || empty($password) || empty($recaptchaResponse)) {
            return ['success' => false, 'message' => $this->LANG["general"]["empty"]];
            die();
        }

        if (strlen($password) <= 5) {
            return ['success' => false, 'message' => $this->LANG["general"]["passlen"]];
            die();
        }
    
        $this->db->Query("SELECT * FROM users WHERE username = :username OR email = :email");
        $this->db->Bind(':username', $username);
        $this->db->Bind(':email', $email);
        
        $result = $this->db->Single();
    
        if ($result) {
            if ($result['username'] === $username) {
                return ['success' => false, 'message' => $this->LANG["general"]["username_taken"]];
            }
            if ($result['email'] === $email) {
                return ['success' => false, 'message' => $this->LANG["general"]["email_taken"]];
            }
            die();
        }
    
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
        try {
            $this->db->Query("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $this->db->Bind(':username', $username);
            $this->db->Bind(':email', $email);
            $this->db->Bind(':password', $hashedPassword);
    
            $success = $this->db->Execute();
            
            return ['success' => $success, 'message' => $success ? $this->LANG["general"]["regsuc"] : $this->LANG["general"]["regfail"]];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $this->LANG["general"]["some"]];
        }
    }
    

    public function login($email, $password, $recaptchaResponse) {
        if (!$this->verifyRecaptcha($recaptchaResponse)) {
            return ['success' => false, 'message' => $this->LANG["general"]["invcaptcha"]];
        }

        try {
            $this->db->Query("SELECT * FROM users WHERE email = :email");
            $this->db->Bind(':email', $email);
            $user = $this->db->Single();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                return ['success' => true, 'message' => $this->LANG["general"]["loginsuc"]];
            } else {
                return ['success' => false, 'message' => $this->LANG["general"]["invemail"]];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $this->LANG["general"]["some"]];
        }
    }

    public function forgotPassword($email, $recaptchaResponse) {
        if (!$this->verifyRecaptcha($recaptchaResponse)) {
            return ['success' => false, 'message' => $this->LANG["general"]["invcaptcha"]];
        }

        try {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $this->db->Query("UPDATE users SET reset_token = :token, token_expiry = :expiry WHERE email = :email");
            $this->db->Bind(':token', $token);
            $this->db->Bind(':expiry', $expiry);
            $this->db->Bind(':email', $email);

            if ($this->db->Execute()) {
                return ['success' => true, 'message' => $this->LANG["general"]["pwreset"], 'token' => $token];
            }

            return ['success' => false, 'message' => $this->LANG["general"]["pwresettoken"]];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $this->LANG["general"]["some"]];
        }
    }

    public function resetPassword($token, $newPassword) {
        try {
            $this->db->Query("SELECT * FROM users WHERE reset_token = :token AND token_expiry > :now");
            $this->db->Bind(':token', $token);
            $this->db->Bind(':now', date('Y-m-d H:i:s'));

            $user = $this->db->Single();

            if ($user) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                $this->db->Query("UPDATE users SET password = :password, reset_token = NULL, token_expiry = NULL WHERE id = :id");
                $this->db->Bind(':password', $hashedPassword);
                $this->db->Bind(':id', $user['id']);

                return $this->db->Execute();
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updatePassword($userId, $oldPassword, $newPassword) {
        try {
            $this->db->Query("SELECT * FROM users WHERE id = :id");
            $this->db->Bind(':id', $userId);

            $user = $this->db->Single();

            if ($user && password_verify($oldPassword, $user['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                $this->db->Query("UPDATE users SET password = :password WHERE id = :id");
                $this->db->Bind(':password', $hashedPassword);
                $this->db->Bind(':id', $userId);

                return $this->db->Execute();
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateEmail($userId, $oldEmail, $newEmail) {
        try {
            $this->db->Query("SELECT * FROM users WHERE id = :id");
            $this->db->Bind(':id', $userId);
    
            $user = $this->db->Single();
    
            if ($user && $user['email'] === $oldEmail) {
                $this->db->Query("UPDATE users SET email = :email WHERE id = :id");
                $this->db->Bind(':email', $newEmail);
                $this->db->Bind(':id', $userId);
    
                return $this->db->Execute();
            }
    
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateTheme($userId, $theme) {
        try {
            if (!in_array($theme, ['light', 'dark'])) {
                return false;
            }
    
            $this->db->Query("UPDATE users SET theme = :theme WHERE id = :id");
            $this->db->Bind(':theme', $theme);
            $this->db->Bind(':id', $userId);
    
            return $this->db->Execute();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getUser($userId) {
        try {
            $this->db->Query("SELECT id, username, email FROM users WHERE id = :id");
            $this->db->Bind(':id', $userId);

            return $this->db->Single();
        } catch (Exception $e) {
            return null;
        }
    }

    public function isAdmin() {        
        $this->db->Query("SELECT rank FROM users WHERE id = :user_id LIMIT 1");
        $this->db->Bind(':user_id', $_SESSION["user_id"]);
        
        if ($this->db->Execute()) {
            $result = $this->db->Single();
            
            if (!$result || $result['rank'] != 100 ) {
                die();
            }
        }
    }

    public function isUserLoggedIn() {
        if(!isset($_SESSION['user_id'])) {
            die();
        }
    }

}
