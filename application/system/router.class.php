<?php

class Router {
    protected $sReqURI = "";
    protected $aParams = [];
    protected $sAction = "";
    protected $sActionEvent = "";
    protected $sLang = '';
    protected $aParamsEventMatch = [];
    protected $_activeItems = [];
    protected $aMetaData = [];
    protected $aWidgets = [];

    public function __construct(){
        $this->sReqURI = strtolower($_SERVER['REQUEST_URI']);
        $this->sReqURI = preg_replace("/\/+/", '/', $this->sReqURI);
        $this->sReqURI = preg_replace("/^\/(.*)\/?$/U", '\\1', $this->sReqURI);
        $this->sReqURI = preg_replace("/^(.*)\?.*$/U", '\\1', $this->sReqURI);

        $this->aParams = $this->getRequestArray($this->sReqURI);
        if(Config::get('lang.use')) {
            $this->sLang = array_shift($this->aParams);
        }

        $aRewriteRule = $this->rewriteRequest($this->aParams);
        if (!empty($aRewriteRule) ) {
            $this->aParams = $aRewriteRule;
        }
        $this->sAction = array_shift($this->aParams);
        $this->sActionEvent = array_shift($this->aParams);

        if($this->getAction() == 'admin') {
            $this->getMetaData();
            $this->initWidgets();
        }
    }

    public function getLang() {
        return $this->sLang;
    }

    protected function getMetaData() {
        $sMetaPath = Config::get('module.dir') . $this->getAction() . '/' . Config::get('meta.name');
        if(file_exists($sMetaPath)) {
            $aMetaData = json_decode(file_get_contents($sMetaPath), true);
            if(isset($aMetaData['name']) && isset($aMetaData['system_name'])) {
                $this->aMetaData[$aMetaData['system_name']] = $aMetaData;
            }
        }
    }

    public function initWidgets() {
        $oSTMT = DB::execute('SELECT * FROM widgets');
        if($oSTMT && $oSTMT->rowCount() > 0) {
            while($aWidget = $oSTMT->fetch()) {
                $this->aWidgets[$aWidget['w_system_name']] = $aWidget;
            }
        }
        $sWidgetsPath = Config::get('widget.dir');
        if(file_exists($sWidgetsPath)) {
            $aFiles = scandir($sWidgetsPath);
            $this->shiftDir($aFiles);

            foreach($aFiles as $iKey => $sFile) {
                if(!isset($this->aWidgets[$sFile])) {
                    $sMetaFile = $sWidgetsPath.$sFile.'/meta.json';
                    if(file_exists($sMetaFile)){
                        $aInformation = file_get_contents( $sMetaFile );
                        $aInformation = json_decode($aInformation, true);
                        if(isset($aInformation['name'])) {
                            $sDescription = (isset($aInformation['description'])) ? $aInformation['description'] : NULL;
                            DB::execute('INSERT INTO widgets (w_system_name, w_name, w_description) VALUES (?,?,?)', $sFile, $aInformation['name'], $sDescription);
                            $this->aWidgets[$sFile] = [
                                'w_system_name' => $sFile,
                                'w_name' => $aInformation['name'],
                                'description' => ($aInformation['description']) ? $aInformation['description'] : ''
                            ];
                        }
                        $aInformation = NULL;
                    }
                }
            }


        } else {
            throw new Exception( "Widget dir not found ".$sWidgetsPath, 1 );
        }
    }

    public function getWidgets() {
        return $this->aWidgets;
    }

    public function getMeta() {
        return $this->aMetaData;
    }

    public function isCurrentPage($sUrl) {
        if(!empty($sUrl)) {
            $aUrl = explode('/', $sUrl);
            $aUrl = array_diff($aUrl, array('', NULL, false));
            $bBad = false;
            $iKey = -1;
            foreach ($aUrl as $sItem) {
                $iKey++;
                switch($iKey) {
                    case 0:
                        if($sItem != $this->getAction()) {
                            $bBad = true;
                            break 2;
                        }
                        break;
                    case 1:
                        if($sItem != $this->getEvent()) {
                            $bBad = true;
                            break 2;
                        }
                        break;
                    default:
                        if($sItem != $this->getParam($iKey - 2)) {
                            $bBad = true;
                            break 2;
                        }
                        break;
                }
            }
            if(!$bBad)
                return true;
            else
                return false;
        }
        return false;
    }

    protected function shiftDir(&$aArray) {
        array_shift($aArray);
        array_shift($aArray);
        return $aArray;
    }

    /**
     * @return mixed|string
     *
     * genUrl('task', 'as', '#') . 'sdfsd=32&ddfgd=4';
     * genUrl(['task','as', '#']);
     *
     * $a['action']
     *
     */
    public function genUrl(){
        $aFuncArg = func_get_args();

        if(Config::get('lang.use')) {
            if ( isset($aFuncArg[0]) ) {
                if (is_array($aFuncArg[0])) {
                    $aFuncArg = Func::array_delete_empty_value($aFuncArg[0]);
                } else {
                    $aFuncArg = Func::array_delete_empty_value($aFuncArg);
                }
            }
            $aFuncArg = array_merge([Lang::getLang()], $aFuncArg) ;
        }

        $iFuncArgCount = count($aFuncArg);

        if ($iFuncArgCount == 0) {
            return Config::get("url");
        }

        $sAmp = '';

        if ($iFuncArgCount == 1) {
            $sAmp = $aFuncArg[0] == "#" ? (Config::get("router.htaccess") ? "?" : "&") : "";
        } elseif ($iFuncArgCount > 1) {
            $sAmp = $aFuncArg[$iFuncArgCount - 1] == "#" ? (Config::get("router.htaccess") ? "?" : "&") : "";
        }

        if ($aFuncArg[$iFuncArgCount - 1] == "#") {
            array_pop($aFuncArg);
        }

        $sURL = implode($aFuncArg, "/") . "/";
        if (!Config::get("router.htaccess")) $sURL = "?{$sURL}";

        return Config::get("url") . $sURL . $sAmp;
    }

