<?php
/**
 * Created by PhpStorm.
 * User: Azamat
 * Date: 09.06.2017
 * Time: 18:51
 */

class Func {

    static function array_delete_empty_value($aArray){
        return array_values(array_diff($aArray,array('')));
    }

    static function shift_dir(&$aFiles) {
        array_shift($aFiles);
        array_shift($aFiles);
    }

    static function headerLocation($sLocation){
        http_response_code(302);
        header('Location: '.$sLocation);
        exit();
    }

    /**
     * Возвращает ошибку 404 и перенаправляет пользователя на указанную страницу
     */
    static function page404(){
        ob_end_clean();
        echo '<div style="display: block;font-family:\'Lucida Console\',serif;text-align: center;box-sizing: border-box;padding:20px;background-color:#95a5a6;color:#2c3e50;border-radius: 2px;">[404]<br/>PAGE NOT FOUND</div>';
        http_response_code(404);
        exit();
    }

    /**
     * Возвращает ошибку 400 и останавливает выполнение кода
     */
    static function page400(){
        ob_end_clean();
        echo '<div style="display: block;font-family:\'Lucida Console\',serif;text-align: center;box-sizing: border-box;padding:20px;background-color:#e74c3c;color:#fff;border-radius: 2px;">[400]<br/>BAD REQUEST</div>';
        http_response_code(400);
        exit();
    }

    static function page403(){
        ob_end_clean();
        echo '<div style="display: block;font-family:\'Lucida Console\',serif;text-align: center;box-sizing: border-box;padding:20px;background-color:#e74c3c;color:#fff;border-radius: 2px;">[403]<br/>ACCESS DENIED</div>';
        http_response_code(403);
        exit();
    }

}