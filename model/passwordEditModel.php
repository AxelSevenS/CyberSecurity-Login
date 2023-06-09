<?php

require_once __DIR__.'/userModel.php';
require_once __DIR__.'/../Utils/DB.php';
require_once __DIR__.'/../Utils/userRegisterValidator.php';

class PasswordEdit {
    public int $id;
    public string $userId;
    public string $code;
    public string $password;
    public ?string $date;

    private function __construct(int $id, string $userId, string $code, string $password, ?string $date = NULL) {
        $this->id = $id;
        $this->userId = $userId;
        $this->code = $code;
        $this->password = $password;
        $this->date = $date;
    }

    // Insert a new password edit
    // returns the new password edit
    public static function insertEdit(User $user, string $newPassword, string $code) : PasswordEdit {
        // Insert new password edit
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
        return PasswordEdit::getEditById(intval($sql->fetchColumn()));
    }

    // Get a password edit by id
    // returns null if not found
    public static function getEditById(int $id) : ?PasswordEdit {
        $sql = DB::getPDO()->prepare("
            SELECT * FROM password_edits WHERE id = :id;
        ");
        $sql->execute( [
            'id' => $id,
        ] );
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        if ( $result == NULL ) {
            return NULL;
        }
        return new PasswordEdit(
            $result['id'],
            $result['user_id'],
            $result['code'],
            $result['password'],
            $result['date'],
        );
    }

    // Get a password edit by user id
    // returns null if not found
    public static function getEditByUserId(string $userId) : ?PasswordEdit {
        $sql = DB::getPDO()->prepare("
            SELECT * FROM password_edits WHERE user_id = :user_id;
        ");
        $sql->execute( [
            'user_id' => $userId,
        ] );
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        if ( $result == NULL ) {
            return NULL;
        }
        return new PasswordEdit(
            $result['id'],
            $result['user_id'],
            $result['code'],
            $result['password'],
            $result['date'],
        );
    }

}

?>