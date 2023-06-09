<?php

require_once __DIR__.'/../Model/userModel.php';
require_once __DIR__.'/../Utils/userRegisterValidator.php';
require_once __DIR__.'/../Utils/JWT.php';


class RegisterController {

    // Prepare the register page for handling requests
    // If the request is a POST request, try to register the user
    // If the request is a GET request, show the register page
    public static function resolveRegister() {
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

            $email = $_POST['email'] ?? NULL;
            $username = $_POST['username'] ?? NULL;
            $password = $_POST['password'] ?? NULL;
            $passwordConfirm = $_POST['confirmPassword'] ?? NULL;

            $user = RegisterController::tryRegister($email, $username, $password, $passwordConfirm);
            if ( $user instanceof User ) {
                header('Location: /login?email='.$_POST['email']);
                return;
            }
            // define("ERROR_MSG", $user);
            $_GET['error'] = $user;
            RegisterController::registerPage();
            exit;
        }

        RegisterController::registerPage();

    }

    // Show the register page
    // Display if the user is logged in or not
    public static function registerPage() {
        $loggedMessage = JWT::getLoggedMessage();
        if ( $loggedMessage !== NULL ) {
            define("LOGGED_MSG", $loggedMessage);
        }

        ob_start();
        require_once __DIR__.'/../view/auth/registerView.php';

        $content = ob_get_clean();
        $title = "Register Page";

        require_once __DIR__.'/../view/template.php';
    }

    // Try to register the user
    // an error message is returned if the registration failed
    // the user is returned if the registration succeeded
    public static function tryRegister(?string $email, ?string $username, ?string $password, ?string $confirmPassword) : User|string {

        if ( $email === NULL || $username === NULL || $password === NULL || $confirmPassword === NULL ) {
            return "Missing fields";
        }

        $error_message = RegisterValidator::verify_credentials($email, $username, $password, $confirmPassword);
        if ( $error_message != NULL ) {
            return $error_message;
        }

        return User::insertUser($email, $username, $password); // returns User or error message
    }
}

?>