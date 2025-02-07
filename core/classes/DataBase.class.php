<?php 

if (!defined('rootsec')) {
    die('Direct access not permitted');
}

class DataBase {
    private $DBH;
    private $STMT;

    public function __construct($_LANG, $Host, $User, $Pass, $Name) {
        $this->LANG = $_LANG;

        $DSN = 'mysql:host=' . $Host . ';dbname=' . $Name;
        $Options = array(
            PDO::ATTR_EMULATE_PREPARES => true,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );

        try {
            $this->DBH = new PDO($DSN, $User, $Pass, $Options);
        } catch(PDOException $e) {
            throw new Exception($this->LANG["database"]["failedconn"] . $e->getMessage());
        }
    }

    public function Query($Query) {
        try {
            $this->STMT = $this->DBH->prepare($Query);
        } catch(PDOException $e) {
            throw new Exception($this->LANG["database"]["queryerror"] . $e->getMessage());
        }
    }

    public function Bind($Param, $Value, $Type = null) {
        try {
            if (is_null($Type)) {
                $Type = $this->DetectType($Value);
            }
            $this->STMT->bindValue($Param, $Value, $Type);
        } catch(PDOException $e) {
            throw new Exception($this->LANG["database"]["binderror"] . $e->getMessage());
        }
    }

    public function Execute() {
        try {
            return $this->STMT->execute();
        } catch(PDOException $e) {
            throw new Exception($this->LANG["database"]["execerror"] . $e->getMessage());
        }
    }

    public function ResultSet() {
        if (!$this->STMT->rowCount()) {
            $this->Execute();
        }
        return $this->STMT->fetchAll(PDO::FETCH_ASSOC);
    }

    public function Single() {
        if (!$this->STMT->rowCount()) {
            $this->Execute();
        }
        return $this->STMT->fetch(PDO::FETCH_ASSOC);
    }

    public function RowCount() {
        return $this->STMT->rowCount();
    }

    public function LastID() {
        return $this->DBH->lastInsertId();
    }

    public function StartTransaction() {
        return $this->DBH->beginTransaction();
    }

    public function EndTransaction() {
        return $this->DBH->commit();
    }

    public function CancelTransaction() {
        return $this->DBH->rollBack();
    }

    public function Quote($value) {
        return $this->DBH->quote($value);
    }

    public function DebugParams() {
        $output = $this->LANG["database"]["statements"]."\n" . $this->STMT->queryString . "\n";
        $output .= $this->LANG["database"]["binds"]."\n";
        $bindings = $this->STMT->debugDumpParams();
        $output .= str_replace("\n", "\n  ", $bindings);
        return $output;
    }

    private function DetectType($value) {
        if (is_int($value)) {
            return PDO::PARAM_INT;
        } elseif (is_bool($value)) {
            return PDO::PARAM_BOOL;
        } elseif (is_null($value)) {
            return PDO::PARAM_NULL;
        } else {
            return PDO::PARAM_STR;
        }
    }
}
?>