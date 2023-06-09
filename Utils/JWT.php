<?php

require_once __DIR__.'/ENV.php';

class JWT {

    private string $header;
    private string $payload;
    private string $signature;

    private function __construct(string $header, string $payload, string $signature) {
        $this->header = $header;
        $this->payload = $payload;
        $this->signature = $signature;
    }

    // getters
    public function getDecodedHeader() : array {
        return json_decode(base64_decode($this->header), true);
    }

    public function getDecodedPayload() : array {
        return json_decode(base64_decode($this->payload), true);
    }

    // check if the JWT is valid and not expired
    public function validate() : bool{

        $payload = $this->getDecodedPayload();
        if ( $payload['exp'] < time() ) {
            return false;
        }

        $signature_check = base64_encode(hash_hmac('sha256', $this->header.$this->payload, ENV::get('JWTSECRET'), true));
        return $signature_check == $this->signature;
    }

    // turns a JWT object into a string, for storage in a cookie, most likely
    public function stringify() : string {
        return $this->header.'.'.$this->payload.'.'.$this->signature;
    }

    // turns a string into a JWT object
    public static function unstringify(string $jwt) : JWT|bool {
        $jwt = explode('.', $jwt);
        if (count($jwt) != 3) {
            return false;
        }
        $header = $jwt[0];
        $payload = $jwt[1];
        $signature = $jwt[2];

        return new JWT($header, $payload, $signature);
    }

    // generates a JWT object to identify as a given user
    public static function generateJWT(User $user) : JWT {

        // $sql = DB::getPDO()->prepare("
        //     SELECT * FROM users WHERE id = :id;
        // ");

        $header = base64_encode(json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]));

        $payload = base64_encode(json_encode([
            'iss' => 'localhost',
            'sub' => $user->id,
            'name' => $user->username,
            'email' => $user->email,
            'iat' => time(), 
            'exp' => time() + 60 * 60 * 24 * 7
        ]));

        $signature = base64_encode(hash_hmac('sha256', $header.$payload, ENV::get('JWTSECRET'), true));

        return new JWT($header, $payload, $signature);
    }


    // Checks if the user is logged in and the token is valid; redirects to the login page if not
    public static function resolveTokenValidity() : ?JWT {
        if ( !isset($_COOKIE['token']) ) {
            // define("ERROR_MSG", "You are not logged in");
            header('Location: /login?error=You are not logged in');
            return NULL;
        }
        $token = JWT::unstringify($_COOKIE['token']);
        if ( !$token->validate() ) {
            unset($_COOKIE['token']);
            setcookie('token', null, -1, '/'); 

            // define("ERROR_MSG", "Invalid Token");
            header('Location: /login?error=Invalid Token');
            return NULL;
        }
        
        return $token;
    }

    // Checks if the user is logged in and displays a message if so
    public static function getLoggedMessage() : ?string {
        if ( !isset($_COOKIE['token']) ) {
            return NULL;
        }
        $token = JWT::unstringify($_COOKIE['token']);
        if ( $token == NULL ) {
            return NULL;
        }

        if ( !$token->validate() ) {
            return NULL;
        }

        return "Logged in as ".$token->getDecodedPayload()['name'];
    }
}

?>