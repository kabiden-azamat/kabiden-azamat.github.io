<?php
/**
 * Created by PhpStorm.
 * User: Azamat
 * Date: 03.06.2017
 * Time: 16:43
 */

define ('DIR_SEP', DIRECTORY_SEPARATOR);
define ('WEB_SEP', '/');
define ('DIR_ROOT', $_SERVER['DOCUMENT_ROOT'] . DIR_SEP);

if (version_compare(PHP_VERSION, '5.5', '<')) {
    die('Unsupported PHP version');
}

spl_autoload_register('func_AutoloadClass', true, true);

function func_AutoloadClass($sClass_name) {
    $aPostfix = ['.class.php'];
    $aDirList = array(
        'application/system' . DIR_SEP,
        'application/modules' . DIR_SEP
    );

    foreach($aDirList as $sDirPath) {
        foreach( $aPostfix as $sPostfix ) {
            $sClassPath = strtolower($sClass_name) . $sPostfix;

            if( file_exists($sDirPath . $sClassPath) ) {
                require_once($sDirPath . $sClassPath);
                return true;
            }
        }

    }
    return false;
}

Core::run();