<?php

class Account extends Singleton {
    const USERS_TABLE = 'users';

    public static function isAuth() {
        if($_SESSION['access_token']) {
            if(self::existToken($_SESSION['access_token'])) {
                return true;
            }
        }
        return false;
    }

    private static function existToken($sToken) {
        $iCount = DB::get_count(self::USERS_TABLE, 'u_access_token = ?', $sToken);
        if($iCount == 1) {
            return true;
        }
        return false;
    }

    public static function Auth($sLogin, $sPassword) {
        
    }

}