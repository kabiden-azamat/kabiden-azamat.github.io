<?php

/**
 * Class Action
 */
class Action {
    protected $aRegisterEvent = array();
    protected $aEvent = array();
    protected $aParamsEventMatch = array();
    public $sCurrentEventName = '';

    /**
     * Регистрация события (алиас к getEventMethod)
     * @param $sEventName
     * @param $sEventFunction
     * @return $this
     * @throws Exception
     */
    public function addEvent($sEventName,$sEventFunction) {
        $this->addEventPreg("/^{$sEventName}$/i",$sEventFunction);
        return $this;
    }

    /**
     * Проверка зарегистрировано ли запрашиваемое событие
     * @param $sName
     * @return bool
     */
    public function isExistEvent($sName){
        foreach ( $this->aRegisterEvent aS $aEvent ) {
            if( preg_match($aEvent['preg'], $sName) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Получить выполняемый метод по событию
     * @param $sName
     * @return bool
     */
    public function getEventMethod($sName){
        foreach ( $this->aRegisterEvent aS $aEvent ) {
            if( preg_match($aEvent['preg'], $sName) ) {
                return $aEvent['method'];
            }
        }
        return false;
    }

    /**
     * Регистрация события в роуторе
     * @return $this
     * @throws Exception
     */
    public function addEventPreg() {

        $iCountArgs=func_num_args();
        if ($iCountArgs<2) {
            throw new Exception("Incorrect number of arguments when adding events");
        }
        $aEvent=array();

        $aNames=(array)func_get_arg($iCountArgs-1);
        $aEvent['method']=$aNames[0];
        if (isset($aNames[1])) {
            $aEvent['name']=$aNames[1];
        } else {
            $aEvent['name']=$aEvent['method'];
        }

        $aEvent['preg']=func_get_arg(0);
        $aEvent['params_preg']=array();
        for ($i=1;$i<$iCountArgs-1;$i++) {
            $aEvent['params_preg'][]=func_get_arg($i);
        }
        $this->aRegisterEvent[]=$aEvent;

        return $this;
    }

    /**
     * Выполнение метода согласно зарегистрированному событию
     * @return bool|mixed
     */
    public function execEvent() {

        if ( count($this->aRegisterEvent) > 0 ) {
            foreach ($this->aRegisterEvent as $aEvent) {

                if (preg_match($aEvent['preg'], Core::getRouter()->getEvent(),$aMatch)) {

                    $this->aParamsEventMatch['event']=$aMatch;
                    $this->aParamsEventMatch['params']=array();
                    foreach ($aEvent['params_preg'] as $iKey => $sParamPreg) {
                        if (preg_match($sParamPreg, Core::getRouter()->getParam($iKey,''),$aMatch)) {
                            $this->aParamsEventMatch['params'][$iKey]=$aMatch;
                        } else {
                            continue 2;
                        }
                    }


                    Core::getRouter()->setParamEventMatch($this->aParamsEventMatch);
                    $this->sCurrentEventName=$this->aParamsEventMatch['event'][0];
                    return call_user_func_array(array($this,$aEvent['method']),array());
                }
            }
        } else {
            if ( method_exists($this, Core::getRouter()->getEvent()) ) {
                return call_user_func_array(array($this,Core::getRouter()->getEvent()),array());
            }
        }

        return ':NOT_FOUND:';
    }

    public function isEventExist() {
        foreach ($this->aRegisterEvent as $aEvent) {
            if (preg_match($aEvent['preg'], Core::getRouter()->getEvent(),$aMatch)) {
                $this->aParamsEventMatch['event']=$aMatch;
                $this->aParamsEventMatch['params']=array();
                foreach ($aEvent['params_preg'] as $iKey => $sParamPreg) {
                    if (preg_match($sParamPreg, Core::getRouter()->getParam($iKey,''),$aMatch)) {
                        $this->aParamsEventMatch['params'][$iKey]=$aMatch;
                    } else {
                        continue 2;
                    }
                }
                return true;
            }
        }
        return false;
    }

    public function initPlugins() {
        $sEvent = Core::getRouter()->getEvent();
        $sAction = Core::getRouter()->getAction();
        $sPluginFilePath =  Config::get( 'module.dir' ) . strtolower($sAction) . DIRECTORY_SEPARATOR . 'plugins/' . strtolower($sEvent) . DIRECTORY_SEPARATOR .  strtolower($sEvent) . '.php';

        if(file_exists($sPluginFilePath)) {
            include $sPluginFilePath;
            $sPluginClass = 'Plugin'.ucfirst($sEvent);
            if(class_exists($sPluginClass)) {
                $oPlugin = new $sPluginClass('Module'.ucfirst($sAction));
                if(method_exists($oPlugin, 'init')) {
                    $oPlugin->init();
                }
            } else
                Func::page404();
        } else
            Func::page404();
    }
}