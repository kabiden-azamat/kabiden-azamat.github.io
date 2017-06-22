<?php
/**
 * Created by PhpStorm.
 * User: Azamat
 * Date: 16.06.2017
 * Time: 16:44
 */

class DB extends Singleton {

    private static $_oDb;
    private static $aConnectParams = [];

    private static $aSQL = NULL;

    static public function init($aConnectParams)
    {
        if (empty(self::$aConnectParams)) {
            self::$aConnectParams = $aConnectParams;
        }
        return true;
    }

    static public function getDb()
    {
        if (self::$_oDb === null) {
            self::$_oDb = new MyPDO('mysql:host=' . self::$aConnectParams['host'] . ';dbname=' . self::$aConnectParams['dbname'] . ';charset=' . self::$aConnectParams['charset'], self::$aConnectParams['user'], self::$aConnectParams['password']);
        }

        return self::$_oDb;
    }

    static public function a_execute($sSQL, $aParams = [])
    {
        $oSTMT = self::getDb()->prepare($sSQL);
        if ($oSTMT->execute($aParams)) {
            return $oSTMT;
        }
        return false;
    }

    static public function execute()
    {
        if (func_num_args() == 0) {
            new Exception('Invalid function arguments');
        }

        $aArgs = func_get_args();

        $sSQL = array_shift($aArgs);

        $oStatement = self::getDb()->prepare($sSQL);

        if ($oStatement->execute($aArgs)) {
            return $oStatement;
        }

        return false;
    }

    static public function get_count()
    {
        if (func_num_args() < 1) {
            new Exception('Invalid function arguments');
        }

        $aArgs = func_get_args();

        $sTable = array_shift($aArgs);
        $sWhere = func_num_args() > 1 ? array_shift($aArgs) : '';

        $sSQL = 'SELECT COUNT(*) FROM ' . $sTable . ($sWhere != '' ? ' WHERE ' . $sWhere : '');

        $oStatement = self::getDb()->prepare($sSQL);

        if ($oStatement->execute($aArgs)) {
            return $oStatement->fetchColumn(0);
        }

        return false;
    }

    static public function lastInsertId()
    {
        return self::getDb()->lastInsertId();
    }

    static public function getPage($sSQL, $iOffset, $iLimit, &$iTotalCount, $aParams = []) {
        function countSQL($sSQL) {
            $iPos = stripos($sSQL, 'FROM');
            if($iPos !== false) {
                $sSQL = substr($sSQL, $iPos, strlen($sSQL));
            }
            return 'SELECT COUNT(*) '.$sSQL;
        }

        $oSTMT = self::execute(countSQL($sSQL), $aParams);
        if($oSTMT && $oSTMT->rowCount() == 1) {
            $iTotalCount = $oSTMT->fetchColumn(0);
        }

        $oSTMT = DB::execute($sSQL.' LIMIT '.$iOffset.','.$iLimit, $aParams);
        if($oSTMT && $oSTMT->rowCount() > 0) {
            return $oSTMT;
        }
        return false;
    }

    static private function addSQL($sParam, $sValue, $aParams = [], $iAdd = true) {
        switch($sParam) {
            case 'need_fields':
            case 'table_name':
                if(isset(self::$aSQL[$sParam])) {
                    if($iAdd) {
                        self::$aSQL[$sParam] = implode(', ', [self::$aSQL[$sParam], $sValue]);
                    } else {
                        self::$aSQL[$sParam] = $sValue;
                    }
                } else {
                   self::$aSQL[$sParam] = $sValue;
                }
                self::$aSQL[$sParam] = $sValue;
                break;
            case 'where':
                if(!is_array($sValue)) {
                    throw new Exception('Value is not Array');
                }
                if(isset(self::$aSQL[$sParam])) {
                    if($iAdd) {
                        self::$aSQL[$sParam] = array_merge(self::$aSQL[$sParam], $sValue);
                    } else {
                        self::$aSQL[$sParam] = $sValue;
                    }
                } else {
                    self::$aSQL[$sParam] = $sValue;
                }
                break;
        }
    }

    static public function select($sTable, $sNeedFields = '*', $iAdd = true) {
        self::addSQL('table_name', $sTable, [], $iAdd);
        self::addSQL('need_fields', $sNeedFields, [], $iAdd);
    }

    static public function where($aWhere) {
        self::addSQL('where', $aWhere);
    }

}