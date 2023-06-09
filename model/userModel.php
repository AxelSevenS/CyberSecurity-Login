<?php

require_once __DIR__.'/../Utils/DB.php';
require_once __DIR__.'/../Utils/userRegisterValidator.php';

class User {
    public int $id;
    public string $email;
    public string $username;
    public string $password;
    public string $salt;
    public ?string $date;

    private function __construct(int $id, string $email, string $username, string $password, ?string $salt = NULL, ?string $date = NULL) {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt ?? User::createSalt();
        $this->date = $date;
    }

    // Verifies that a give password matches the user's password in the database
    // returns true if the password matches, false otherwise
    public function checkPassword(string $password) : bool {
        return $this->password == User::hashPassword($password, $this->salt);
    }

    // Get a user by id
    // returns null if not found
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

    // Get a user by email
    // returns null if not found
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

    // Get a user by username
    // returns null if not found
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

    // Get a user whose email corresponds to the given email or whose username corresponds to the given username
    // returns null if not found
    public static function getUserByUsernameOrEmail(string $email, string $username) : ?User {
        $sql = DB::getPDO()->prepare("SELECT * FROM users WHERE (`email` = :email OR `username` = :username) ");
        $sql->execute( [
            'email' => $email,
            'username' => $username,
        ] );

        if ($sql->rowCount() == 0) {
            return null;
        }

        $array = $sql->fetch();
        return new User($array['id'], $array['email'], $array['username'], $array['password'], $array['salt'], $array['register_date']);
    }

    // Get a user whose email or username corresponds to the given identifier
    // returns null if not found
    public static function getUserByIdentifier(string $identifier) : ?User {
        return User::getUserByUsernameOrEmail($identifier, $identifier);
    }

    // Insert a new user in the database
    // This checks that the credentials are valid (unicity, length, format, etc.)
    // returns the newly created user if successful, an error message otherwise
    public static function insertUser(string $email, string $username, string $password) : User|string {

        $credentialsError = RegisterValidator::verify_credentials($email, $username, $password);
        if ($credentialsError != null) {
            return $credentialsError;
        }

        $salt = User::createSalt();
        $hashedPassword = User::hashPassword($password, $salt);

        $sql = DB::getPDO()->prepare("
            INSERT INTO users (email, username, password, salt) 
            VALUES (:email, :username, :password, :salt)
        ");
        $sql->execute( [
            'email' => $email,
            'username' => $username,
            'password' => $hashedPassword,
            'salt' => $salt,
        ] );

        // return id of the inserted user
        $sql = DB::getPDO()->query("SELECT last_insert_id();");
        return User::getUserById(intval($sql->fetchColumn()));
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