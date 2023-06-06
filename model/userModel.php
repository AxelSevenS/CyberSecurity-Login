<?php

require_once __DIR__.'/../dbconnection/DB.php';
require_once __DIR__.'/../Utils/userRegisterValidator.php';

class User {
    public int $id;
    public string $username;
    public string $email;
    public string $password;
    public string $salt;
    public string $date;

    public function __construct(int $id, string $username, string $email, string $password, string $salt, string $date = "") {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->salt = $salt;
        $this->date = $date;
    }


    public function checkPassword(string $password) : bool {
        return $this->password == User::hashPassword($password, $this->salt);
    }


    public static function getUserById(int $id) : ?User {
        
        $sql = DB::getPDO()->prepare("SELECT * FROM users WHERE id = :id");
        $sql->execute( [
            'id' => $id,
        ] );
        

        if ($sql->rowCount() == 0) {
            return null;
        }

        $array = $sql->fetch();
        return new User($array['id'], $array['username'], $array['email'], $array['password'], $array['salt'], $array['register_date']);
    }

    public static function getUserByEmail(string $email) : ?User {
        
        $sql = DB::getPDO()->prepare("SELECT * FROM users WHERE email = :email");
        $sql->execute( [
            'email' => $email,
        ] );
        

        if ($sql->rowCount() == 0) {
            return null;
        }

        $array = $sql->fetch();
        return new User($array['id'], $array['username'], $array['email'], $array['password'], $array['salt'], $array['register_date']);
    }
    public static function getUserByUsername(string $username) : ?User {
        
        $sql = DB::getPDO()->prepare("SELECT * FROM users WHERE username = :username");
        $sql->execute( [
            'username' => $username,
        ] );
        

        if ($sql->rowCount() == 0) {
            return null;
        }

        $array = $sql->fetch();
        return new User($array['id'], $array['username'], $array['email'], $array['password'], $array['salt'], $array['register_date']);
    }

    public static function getUserByUsernameOrEmail(string $username, string $email) : ?User {
        
        $sql = DB::getPDO()->prepare("SELECT * FROM users WHERE (username = :username OR email = :email) ");
        $sql->execute( [
            'username' => $username,
            'email' => $email,
        ] );
        

        if ($sql->rowCount() == 0) {
            return null;
        }

        $array = $sql->fetch();
        return new User($array['id'], $array['username'], $array['email'], $array['password'], $array['salt'], $array['register_date']);
    }

    public static function getUserByIdentifier(string $identifier) : ?User {
        return User::getUserByUsernameOrEmail($identifier, $identifier);
    }


    public static function insertUser(string $username, string $email, string $password) : User {

        $credentialsError = RegisterValidator::verify_credentials($username, $email, $password);
        if ($credentialsError != null) {
            throw new Exception($credentialsError);
        }

        $salt = bin2hex(openssl_random_pseudo_bytes(16));
        $hashedPassword = User::hashPassword($password, $salt);

        $sql = DB::getPDO()->prepare( "INSERT INTO users (username, email, password, salt) VALUES (:username, :email, :password, :salt)" );
        $sql->execute( [
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'salt' => $salt,
        ] );


        $sql = DB::getPDO()->query("SELECT last_insert_id();");
        $id = intval($sql->fetchColumn());
        return User::getUserById($id);
    }


    public static function editUser(int $id, string $username, string $email, string $password) : User {
        $sql = DB::getPDO()->prepare( "UPDATE user SET username = :username, email = :email, password = :password WHERE id = :id" );
        $sql->execute( [
            'id' => $id,
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ] );

        return User::getUserById($id);
    }


    public static function hashPassword(string $password, string $salt) : string {
        return hash_pbkdf2("sha256", $password, $salt, 1000);
    }

    // public static function getUserById(int $userId) : array{
    //     $sql = DB::getPDO()->prepare("SELECT username FROM users WHERE id = :id");
    //     $sql->execute( [
    //         'id' => $userId,
    //     ] );
    

    //     $array = $sql->fetchAll();
    //     $userArray = [];
    //     foreach($array as $arrayUser){
    //         $userArray[] = $arrayUser['username'];
    //     }
    //     if ($sql->rowCount() == 0) {
    //         $userArray[] = null;
    //     }
    //     return $userArray;
    // }
    

    // public static function getUserByIdentifierAndPassword(string $identifier, string $password) : ?User {

    //     $sql = DB::getPDO()->prepare("SELECT * FROM users WHERE (username = :identifier OR email = :identifier) AND password = :password");
    //     $sql->execute( [
    //         'identifier' => $identifier,
    //         'password' => $password
    //     ] );
        

    //     if ($sql->rowCount() == 0) {
    //         return null;
    //     }

    //     $array = $sql->fetch();
    //     return new User($array['id'], $array['username'], $array['email'], $array['password'], $array['register_date']);
    // }
}

?>