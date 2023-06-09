<?php

class ENV {
    private static array $env;
    public static function get(string $key) : string {
        if ( !isset($env) ) {
            $env = parse_ini_file(__DIR__.'/../../.env');
        }
        return $env[$key];
    }
}

?>