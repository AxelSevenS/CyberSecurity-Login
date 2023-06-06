<?php

require_once __DIR__.'/../Model/userModel.php';
require_once __DIR__.'/../Utils/userRegisterValidator.php';

define('MAX_LOGIN_RETRIES', 5);

class LoginController {


    public static function resolveLogin() {

        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

            $identifier = $_POST['identifier'];
            $password = $_POST['password'];

            $loginUser = self::tryLogin($identifier, $password);
            if ( is_string($loginUser) ) {
                self::loginPage($loginUser);
                return;
            }

            $_SESSION['user'] = $loginUser;
            header('Location: /');
            return;
        }

        self::loginPage();

    }

    public static function loginPage(?string $errorMessage = NULL) {
        ob_start();
        require_once __DIR__.'/../view/auth/loginView.php';
        $content = ob_get_clean();
        $title = "Login Page";
        $error = $errorMessage;

        require_once __DIR__.'/../view/template.php';
    }


    public static function tryLogin(string $identifier, string $password) : string|User {
        $user = User::getUserByIdentifier( $identifier );
        if ( $user == NULL ) {
            return "User not found";
        }

        if ( !$user->checkPassword( $password ) ) {
            $_SESSION['loginRetries'] = ($_SESSION['loginRetries'] ?? MAX_LOGIN_RETRIES) - 1;
            return "Wrong password";
        }


        $user->password = $password;
        $_SESSION['loginRetries'] = MAX_LOGIN_RETRIES;
        $_SESSION['userID'] = $user->id;
        $_SESSION['userName'] = $user->username;
        $_SESSION['userEmail'] = $user->email;
        return $user;
    }

}