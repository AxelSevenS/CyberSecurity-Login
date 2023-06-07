<?php

require_once __DIR__.'/../Model/userModel.php';

define('MAX_LOGIN_TRIES', 5);

class AccountController {


    public static function resolveAccount() {

        if ( $_SERVER['REQUEST_METHOD'] !== 'GET' ) {
            return;
        }
        if ( session_id() === "" ) {
            session_start();
        }

        
        if ( !isset($_SESSION['user']) ) {
            define("ERROR_MSG", "You are not logged in");
            header('Location: /login');
            return;
        }

        self::accountPage();

    }

    public static function accountPage(?string $errorMessage = NULL) {
        ob_start();
        require_once __DIR__.'/../view/auth/accountView.php';
        $content = ob_get_clean();
        $title = "Account Page";
        define("ERROR_MSG", $errorMessage);

        require_once __DIR__.'/../view/template.php';
    }

    public static function ResolveLogout() {
        if ( session_id() === "" ) {
            session_start();
        }
        unset($_SESSION['user']);
        header('Location: /login');
    }
    

    public static function tryLogin(string $identifier, string $password) : string|User {

        if(session_id() === "") {
            session_start();
        }
        if ( isset($_SESSION['loginRetries']) && $_SESSION['loginRetries'] <= 0 ) {
            return "Too many login retries";
        }

        $user = User::getUserByIdentifier( $identifier );
        if ( $user == NULL ) {
            return "User not found";
        }

        if ( !$user->checkPassword( $password ) ) {
            $_SESSION['loginRetries'] = ($_SESSION['loginRetries'] ?? MAX_LOGIN_TRIES) - 1;
            return "Wrong password";
        }


        $user->password = $password;
        $_SESSION['loginRetries'] = MAX_LOGIN_TRIES;
        $_SESSION['userID'] = $user->id;
        $_SESSION['userName'] = $user->username;
        $_SESSION['userEmail'] = $user->email;
        return $user;
    }

}