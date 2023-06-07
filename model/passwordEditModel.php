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

    public function validatePasswordEdit(string $code, int $editId) : ?string {
        $user = User::getUserById($this->userId);
        if ( $user == NULL ) {
            return "User not found";
        }

        if ( User::hashPassword($code, $user->salt) == $this->code ) {
            return NULL;
        }

    }

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
            $result['expiration_date'],
        );
    }

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
            $result['expiration_date'],
        );
    }

    // public static function modifyPassword(User $user, string $newPassword) : ?string {

    //     // Delete old password edits
    //     DB::getPDO()->exec("
    //         DELETE FROM password_edits WHERE TIMESTAMPDIFF(MINUTE, expiration_date, NOW()) >= 5;
    //     ");
        
    //     // // Check if the user has already changed his password in the last 5 minutes
    //     // $sql = DB::getPDO()->prepare("
    //     //     SELECT * FROM password_edits WHERE user_id = :user_id AND TIMESTAMPDIFF(MINUTE, date, NOW()) < 5;
    //     // ");
    //     // $sql->execute( [
    //     //     'user_id' => $this->id,
    //     // ] );

    //     // if ( $sql->rowCount() > 0 ) {
    //     //     return "You have already changed your password in the last 5 minutes";
    //     // }

    //     // Insert new password edit
    //     $code = User::createSalt();
    //     $hashedCode = User::hashPassword($code, $user->salt);
    //     $hashedPassword = User::hashPassword($user->password, $user->salt);
    //     $sql = DB::getPDO()->prepare("
    //         INSERT INTO password_edits (user_id, code, password)
    //         VALUES (:user_id, :code, :password);
    //     ");
    //     $sql->execute( [
    //         'user_id' => $user->id,
    //         'code' => $hashedCode,
    //         'password' => $hashedPassword,
    //     ] );
    //     return $code;
    // }

}

?>