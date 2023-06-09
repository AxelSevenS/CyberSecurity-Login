<?php

class RegisterValidator {
    
    // Verify the email format and length
    public static function verify_email(string $email): ?string {

        if (strlen($email) > 255) {
            return "Email must be less than 255 characters long";
        }

        if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            return "Email is not valid : ".$email;
        }
        
        return NULL;
    }
    
    // Verify the password format and length
    public static function verify_password(string $password): ?string{

        if (strlen($password) < 8) {
            return "Password must be at least 8 characters long";
        }

        $upperCase = '/[A-Z]/';
        if ( !preg_match($upperCase, $password) ) {
            return "Password must contain at least one uppercase letter";
        }

        $lowerCase = '/[a-z]/';
        if ( !preg_match($lowerCase, $password) ) {
            return "Password must contain at least one lowercase letter";
        }

        $special = '/[\/\'^£$%&*()}{@#~?><>,|=_+¬-]/';
        if ( !preg_match($special, $password) ) {
            return "Password must contain at least one special character";
        }

        return NULL;
    }

    // verify that the username is not already in use
    public static function verify_username_unicity(string $username): ?string {
        if (User::getUserByUsername($username) != NULL) {
            return "Username is already in use";
        }

        return NULL;
    }

    // verify that the email is not already in use
    public static function verify_email_unicity(string $email): ?string {
        if (User::getUserByEmail($email) != NULL) {
            return "Email is already in use";
        }

        return NULL;
    }

    // verify that the credentials are valid
    public static function verify_credentials(string $email, string $username, string $password, ?string $confirmPassword = NULL) : ?string {

        $emailError = RegisterValidator::verify_email($email);
        if ( $emailError != NULL ) {
            return $emailError;
        }

        $emailError = RegisterValidator::verify_email_unicity($email);
        if ( $emailError != NULL ) {
            return $emailError;
        }

        $usernameError = RegisterValidator::verify_username_unicity($username);
        if ( $usernameError != NULL ) {
            return $usernameError;
        }

        // Do not check password, it is verified and hashed on the client side
        // $passwordError = RegisterValidator::verify_password($password);
        // if ( $passwordError != NULL ) {
        //     return $passwordError;
        // }

        if ( $confirmPassword !== NULL && $password !== $confirmPassword ) {
            return "Passwords do not match";
        }
        
        return NULL;
    }

}


?>