<?php

class MetaData extends Singleton {
    private static $aMetaData = [];
    
    public static function loadMeta() {
        $sMetaDir = Config::get('admin.event.dir');
        $aEvents = scandir($sMetaDir);
        Func::shift_dir($aEvents);
        if(!empty($aEvents)) {
            foreach($aEvents as $sEvent) {
                $sMetaPath = $sMetaDir . $sEvent . DIR_SEP . 'meta.json';
                if(file_exists($sMetaPath)) {
                    $aTempMeta = file_get_contents($sMetaPath);
                    $aTempMeta = json_decode($aTempMeta, true);
                    if(isset($aTempMeta['module']) && isset($aTempMeta['module_name']) && isset($aTempMeta['system'])) {
                        if(!$aTempMeta['system']) {
                            $aTempMeta['module_name'] = Lang::get($aTempMeta['module_name']);
                            self::$aMetaData[$aTempMeta['module']] = $aTempMeta;
                        }
                    }
                }
            }
        }
    }
    
    public static function get() {
        return self::$aMetaData;
    }
    
}