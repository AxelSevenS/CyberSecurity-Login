<?php

require_once __DIR__.'/../Model/userModel.php';

define('MAX_LOGIN_TRIES', 5);

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
        define("ERROR_MSG", $errorMessage);

        require_once __DIR__.'/../view/template.php';
    }


    public static function tryLogin(string $identifier, string $password) : string|User {

        $user = User::getUserByIdentifier( $identifier );
        if ( $user == NULL ) {
            return "User not found";
        }

        if ( $user->getLoginAttempts() <= 0 ) {
            return "Too many login attempts";
        }

        if ( !$user->checkPassword( $password ) ) {
            $user->decrementLoginAttempts();
            return "Wrong password";
        }

        $user->resetLoginAttempts();


        $user->password = $password;
        if ( session_id() === "" ) {
            session_start();
        }
        $_SESSION['user'] = $user;
        return $user;
    }

}