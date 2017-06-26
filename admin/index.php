<?php
/**
 * Created by PhpStorm.
 * User: Azamat
 * Date: 23.06.2017
 * Time: 23:00
 */

class Admin {

    public function __construct() {
        $sEvent = Core::getRouter()->getEvent();
        $sEventName = 'Event' . ucfirst($sEvent);
        $sEventPath = Config::get('admin.event.dir') . strtolower($sEvent) . DIRECTORY_SEPARATOR . $sEventName . '.php';
        if(file_exists($sEventPath)) {
            include_once $sEventPath;
            if(class_exists($sEventName)) {
                MetaData::loadMeta();
                $oModule = new $sEventName(Core::getRouter()->getAction(), true);
            }
        } else {
            Func::page404();
        }
    }
    
    public static function Loads() {
        return [
            'header' => [
                ['type' => 'content', 'name' => 'description', 'content' => 'Административная панель Azamat CMS 4.0'],
                ['type' => 'content', 'name' => 'keywords', 'content' => 'admin, panel, web admin'],
                ['type' => 'content', 'name' => 'author', 'content' => 'Azamat'],
                ['type' => 'content', 'name' => 'viewport', 'content' => 'width=device-width, initial-scale=1'],
                ['type' => 'css', 'link' => '___TEMPLATE_PATH___resourse/css/bootstrap.min.css'],
                ['type' => 'css', 'link' => '___TEMPLATE_PATH___resourse/css/icons.css'],
                ['type' => 'css', 'link' => '___TEMPLATE_PATH___resourse/css/main.css'],
                ['type' => 'css', 'link' => '___TEMPLATE_PATH___resourse/css/responsive.css'],
                ['type' => 'css', 'link' => '___TEMPLATE_PATH___resourse/css/noty.css']
            ],
            'footer' => [
                ['type' => 'js', 'link' => '___TEMPLATE_PATH___resourse/js/jquery-2.1.3.js'],
                ['type' => 'js', 'link' => '___TEMPLATE_PATH___resourse/js/bootstrap.min.js'],
                ['type' => 'js', 'link' => '___TEMPLATE_PATH___resourse/js/noty.js'],
                ['type' => 'js', 'link' => 'https://maps.google.com/maps/api/js?key=AIzaSyDrlCWSCEGTYat1yFIybvtjXe6v24wXY04', 'async' => 'true'],
                ['type' => 'js', 'link' => '___TEMPLATE_PATH___resourse/js/app.js'],
                ['type' => 'js', 'link' => '___TEMPLATE_PATH___resourse/js/common.js'],
                ['type' => 'js', 'link' => '___TEMPLATE_PATH___resourse/js/inbox.js'],
            ]
        ];
    }

}