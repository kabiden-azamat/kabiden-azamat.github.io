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
       
        
        /*ini_set('display_errors', 'on');
        $oErrorHandler = new ErrorHandler();
        $oErrorHandler->register();*/

        Config::LoadFromFile('./application/configs/config.php');

        DB::init( Config::get('db') );

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
        if($sModuleName == Config::get('admin.action')) {
            $sModulePath = Config::get('admin.dir') . 'index.php';
            if(file_exists($sModulePath)) {
                include_once $sModulePath;
                if(class_exists('Admin')) {
                    $oModule = new Admin();
                }
            } else {
                Func::page404();
            }
        } else {
            $sModulePath = Config::get('module.dir') . strtolower($sModuleName) . DIRECTORY_SEPARATOR . 'Module' . ucfirst($sModuleName) . '.php';
            if (file_exists($sModulePath)) {
                include_once $sModulePath;
                $sModule = 'Module' . ucfirst($sModuleName);
                if (class_exists($sModule)) {
                    $oModule = new $sModule($sModuleName);
                }
            } else {
                Func::page404();
            }
        }
    }

}