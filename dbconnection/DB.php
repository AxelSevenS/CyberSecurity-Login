<?php

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

            self::$connection->exec("CREATE DATABASE IF NOT EXISTS `$dbname`;
                CREATE USER IF NOT EXISTS '$username'@'localhost' IDENTIFIED BY '$password';
                GRANT ALL ON `$dbname`.* TO '$username'@'localhost';
                FLUSH PRIVILEGES;");
            self::$connection->exec("USE `$dbname`;");

            // create users table with the columns id, username, email, password, salt, register_date
            self::$connection->exec("CREATE TABLE IF NOT EXISTS users (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                salt VARCHAR(255) NOT NULL,
                register_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");

            // self::$connection->exec("CREATE TABLE IF NOT EXISTS loginToken (
            //     id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            //     selector VARCHAR(255) NOT NULL,
            //     token VARCHAR(255) NOT NULL,
            //     user_id INT(6) UNSIGNED NOT NULL,
            //     expires TIMESTAMP NOT NULL,
            //     FOREIGN KEY (user_id) REFERENCES users(id)
            // )");
            

            
        }
        return self::$connection;
    }

}

?>