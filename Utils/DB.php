<?php

require_once __DIR__.'/ENV.php';

// TODO: We cannot use $_SERVER['REMOTE_ADDR'] as we are in local;
// When putting this on an actual server, use $_SERVER['REMOTE_ADDR']
define("USER_IP", "192.168.0.1");
// define("USER_IP", $_SERVER['REMOTE_ADDR']);

class DB {


    private static PDO $connection;

    public static function getPDO(): PDO {
        if (!isset(self::$connection)) {
            
            self::$connection = new PDO("mysql:dbname=".ENV::get('DBNAME').";host=localhost", ENV::get('DBUSERNAME'), ENV::get('DBPASSWORD'));
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // try {
            //     self::$connection->exec("
            //     CREATE DATABASE IF NOT EXISTS `".CONF['dbname']."`;
            //     CREATE USER IF NOT EXISTS `".CONF['dbusername']."`@'localhost' IDENTIFIED BY `".CONF['dbpassword']."`;
            //     GRANT ALL ON `".CONF['dbname']."`.* TO `".CONF['dbusername']."`@'localhost';
            //     FLUSH PRIVILEGES;
            //     ");
            //     self::$connection->exec("USE ".CONF['dbname'].";");
            // } catch (Exception $e) {
            //     echo "Error: ".$e->getMessage();
            // }


            // self::$connection->exec("
            //     CREATE TABLE IF NOT EXISTS users (
            //         id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            //         email VARCHAR(255) NOT NULL,
            //         username VARCHAR(255) NOT NULL,
            //         password VARCHAR(255) NOT NULL,
            //         salt VARCHAR(255) NOT NULL,
            //         register_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            //     )
            // ");

            // self::$connection->exec("
            //     CREATE TABLE IF NOT EXISTS login_attempts (
            //         id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            //         user_id INT(6) UNSIGNED NOT NULL,
            //         machine_id VARCHAR(255) NOT NULL,
            //         remaining_attempts INT(6) UNSIGNED NOT NULL DEFAULT 5,
            //         last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            //     )
            // ");

            // self::$connection->exec("
            //     CREATE TABLE IF NOT EXISTS password_edits (
            //         id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            //         user_id INT(6) UNSIGNED NOT NULL,
            //         code VARCHAR(255) NOT NULL,
            //         password VARCHAR(255) NOT NULL,
            //         date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            //     )
            // ");
            
            
            
        }
        return self::$connection;
    }

}

?>