<?php
class User {
    private $m_sEmail;
    private $m_sName;
    private $m_sPassword;
    public function __set($p_sProperty, $p_vValue)
    {
        switch ($p_sProperty){
            case "Email":
                $this->m_sEmail = $p_vValue;
                break;
            case "Name":
                $this->m_sName = $p_vValue;
                break;
            case "Password":
                $this->m_sPassword = $p_vValue;
                break;
        }
    }
    public function __get($p_sProperty)
    {
        switch ($p_sProperty) {
            case "Email":
                return $this->m_sEmail;
                break;
            case "Name":
                return $this->m_sName;
                break;
            case "Password":
                return $this->m_sPassword;
                break;
        }
    }
    public function canLogin() {
        if(!empty($this->m_sEmail) && !empty($this->m_sPassword)){

            $PDO =  Db::getInstance();
            $stmt = $PDO->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindValue(":email", $this->m_sEmail, PDO::PARAM_STR );
            $stmt->execute();

            if($stmt->rowCount() > 0){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $password = $this->m_sPassword;
                $hash = $result['password'];

                if(password_verify($password, $hash)) {
                    $this->createSession($result['user_id']);
                    return true;
                } else{
                    return false;
                }
            } else {
                return false;
            }
        }
    }
    public function Register() {
        if(!empty($this->m_sEmail) && !empty($this->m_sPassword) && !empty($this->m_sName)){
            $PDO = Db::getInstance();
            $statement = $PDO->prepare("INSERT INTO users (email, password, name) values (:email, :password, :name)");
            $options = [ 'cost' => 12];
            $password = password_hash($this->m_sPassword, PASSWORD_DEFAULT, $options);
            $statement->bindValue(":email", $this->m_sEmail);
            $statement->bindValue(":password", $password);
            $statement->bindValue(":name", $this->m_sName);
            $statement->execute();
        }
        else {
            throw new Exception("Please fill in all fields");
        }
    }

    private function createSession($id) {
        session_start();
        $_SESSION["user_id"] = $id;
    }
}
