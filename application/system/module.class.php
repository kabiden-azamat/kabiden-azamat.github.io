<?php
/**
 * Created by PhpStorm.
 * User: Azamat
 * Date: 23.06.2017
 * Time: 1:10
 */

class Module extends Action{
    private $sModuleName;
    private $sLoadClasses = [];

    public function __construct($sModuleName) {
        $this->sModuleName = $sModuleName;
        $this->init();

        $s = $this->execEvent();

        if ( ":NOT_FOUND:" == $s){
            Func::page404();
        }
        exit;
    }

    protected function init() {}

    protected function getModuleName() {
        return $this->sModuleName;
    }

}