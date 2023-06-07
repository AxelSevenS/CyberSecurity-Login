<?php

require_once __DIR__.'/../Model/userModel.php';
require_once __DIR__.'/../Utils/userRegisterValidator.php';


class RegisterController {

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
            RegisterController::registerPage($user);
            return;
        }

        RegisterController::registerPage();

    }

    public static function registerPage(?string $errorMessage = NULL) {
        ob_start();
        require_once __DIR__.'/../view/auth/registerView.php';
        $content = ob_get_clean();
        $title = "Register Page";
        define("ERROR_MSG", $errorMessage);

        require_once __DIR__.'/../view/template.php';
    }

    public static function tryRegister(?string $email, ?string $username, ?string $password, ?string $confirmPassword) : User|string {

        if ( $email === NULL || $username === NULL || $password === NULL || $confirmPassword === NULL ) {
            return "Missing fields";
        }

        $error_message = RegisterValidator::verify_credentials($email, $username, $password, $confirmPassword);
        if ( $error_message != NULL ) {
            return $error_message;
        }

        $user = User::insertUser($email, $username, $password);
        if ( $user === NULL ) {
            return "Could not register user";
        }
        
        return $user;
    }

    // public static function index() {

    //     if ( isset($_GET['id']) ) {
    //         $user = UserRepository::getUserById($_GET['id']);
    //     } else if ( isset($_SESSION) && isset($_SESSION['userId']) ) {
    //         $user = UserRepository::getUserById($_SESSION['userId']);
    //     } else {
    //         header('Location: /login');
    //         return;
    //     }


    //     ob_start();

    //     require __DIR__.'/../View/user.php';

    //     $page_contents = ob_get_clean();
    //     require __DIR__.'/../View/Template/page-layout.php';
    // }

	// public static function login_page(){

    //     if ( isset($_GET) ) {
            
    //         if ( isset($_GET['id']) && isset($_GET['password']) ) {

    //             $loggedUser = RegisterController::login( $_GET['id'], $_GET['password'] );

    //             if ($loggedUser != null) {
    //                 if (session_status() == PHP_SESSION_ACTIVE){
    //                     session_destroy();
    //                 }

    //                 session_start();
    //                 $_SESSION['userId'] = $loggedUser->id;
    //                 $_SESSION['userName'] = $loggedUser->username;
    //                 $_SESSION['userEmail'] = $loggedUser->email;
    //                 $_SESSION['userPassword'] = $loggedUser->password;
    //                 $_SESSION['userDate'] = $loggedUser->date;

    //                 header('Location: ' . $_SERVER['HTTP_REFERER']);
    //             } else {
    //                 $error_message = "Mot de Passe ou Identifiant incorrect";
    //             }
    //         }
    //     }


    //     ob_start();

	// 	require __DIR__.'/../View/login.php';

    //     $page_contents = ob_get_clean();
    //     require __DIR__.'/../View/Template/page-layout.php';
	// }

	// public static function logout_page(){

    //     session_destroy();
        
    //     header('Location: /');
	// }

	// public static function register_page(){

    //     if ( isset($_POST) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['password-confirm']) ) {

    //         $error_message = RegisterController::tryRegister();
            
    //     }


    //     ob_start();

	// 	require __DIR__.'/../View/register.php';

    //     $page_contents = ob_get_clean();
    //     require __DIR__.'/../View/Template/page-layout.php';

	// }
}

?>