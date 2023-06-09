<?php

require_once __DIR__.'/accountController.php';
require_once __DIR__.'/../Model/userModel.php';
require_once __DIR__.'/../Model/loginAttemptModel.php';
require_once __DIR__.'/../Utils/JWT.php';

class LoginController {

    // Prepare the page for handling requests
    // If the request is a POST request, try to login the user
    // If the request is a GET request, show the login page
    public static function resolveLogin() {

        if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

            AccountController::logout();

            $identifier = $_POST['identifier'];
            $password = $_POST['password'];
            $remember = isset($_POST['rememberMe']);

            $loginUser = self::tryLogin($identifier, $password, $remember);
            if ( is_string($loginUser) ) {
                // define("ERROR_MSG", $loginUser);
                $_GET['error'] = $loginUser;
                self::loginPage();
                return;
            }

            header("Location: /");
            exit;
        }

        self::loginPage();

    }

    // Show the login page
    // Display if the user is logged in or not
    // Display if the user has too many login attempts
    public static function loginPage() {
        $loggedMessage = JWT::getLoggedMessage();
        if ( $loggedMessage !== NULL ) {
            define("LOGGED_MSG", $loggedMessage);
        }

        ob_start();
        require_once __DIR__.'/../view/auth/loginView.php';


        $content = ob_get_clean();
        $title = "Login Page";

        require_once __DIR__.'/../view/template.php';
    }

    // Try to login the user
    // an error message is returned if the login failed
    // the user is returned if the login succeeded
    public static function tryLogin(string $identifier, string $password, bool $remember) : string|User {


        $user = User::getUserByIdentifier( $identifier );
        if ( $user == NULL ) {
            return "User not found";
        }

        if ( LoginAttempt::getLoginAttempts($user->id, USER_IP) <= 0 ) {
            return "Too many login attempts";
        }

        if ( !$user->checkPassword( $password ) ) {
            LoginAttempt::decrementLoginAttempts($user->id, USER_IP);
            return "Wrong password";
        }

        LoginAttempt::resetLoginAttempts($user->id, USER_IP);


        $token = JWT::generateJWT($user)->stringify();
        if ( $remember ) {
            setcookie('token', $token, time() + (86400 * 30), "/"); // expires in 30 days
        } else {
            setcookie('token', $token, 0, "/"); // expires at end of session
        }
        return $user;
    }

}