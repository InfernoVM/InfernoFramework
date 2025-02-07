<?php 
if (!defined('rootsec')) {
    die('Direct access not permitted');
}

class SiteConfig {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getSiteName() {
        return $this->fetchSingleField('site_name');
    }

    public function getDomain() {
        return $this->fetchSingleField('domain');
    }

    public function getGoogleRecaptchaSiteKey() {
        return $this->fetchSingleField('google_recaptcha_site_key');
    }

    public function getGoogleRecaptchaSecretKey() {
        return $this->fetchSingleField('google_recaptcha_secret_key');
    }


    private function fetchSingleField($fieldName) {
        try {
            $query = "SELECT $fieldName FROM site_settings LIMIT 1";
            $this->db->Query($query);
            $result = $this->db->Single();
            return $result[$fieldName] ?? null;
        } catch (Exception $e) {
            throw new Exception("Error fetching field '$fieldName': " . $e->getMessage());
        }
    }
}

?>
