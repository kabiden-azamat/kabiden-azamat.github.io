<?php
/**
 * Created by PhpStorm.
 * User: Azamat
 * Date: 03.06.2017
 * Time: 16:51
 * Class Singleton
 */
class Singleton {
    private static $oInstance;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance() {
        if (!(self::$oInstance instanceof self)) {
            self::$oInstance = new self();
        }
        return self::$oInstance;
    }
}