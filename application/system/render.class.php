<?php
/**
 * Created by PhpStorm.
 * User: Azamat
 * Date: 23.06.2017
 * Time: 1:38
 */

class Render {
    private $sModulePath = '';
    private $sTemplatePath = '';
    private $sTemplateWeb = '';
    private $sTheme = NULL;
    private $sTemplate = NULL;
    private $isAdmin = false;
    private $sTitle = NULL;
    private $sOutputType = '';
    private $aVars = [];
    private $aBreadCrumb = [];
    private $aLoads = [
        'header' => [],
        'footer' => []
    ];

    public function __construct($sModuleName, $sPath, $isAdmin) {
        $this->isAdmin = $isAdmin;
        if($isAdmin) {
            $this->aLoads = Admin::Loads();
            $this->sModulePath = Config::get('admin.event.dir') . strtolower(Core::getRouter()->getEvent()) . DIRECTORY_SEPARATOR . Config::get('admin.template');
            $this->sTemplatePath = Config::get('template.dir') . Config::get('admin.template_name') . DIRECTORY_SEPARATOR;
            $this->sTemplateWeb = Config::get('template.web') . Config::get('admin.template_name') . WEB_SEP;
            
        } else {
            $this->sModulePath = $sPath;
            $this->sTemplatePath = Config::get('template.dir') . Config::get('template.default') . DIRECTORY_SEPARATOR;
            $this->sTemplateWeb = Config::get('template.web') . Config::get('template.default') . WEB_SEP;
        }
    }

    public function setOutput($sOutput = 'html') {
        $this->sOutputType = $sOutput;
    }

    public function putVar( $sVarName, $vValue ) {
        if(!preg_match( '/\A[a-zA-Z_]/', $sVarName ) ) throw new Exception( "Invalid variable name '{$sVarName}'", 1 );
        $this->aVars[ $sVarName ] = $vValue;
        return $this;
    }

    public function putVarArray($aVars) {
        foreach( $aVars as $sFldName=>$sFldValue ) {
            $this->putVar($sFldName, $sFldValue);
        }
        return $this;
    }
    
    public function setMainTemplate($sName) {
        $this->sTemplatePath = Config::get('template.dir') . Config::get('template.default') . DIRECTORY_SEPARATOR;
        if(!file_exists($this-sTemplatePath)) {
            throw new Exception('Main template path not found!');
        }
    }

    public function setTemplate($sName) {
        $this->sTemplate = $this->sModulePath . $sName . '.php';
        if(!file_exists($this->sTemplate)) {
            throw new Exception('Template not found!');
        }
    }

    public function setTitle($sTitle) {
        $this->sTitle = $sTitle;
    }

    public function __get($name) {
        if(isset($this->aVars[$name])) {
            return $this->aVars[$name];
        }
        if(isset($this->$name)) {
            return $this->$name;
        }
        return false;
    }

    public function Run($sTheme) {
        $this->sTheme = $sTheme;

        switch ($this->sOutputType) {
            case 'text':
                header( 'Content-Type: text/plain; charset=utf-8' );
                echo $sTheme;
                break;
            case 'json':
                header( 'Content-Type: application/json; charset=utf-8' );
                echo( json_encode( $this->aVars  ) );
                break;
            default:
                $sOutput = $this->sTemplatePath . $sTheme . '.php';
                if(file_exists($sOutput)) {
                    include_once $sOutput;
                } else {
                    throw new Exception('Theme '.$sTheme.' not found!');
                }
                break;
        }
    }

    public function loadTemplate() {
        if(file_exists($this->sTemplate)) {
            include_once $this->sTemplate;
            echo PHP_EOL;
        }
    }
    
    public function inc_tpl($sName) {
        if(file_exists($this->sTemplatePath . $sName . '.php')) {
            include $this->sTemplatePath . $sName . '.php';
        } else {
            throw new Exception('Template file not found!');
        }
    }

    public function footer() {
        $this->includeLoads('footer');
    }

    public function header() {
        $this->includeLoads('header');
    }

    private function includeLoads($sPosition) {
        switch($sPosition) {
            case 'header':
                $sOutput = '<meta charset="utf-8">' . PHP_EOL;
                $sOutput .= '<title>'.$this->sTitle.'</title>' . PHP_EOL;
                if(isset($this->aLoads['header'])) {
                    if(!empty($this->aLoads['header'])) {
                        foreach($this->aLoads['header'] as $aMeta) {
                            $sOutput .= $this->genereteMeta($aMeta) . PHP_EOL;
                        }
                    }
                }
                echo $sOutput;
                break;
            case 'footer':
                $sOutput = '';
                if(isset($this->aLoads['footer'])) {
                    if(!empty($this->aLoads['footer'])) {
                        foreach($this->aLoads['footer'] as $aMeta) {
                            $sOutput .= $this->genereteMeta($aMeta) . PHP_EOL;
                        }
                    }
                }
                echo $sOutput;
                break;
        }
    }
    
    private function genereteMeta($aMeta) {
        if(isset($aMeta['type'])) {
            switch($aMeta['type']) {
                case 'content':
                    if(isset($aMeta['name']) && isset($aMeta['content'])) {
                        return '<meta name="'.$aMeta['name'].'" content="'.$aMeta['content'].'">';
                    }
                    break;
                case 'css':
                    if(isset($aMeta['link'])) {
                        $this->prepareLink($aMeta['link']);
                        return '<link rel="stylesheet" href="'.$aMeta['link'].'">';
                    }
                    break;
                case 'js':
                    if(isset($aMeta['link'])) {
                        $this->prepareLink($aMeta['link']);
                        $sAtributes = '';
                        if(isset($aMeta['async'])) {
                            $sAtributes .= 'async';
                        }
                        if(!empty($sAtributes)) $sAtributes = ' '.$sAtributes;
                        return '<script src="'.$aMeta['link'].'"'.$sAtributes.'></script>';
                    }
                    break;
            }
        }
        return '';
    }

    public function getTitle() {
        return $this->sTitle;
    }
    
    private function prepareLink(&$sLink) {
        $sLink = str_replace('___TEMPLATE_PATH___', $this->sTemplateWeb, $sLink);
    }

    public function addBreadCrumb($sTitle, $sUrl = false) {
        $this->aBreadCrumb[] = ['title' => $sTitle, 'url' => $sUrl];
    }

    public function getBreadCrumbs() {
        return $this->aBreadCrumb;
    }

}