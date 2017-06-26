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
$aConfigs['web'] = '/';

$aConfigs['site_name'] = 'Test';
$aConfigs['app']['folder'] = 'application/';
$aConfigs['app']['dir'] = '___root___/___app.folder___';
$aConfigs['app']['web'] = '___web______app.folder___';

$aConfigs['module']['folder'] = 'modules/';
$aConfigs['module']['dir'] = '___app.dir______module.folder___';
$aConfigs['module']['lang'] = 'i18n/';
$aConfigs['module']['template'] = 'templates/';
$aConfigs['module']['classes'] = 'classes/';

$aConfigs['log']['folder'] = 'logs/';
$aConfigs['log']['dir'] = '___app.dir______log.folder___';

$aConfigs['lang']['use'] = true;
$aConfigs['lang']['folder'] = 'i18n/';
$aConfigs['lang']['dir'] = '___app.dir______lang.folder___';
$aConfigs['lang']['default'] = 'ru';

$aConfigs['template']['folder'] = 'templates/';
$aConfigs['template']['dir'] = '___app.dir______template.folder___';
$aConfigs['template']['web'] = '___app.web______template.folder___';
$aConfigs['template']['default'] = 'default';

$aConfigs['admin']['panel']['name'] = 'Azamat CMS';
$aConfigs['admin']['panel']['version'] = '1.0';
$aConfigs['admin']['action'] = 'admin';
$aConfigs['admin']['folder'] = 'admin/';
$aConfigs['admin']['dir'] = '___root___/___admin.folder___';
$aConfigs['admin']['event']['folder'] = 'events/';
$aConfigs['admin']['event']['dir'] = '___admin.dir______admin.event.folder___';
$aConfigs['admin']['lang']['folder'] = 'i18n/';
$aConfigs['admin']['template'] = 'templates/';
$aConfigs['admin']['template_name'] = 'admin';

$aConfigs['router']['htaccess'] = true;
$aConfigs['router']['default_action'] = "index";
$aConfigs['router']['offset_request_url'] = 0;
$aConfigs['router']['rewrite'] = array();

/**
 * MySQL Конфигурация
 */
$aConfigs['db'] = array(
    'host' => 'localhost',
    'dbname' => 'kinoplus',
    'user' => 'root',
    'password' => '',
    'charset' => 'utf8'
);

return $aConfigs;