<?php
/**
 * Created by PhpStorm.
 * User: Azamat
 * Date: 03.06.2017
 * Time: 16:51
 */

class Core extends Singleton {

    private static $oRouter = NULL;

    public static function getRouter() {
        if(self::$oRouter == NULL) {
            self::$oRouter = new Router();
        }
        return self::$oRouter;
    }

    public static function run() {
        ob_start();
        ini_set('display_errors', 'on');
        $oErrorHandler = new ErrorHandler();
        $oErrorHandler->register();

        Config::LoadFromFile('./application/configs/config.php');

        if(Config::get('lang.use')) {
            $sLang = self::getRouter()->getLang();
            if(Lang::isAvailable($sLang)) {
                Lang::setLang($sLang);
            } else {
                Lang::setLang(Config::get('lang.default'));
                Func::headerLocation(Core::getRouter()->genUrl(''));
            }
        }

        $sModuleName = Core::getRouter()->getAction();
        $sModulePath = Config::get('module.dir') . strtolower($sModuleName) . DIRECTORY_SEPARATOR . 'Module' . ucfirst($sModuleName) . '.php';
        if(file_exists($sModulePath)) {
            include_once $sModulePath;
            $sModuleName = 'Module'.ucfirst($sModuleName);
            if(class_exists($sModuleName)) {
                $sModule = new $sModuleName($sModuleName);
            }
        } else {
            Func::page404();
        }
    }

}