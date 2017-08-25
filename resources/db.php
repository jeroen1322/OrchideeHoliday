<?php
//use
// DB::conn()->prepare(....
class DB {
    private static $mysqli;
    private function __construct(){} //no instantiation

    static function conn() {
        if( !self::$mysqli ) {
            //Server gegevens
            $server = "localhost";
            $username = "root";
            $passw = "root";
            $dbname = "orchideeholiday";
            self::$mysqli = new mysqli($server, $username, $passw, $dbname);
        }
        return self::$mysqli;
    }
}
