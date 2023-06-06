<?php

// TODO: On ne peux pas utiliser $_SERVER['REMOTE_ADDR'] car on est en local;
// si on veut tester le code sur un serveur, il faut utiliser $_SERVER['REMOTE_ADDR']
define("USER_IP", "192.168.0.1");

class DB {


    private static PDO $connection;

    public static function getPDO(): PDO {
        if (!isset(self::$connection)) {
            $dbname = "SecurityProject-Axel";
            $username = "SecurityProject-Axel";
            $password = "S3KuR1tY!!*1987";
            // create the database if it doesn't exist
            self::$connection = new PDO("mysql:host=localhost", "root", "");
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            self::$connection->exec("
                CREATE DATABASE IF NOT EXISTS `$dbname`;
                CREATE USER IF NOT EXISTS '$username'@'localhost' IDENTIFIED BY '$password';
                GRANT ALL ON `$dbname`.* TO '$username'@'localhost';
                SET GLOBAL event_scheduler = ON;
                FLUSH PRIVILEGES;"
            );
            self::$connection->exec("USE `$dbname`;");


            self::$connection->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    salt VARCHAR(255) NOT NULL,
                    register_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");

            self::$connection->exec("
                CREATE TABLE IF NOT EXISTS loginAttempts (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id INT(6) UNSIGNED NOT NULL,
                    machine_id VARCHAR(255) NOT NULL,
                    remaining_attempts INT(6) UNSIGNED NOT NULL DEFAULT 5,
                    last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ");

            self::$connection->exec("
                CREATE TABLE IF NOT EXISTS passwordResets (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id INT(6) UNSIGNED NOT NULL,
                    token VARCHAR(255) NOT NULL,
                    expiration_date TIMESTAMP NOT NULL
                )
            ");
            
            
            
        }
        return self::$connection;
    }

}

?>