<?php

require_once __DIR__.'/../Model/passwordEditModel.php';
require_once __DIR__.'/accountController.php';

class PasswordEditController {

    public static function ResolveModifyPassword() {
        
        try {
            $newPassword = $_POST['newPassword'];
            $newPasswordConfirm = $_POST['newPasswordConfirm'];
            $oldPassword = $_POST['oldPassword'];
        } catch (Exception $e) {
            define("ERROR_MSG", "Invalid request [".$e->getMessage()."]");
            header('Location: /account');
            return;
        }

        if ( $newPassword !== $newPasswordConfirm ) {
            define("ERROR_MSG", "New Passwords do not match");
            header('Location: /account');
            return;
        }

        if ( session_id() === "" ) {
            session_start();
        }
        $user = User::getUserById( $_SESSION['user']->id );
        if ( $user === NULL ) {
            define("ERROR_MSG", "User not found");
            header('Location: /account');
            return;
        }
        if ( !$user->checkPassword($oldPassword) ) {
            define("ERROR_MSG", "Old Password is incorrect");
            header('Location: /account');
            return;
        }
        // Delete old password edits
        DB::getPDO()->exec("
            DELETE FROM password_edits WHERE TIMESTAMPDIFF(MINUTE, expiration_date, NOW()) >= 5;
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

        // Insert new password edit
        $code = User::createSalt();
        $hashedCode = User::hashPassword($code, $user->salt);

        $hashedPassword = User::hashPassword($newPassword, $user->salt);
        $sql = DB::getPDO()->prepare("
            INSERT INTO password_edits (user_id, code, password)
            VALUES (:user_id, :code, :password);
        ");
        $sql->execute( [
            'user_id' => $user->id,
            'code' => $hashedCode,
            'password' => $hashedPassword,
        ] );

        $sql = DB::getPDO()->query("SELECT last_insert_id();");
        $edit = PasswordEdit::getEditById(intval($sql->fetchColumn()));

        self::sendPasswordEditConfirmationEmail($code, $edit->id);
    }

    public static function sendPasswordEditConfirmationEmail(string $validationCode, int $editId) {
        // TODO: replace this with a real email sending function
        ?>
            <h3>(Pretend this is an email)</h3>
            <p>Your Password was changed successfully, use the following link to validate the change.</p>
            <p><a href="/validatePasswordEdit?id=<?= $editId ?>&code=<?= $validationCode ?>">Validate Password Change</a></p>
        <?php
    }

    public static function ResolveValidatePasswordEdit() {
        $id = $_GET['id'];
        $validationCode = $_GET['code'];

        $passwordEdit = PasswordEdit::getEditById( $id );

        if ( $passwordEdit === NULL ) {
            define("ERROR_MSG", "Password edit not found");
            header('Location: /login');
            return;
        }

        $user = User::getUserById( $passwordEdit->userId );
        if ( $passwordEdit->code !== User::hashPassword($validationCode, $user->salt) ) {
            define("ERROR_MSG", "Invalid validation code");
            header('Location: /login');
            return;
        }

        $sql = DB::getPDO()->prepare("
            UPDATE users SET password = :password WHERE id = :id;
        ");
        $sql->execute( [
            'password' => $passwordEdit->password,
            'id' => $user->id,
        ] );
    }
}