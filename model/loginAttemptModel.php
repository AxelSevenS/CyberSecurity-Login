<?php


class LoginAttempt {

    const MAX_ATTEMPTS = 5;
    
    // Get the number of login attempts remaining for a user
    // If the user has no login attempts, the default (MAX_ATTEMPTS) is returned
    public static function getLoginAttempts(int $userId, string $machineId) : int {
        DB::getPDO()->query("
            DELETE FROM login_attempts WHERE TIMESTAMPDIFF(MINUTE, last_attempt, NOW()) >= 5;
        ");

        $sql = DB::getPDO()->prepare("
            SELECT remaining_attempts FROM login_attempts WHERE user_id = :user_id AND machine_id = :machine_id;
        ");
        $sql->execute( [
            'user_id' => $userId,
            'machine_id' => $machineId,
        ] );

        if ($sql->rowCount() == 0) {
            return self::MAX_ATTEMPTS;
        }
        return $sql->fetch()["remaining_attempts"];
    }

    // Decrement the number of login attempts remaining for a user
    // If the user has no login attempts, a new row is created with MAX_ATTEMPTS - 1
    public static function decrementLoginAttempts(int $userId, string $machineId) : void {
        
        $sql = DB::getPDO()->prepare("
            SELECT remaining_attempts FROM login_attempts WHERE user_id = :user_id AND machine_id = :machine_id;
        ");
        $sql->execute( [
            'user_id' => $userId,
            'machine_id' => $machineId,
        ] );

        if ($sql->rowCount() == 0) {
            $sql = DB::getPDO()->prepare("
                INSERT INTO login_attempts (user_id, machine_id, remaining_attempts) VALUES (:user_id, :machine_id, :remaining_attempts);
            ");
            $sql->execute( [
                'user_id' => $userId,
                'machine_id' => $machineId,
                'remaining_attempts' => self::MAX_ATTEMPTS - 1,
            ] );
        } else {
            $sql = DB::getPDO()->prepare("
                UPDATE login_attempts SET remaining_attempts = remaining_attempts - 1 WHERE user_id = :user_id AND machine_id = :machine_id AND remaining_attempts > 0;
            ");
            $sql->execute( [
                'user_id' => $userId,
                'machine_id' => $machineId,
            ] );
        }
    }

    // Reset the number of login attempts remaining for a user
    public static function resetLoginAttempts(int $userId, string $machineId) : void {
        $sql = DB::getPDO()->prepare("
            DELETE FROM login_attempts WHERE user_id = :user_id AND machine_id = :machine_id;
        ");
        $sql->execute( [
            'user_id' => $userId,
            'machine_id' => $machineId,
        ] );
    }
}