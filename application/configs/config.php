<?php
/**
 * Created by PhpStorm.
 * User: Azamat
 * Date: 03.06.2017
 * Time: 16:00
 */

if (!isset( $_SERVER['SHELL'] ) ) {
    $aConfigs['home_domain'] = $_SERVER['HTTP_HOST'];
    $aConfigs['https'] = FALSE;
    $aConfigs['url'] = ($aConfigs['https'] ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/";
    $aConfigs['root'] = $_SERVER['DOCUMENT_ROOT'];
}

$aConfigs['site_name'] = 'Test';
$aConfigs['app']['folder'] = 'application/';
$aConfigs['app']['dir'] = '___root___/___app.folder___';

$aConfigs['module']['folder'] = 'modules/';
$aConfigs['module']['dir'] = '___app.dir______module.folder___';
$aConfigs['module']['lang'] = 'i18n/';
$aConfigs['module']['template'] = 'templates/';

$aConfigs['log']['folder'] = 'logs/';
$aConfigs['log']['dir'] = '___app.dir______log.folder___';

$aConfigs['lang']['use'] = true;
$aConfigs['lang']['folder'] = 'i18n/';
$aConfigs['lang']['dir'] = '___app.dir______lang.folder___';
$aConfigs['lang']['default'] = 'ru';

$aConfigs['router']['htaccess'] = true;
$aConfigs['router']['default_action'] = "index";
$aConfigs['router']['offset_request_url'] = 0;
$aConfigs['router']['rewrite'] = array();

return $aConfigs;