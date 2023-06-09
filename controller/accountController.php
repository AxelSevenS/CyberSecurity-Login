<?php

require_once __DIR__.'/../model/userModel.php';
require_once __DIR__.'/../Utils/JWT.php';


class AccountController {

    // Prepare the page for handling requests
    // If the user is logged in, show the account page and allow for password modification
    // If the user is not logged in, redirect to the login page
    public static function resolveAccount() {

        if ( $_SERVER['REQUEST_METHOD'] !== 'GET' ) {
            return;
        }

        
        if ( !isset($_COOKIE['token']) ) {
            // define("ERROR_MSG", "You are not logged in");
            header('Location: /login?error=You are not logged in');
            exit;
        }

        self::accountPage();

    }

    // Show the account page
    // Allow for password modification
    public static function accountPage() {

        $token = JWT::resolveTokenValidity();
        if ( $token == NULL ) {
            // define("ERROR_MSG", "Invalid Token");
            header('Location: /login?error=Invalid Token');
            exit;
        }

        $payload = $token->getDecodedPayload();

        ob_start();
        require_once __DIR__.'/../view/auth/accountView.php';

        
        $content = ob_get_clean();
        $title = "Account Page";

        require_once __DIR__.'/../view/template.php';
    }

    public static function resolveLogout() {
        self::logout();
        header('Location: /login');
        exit;
    }

    // Logout the user and then redirect to the login page
    public static function logout() {
        unset($_COOKIE['token']); 
        setcookie('token', null, -1, '/'); 
    }

}