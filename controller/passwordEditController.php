<?php

require_once __DIR__.'/../Model/passwordEditModel.php';
require_once __DIR__.'/accountController.php';
require_once __DIR__.'/../Utils/JWT.php';

class PasswordEditController {

    // Handle a Password Edit request
    public static function resolveModifyPassword() {

        if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
            // define("ERROR_MSG", "Invalid request method");
            header("Location: /account?error=Invalid request method");
            exit;
        }
        if ( !isset($_POST['newPassword']) || !isset($_POST['newPasswordConfirm']) || !isset($_POST['oldPassword']) ) {
            // define("ERROR_MSG", "Invalid request parameters");
            header("Location: /account?error=Invalid request parameters");
            exit;
        }
        $newPassword = $_POST['newPassword'];
        $newPasswordConfirm = $_POST['newPasswordConfirm'];
        $oldPassword = $_POST['oldPassword'];

        $error = self::modifyPassword($newPassword, $newPasswordConfirm, $oldPassword);
        if ( $error !== NULL ) {
            // define("ERROR_MSG", $error);
            header("Location: /account?error=$error");
            exit;
        }
        
    }

    // Create a password edit request, waiting for validation
    public static function modifyPassword(string $newPassword, string $newPasswordConfirm, string $oldPassword) : ?string {

        if ( $newPassword !== $newPasswordConfirm ) {
            return "New Passwords do not match";
        }
        
        $token = JWT::resolveTokenValidity();
        if  ( $token === NULL ) {
            return "Invalid token";
        }

        $user = User::getUserById( $token->getDecodedPayload()['sub'] );
        if ( $user === NULL ) {
            return "User not found";
        }
        if ( !$user->checkPassword($oldPassword) ) {
            return "Old Password is incorrect";
        }


        // Delete old password edits
        DB::getPDO()->exec("
            DELETE FROM password_edits WHERE TIMESTAMPDIFF(MINUTE, date, NOW()) >= 5;
        ");
        
        // Check if the user has already changed his password in the last 5 minutes
        $sql = DB::getPDO()->prepare("
            SELECT * FROM password_edits WHERE user_id = :user_id AND TIMESTAMPDIFF(MINUTE, date, NOW()) < 5;
        ");
        $sql->execute( [
            'user_id' => $user->id,
        ] );

        if ( $sql->rowCount() > 0 ) {
            return "You have already changed your password in the last 5 minutes";
        }

        $code = User::createSalt();
        $edit = PasswordEdit::insertEdit( $user, $newPassword, $code );

        self::sendPasswordEditConfirmationEmail($code, $edit->id);

        return NULL;
    }

    // Send an email to the user with a link to validate the password edit
    // (This is just a placeholder, it doesn't actually send an email; it just prints a link to the page)
    public static function sendPasswordEditConfirmationEmail(string $validationCode, int $editId) {
        // TODO: replace this with a real email sending function
        ?>
            <h3>(Pretend this is an email)</h3>
            <p>Your Password was changed successfully, use the following link to validate the change.</p>
            <p><a href="/validatePasswordEdit?id=<?= $editId ?>&code=<?= $validationCode ?>">Validate Password Change</a></p>
        <?php
    }

    // Handle a Password Edit validation request
    public static function resolveValidatePasswordEdit() {
        $id = $_GET['id'];
        $validationCode = $_GET['code'];

        $passwordEdit = PasswordEdit::getEditById( $id );

        if ( $passwordEdit === NULL ) {
            // define("ERROR_MSG", "Invalid password edit");
            header('Location: /login?error=Invalid password edit');
            exit;
        }

        $user = User::getUserById( $passwordEdit->userId );
        if ( $passwordEdit->code !== User::hashPassword($validationCode, $user->salt) ) {
            // define("ERROR_MSG", "Invalid validation code");
            header('Location: /login?error=Invalid validation code');
            exit;
        }

        $sql = DB::getPDO()->prepare("
            UPDATE users SET password = :password WHERE id = :id;
        ");
        $sql->execute( [
            'password' => $passwordEdit->password,
            'id' => $user->id,
        ] );

        $sql = DB::getPDO()->prepare("
            DELETE FROM password_edits WHERE id = :id;
        ");
        $sql->execute( [
            'id' => $id,
        ] );

        AccountController::logout();
        header('Location: /login');
        exit;
    }
}