    public function _addActiveItems($sItem) {
        $this->_activeItems[] = $sItem;
        $this->_activeItems = array_unique($this->_activeItems);
    }

    public function _activeItems() {
        return $this->_activeItems;
    }

    /**
     * @param $sReq
     * @return array
     */
    public function getRequestArray($sReq) {
        $aRequestUrl = ($sReq == '') ? array() : explode('/', $sReq);
        for ($i = 0; $i < Config::get('router.offset_request_url'); $i++) {
            array_shift($aRequestUrl);
        }
        $aRequestUrl = array_map('urldecode', $aRequestUrl);
        return Func::array_delete_empty_value($aRequestUrl);
    }

    /**
     * @param $aRequestUrl
     * @return array
     */
    protected function rewriteRequest($aRequestUrl) {
        $sReq = implode('/', $aRequestUrl);
        if ($aRewrite = Config::get('router.uri')) {
            $sReq = preg_replace(array_keys($aRewrite), array_values($aRewrite), $sReq);
        }
        return ($sReq == '') ? array() : explode('/', $sReq);
    }

    /**
     * @return int
     */
    public function paramCount() {
        return count($this->aParams);
    }

    /**
     * @param $iParamIndex
     * @param bool|FALSE $sDefaultValue
     * @return bool
     */
    public function getParam($iParamIndex, $sDefaultValue = FALSE) {
        return isset($this->aParams[$iParamIndex]) ? $this->aParams[$iParamIndex] : $sDefaultValue;
    }

    public function getHost(){
        $sURL = $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
        $sURL.= $_SERVER['HTTP_HOST'] . '/';
        return $sURL;
    }

    /**
     * @return array
     */
    public function getParams() {
        return $this->aParams;
    }

    /**
     * @param $aParamEventMatch
     */
    public function setParamEventMatch($aParamEventMatch) {
        $this->aParamsEventMatch = $aParamEventMatch;
    }

    /**
     * @param $iParamNum
     * @param null $iItem
     * @return null
     */
    public function getParamEventMatch($iParamNum, $iItem = null) {
        if (!is_null($iItem)) {
            if (isset($this->aParamsEventMatch['params'][$iParamNum][$iItem])) {
                return $this->aParamsEventMatch['params'][$iParamNum][$iItem];
            } else {
                return null;
            }
        } else {
            if (isset($this->aParamsEventMatch['event'][$iParamNum])) {
                return $this->aParamsEventMatch['event'][$iParamNum];
            } else {
                return null;
            }
        }
    }

    /**
     * @return mixed|string
     */
    public function getAction() {
        return $this->sAction != "" ? $this->sAction : Config::get('router.default_action');
    }

    public function setAction($sActionName) {
        $this->sAction = $sActionName;
    }

    /**
     * @return mixed|string
     */
    public function getEvent() {
        return $this->sActionEvent != "" ? $this->sActionEvent : "index";
    }

    /**
     * @param bool|FALSE $bAmp
     * @param bool $bFullUrl
     * @return string
     */
    public function currentURL($bAmp = FALSE, $bFullUrl = false) {
        return ($bFullUrl ? Config::get("url") : '') . strtolower($_SERVER['REQUEST_URI']) . ($bAmp ? (Config::get("router.htaccess") ? "?" : "&") : "");
    }

    public function getFullUrl(){
        $sURL = $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
        $sURL.= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $sURL;
    }

    /**
     * @param $aAllowURL_NoAuth
     * @return bool
     */
    public function isAllowUrl($aAllowURL_NoAuth) {
        $bIsAllow = false;
        foreach ($aAllowURL_NoAuth as $aUrlActions) {
            if (empty($aUrlActions)) continue;
            if (count($aUrlActions) == 0) continue;

            $_sAction = array_shift($aUrlActions);
            $_sEvent = '';

            if ($_sAction != $this->getAction()) {
                continue;
            } else {
                if (count($aUrlActions) == 0) {
                    $bIsAllow = true;
                    break;
                }
            }

            if (count($aUrlActions) > 0) {
                $_sEvent = array_shift($aUrlActions);
            }

            if ($_sEvent != $this->getEvent()) {
                continue;
            } else {
                if (count($aUrlActions) == 0) {
                    $bIsAllow = true;
                    break;
                }
            }

            if (count($aUrlActions) > 0) {
                if (count($this->getParams()) == count($aUrlActions)) {
                    foreach ($aUrlActions as $iParamIndex => $sParam) {
                        if ($this->getParam($iParamIndex) != $sParam) continue 2;
                        $bIsAllow = true;
                    }
                }
            }
        }

        return $bIsAllow;
    }
}