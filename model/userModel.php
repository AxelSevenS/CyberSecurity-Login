<?php

require_once __DIR__.'/../dbconnection/DB.php';
require_once __DIR__.'/../Utils/userRegisterValidator.php';

class User {
    public int $id;
    public string $email;
    public string $username;
    public string $password;
    public string $salt;
    public ?string $date;

    public function __construct(int $id, string $email, string $username, string $password, ?string $salt = NULL, ?string $date = NULL) {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt ?? User::createSalt();
        $this->date = $date;
    }


    public function checkPassword(string $password) : bool {
        return $this->password == User::hashPassword($password, $this->salt);
    }

    public function getLoginAttempts() : int {
        DB::getPDO()->query("
            DELETE FROM loginAttempts WHERE TIMESTAMPDIFF(MINUTE, last_attempt, NOW()) >= 5;
        ");

        $sql = DB::getPDO()->prepare("
            SELECT remaining_attempts FROM loginAttempts WHERE user_id = :user_id AND machine_id = :machine_id;
        ");
        $sql->execute( [
            'user_id' => $this->id,
            'machine_id' => USER_IP,
        ] );

        if ($sql->rowCount() == 0) {
            return 5;
        }
        return $sql->fetch()["remaining_attempts"];
    }

    public function insert() : int {

        $hashedPassword = User::hashPassword($this->password, $this->salt);

        $sql = DB::getPDO()->prepare("
            INSERT INTO users (email, username, password, salt) 
            VALUES (:email, :username, :password, :salt)
        ");
        $sql->execute( [
            'email' => $this->email,
            'username' => $this->username,
            'password' => $hashedPassword,
            'salt' => $this->salt,
        ] );

        // return id of the inserted user
        $sql = DB::getPDO()->query("SELECT last_insert_id();");
        return intval($sql->fetchColumn());
    }

    public function decrementLoginRetries() : void {
        
        $sql = DB::getPDO()->prepare("
            SELECT remaining_attempts FROM loginAttempts WHERE user_id = :user_id AND machine_id = :machine_id;
        ");
        $sql->execute( [
            'user_id' => $this->id,
            'machine_id' => USER_IP,
        ] );

        if ($sql->rowCount() == 0) {
            $sql = DB::getPDO()->prepare("
                INSERT INTO loginAttempts (user_id, machine_id, remaining_attempts) VALUES (:user_id, :machine_id, :remaining_attempts);
            ");
            $sql->execute( [
                'user_id' => $this->id,
                'machine_id' => USER_IP,
                'remaining_attempts' => 4,
            ] );
        } else {
            $sql = DB::getPDO()->prepare("
                UPDATE loginAttempts SET remaining_attempts = remaining_attempts - 1 WHERE user_id = :user_id AND machine_id = :machine_id AND remaining_attempts > 0;
            ");
            $sql->execute( [
                'user_id' => $this->id,
                'machine_id' => USER_IP,
            ] );
        }
    }

    public function resetLoginRetries() : void {
        $sql = DB::getPDO()->prepare("
            DELETE FROM loginAttempts WHERE user_id = :user_id AND machine_id = :machine_id;
        ");
        $sql->execute( [
            'user_id' => $this->id,
            'machine_id' => USER_IP,
        ] );
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
        return new User($array['id'], $array['email'], $array['username'], $array['password'], $array['salt'], $array['register_date']);
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
        return new User($array['id'], $array['email'], $array['username'], $array['password'], $array['salt'], $array['register_date']);
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
        return new User($array['id'], $array['email'], $array['username'], $array['password'], $array['salt'], $array['register_date']);
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
        return new User($array['id'], $array['email'], $array['username'], $array['password'], $array['salt'], $array['register_date']);
    }

    public static function getUserByIdentifier(string $identifier) : ?User {
        return User::getUserByUsernameOrEmail($identifier, $identifier);
    }


    public static function insertUser(string $email, string $username, string $password) : User {

        $credentialsError = RegisterValidator::verify_credentials($email, $username, $password);
        if ($credentialsError != null) {
            throw new Exception($credentialsError);
        }

        $user = new User(0, $username, $email, $password);
        $userId = $user->insert();
        return User::getUserById($userId);
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
        return hash_pbkdf2("sha512", $password, $salt, 1000);
    }

    public static function createSalt() : string {
        return bin2hex(openssl_random_pseudo_bytes(16));
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
    //     return new User($array['id'], $array['email'], $array['username'], $array['password'], $array['register_date']);
    // }
}

?>