<?php
/**
 * Created by PhpStorm.
 * User: Azamat
 * Date: 23.06.2017
 * Time: 1:10
 */

class Module extends Action{
    private $sModuleName;
    private $oRender = NULL;
    private $oClass = NULL;
    private $isAdmin = false;

    public function __construct($sModuleName, $isAdmin = false) {
        $this->isAdmin = $isAdmin;
        $this->sModuleName = $sModuleName;
        $this->init();
        $this->loadClass();

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

    protected function _() {
        if($this->oClass === NULL || !is_object($this->oClass)) {
            throw new Exception('Class did not loaded!');
        }
        return $this->oClass;
    }

    protected function loadClass() {
        $sClassPath = Config::get('module.dir') . strtolower($this->sModuleName) . DIRECTORY_SEPARATOR . Config::get('module.classes') . DIRECTORY_SEPARATOR . strtolower($this->sModuleName) . '.class.php';
        if(file_exists($sClassPath)) {
            include_once $sClassPath;
            $sClass = 'Object'.ucfirst($this->sModuleName);
            if(class_exists($sClass)) {
                $this->oClass = new $sClass();
            }
        }
    }

    protected function getRender() {
        if($this->oRender === NULL) {
            $this->oRender = new Render($this->sModuleName, Config::get('module.dir') . strtolower($this->sModuleName) . DIRECTORY_SEPARATOR . Config::get('module.template'), $this->isAdmin);
        }
        return $this->oRender;
    }

}