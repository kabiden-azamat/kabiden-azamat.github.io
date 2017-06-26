<?php

class Lang extends Singleton {

    private static $sLang = '';
	private static $aLang = NULL;
	private static $sPath = NULL;
    private static $aPack = [
        'root' => []
    ];
    
	public static function setLang($sLangID) {
        self::initPath();
		if ( empty ( self::getAvailable() ) ) {
            throw new Exception("The system does not have the available languages.");
        }
        if ( self::isAvailable( $sLangID ) ) {
            self::$sLang = $sLangID;
        }
        return false;
	}

	protected static function initPath() {
	    if(self::$sPath === NULL) {
            self::$sPath = Config::get('lang.dir');
        }
    }

	public static function getAvailable() {
	    self::initPath();
	    if(self::$aLang === NULL) {
            $sPath = self::$sPath;
            if(file_exists($sPath)) {
                if(count(self::$aLang) > 0) self::$aLang = [];
                $aFiles = scandir($sPath);
                Func::shift_dir($aFiles);
                if(count($aFiles) > 0) {
                    foreach($aFiles as $sFile) {
                        $aFl = explode('.', $sFile);
                        $sExt = array_pop($aFl);
                        if($sExt == 'php') {
                            $sLang = implode('.', $aFl);
                            if(preg_match('/^[a-z]{2}$/i', $sLang)) {
                                self::$aLang[$sLang] = $sLang;
                            }
                        }
                    }
                } else {
                    self::$aLang = [];
                }
            } else {
                self::$aLang = NULL;
            }
        }
        return self::$aLang;
	}

	public static function isAvailable($sLangID) {
	    if(self::$aLang === NULL) {
	        self::getAvailable();
        }
	    if(isset(self::$aLang[$sLangID])) {
	        return true;
        }
        return false;
    }

    public static function getLang() {
        return self::$sLang;
    }

    public static function loadPack($sName = 'root', $isAdmin = false) {
        if($sName == 'root') {
            $sPack = self::$sPath . self::$sLang . '.php';
        } elseif(!$isAdmin) {
            $sPack = Config::get('module.dir') . $sName . '/' . Config::get('module.lang') . self::$sLang . '.php';
        } elseif($isAdmin) {
            $sPack = Config::get('admin.event.dir') . strtolower($sName) .  DIRECTORY_SEPARATOR . Config::get('admin.lang.folder') . self::getLang() . '.php';
        }
        if(file_exists($sPack)) {
            if($isAdmin) {
                self::$aPack['admin'][$sName] = include $sPack;
            } else {
                self::$aPack[$sName] = include $sPack;
            }
        }
    }

    public static function get($sName) {
        if(empty(self::$aPack['root'])) {
            self::loadPack();
        }
        $aName = explode('.', $sName);
        if(count($aName) == 3 && $aName[0] == 'admin') {
            if(!isset(self::$aPack['admin'][$aName[1]])) {
                self::loadPack($aName[1], true);
            }
            if(isset(self::$aPack['admin'][$aName[1]][$aName[2]])) {
                return self::$aPack['admin'][$aName[1]][$aName[2]];
            }
        } elseif(count($aName) == 2) {
            if(!isset(self::$aPack[$aName[0]])) {
                self::loadPack($aName[0]);
            }
            if(isset(self::$aPack[$aName[0]][$aName[1]])) {
                return self::$aPack[$aName[0]][$aName[1]];
            }
        } elseif(count($aName) == 1) {
            if(isset(self::$aPack['root'][$sName])) {
                return self::$aPack['root'][$sName];
            }
        }
        return '['.$sName.']';
    }

}