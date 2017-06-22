<?php
/**
 * Created by PhpStorm.
 * User: Azamat
 * Date: 08.06.2017
* Time: 0:52
*/

class Config {
    static protected $aMapper = array();
    static protected $aInstance = null;
    protected $aConfig = array();

    protected function __construct(){}

    /**
     * Load configuration array from file
     *
     * @static
     * @param string $sFile Путь до файла конфига
     * @param bool $bRewrite Перезаписывать значения
     * @return bool|Config
     */
    static public function LoadFromFile($sFile, $bRewrite = true){
        // Check if file exists
        if (!file_exists($sFile)) {
            return false;
        }
        // Get config from file
        $aConfig = include($sFile);
        return self::Load($aConfig, $bRewrite);
    }

    /**
     * Load configuration array from given array
     *
     * @static
     * @param array $aConfig Массив конфига
     * @param bool $bRewrite Перезаписывать значения
     * @return bool|Config
     */
    static public function Load($aConfig, $bRewrite = true){
        if (!is_array($aConfig)) {
            return false;
        }
        self::getInstance()->SetConfig($aConfig, $bRewrite);
        return self::getInstance();
    }

    /**
     * Устанавливает значения конфига
     *
     * @param array $aConfig Массив конфига
     * @param bool $bRewrite Перезаписывать значения
     * @return bool
     */
    public function SetConfig($aConfig = array(), $bRewrite = true)
    {
        if (is_array($aConfig)) {
            if ($bRewrite) {
                $this->aConfig = $aConfig;
            } else {
                $this->aConfig = $this->ArrayEmerge($this->aConfig, $aConfig);
            }
            return true;
        }
        $this->aConfig = array();
        return false;
    }

    /**
     * Сливает ассоциативные массивы
     *
     * @param array $aArr1 Массив
     * @param array $aArr2 Массив
     * @return array
     */
    protected function ArrayEmerge($aArr1, $aArr2)
    {
        return $this->func_array_merge_assoc($aArr1, $aArr2);
    }

    /**
     * Сливает два ассоциативных массива
     *
     * @param array $aArr1 Массив
     * @param array $aArr2 Массив
     * @return array
     */
    protected function func_array_merge_assoc($aArr1, $aArr2)
    {
        $aRes = $aArr1;
        foreach ($aArr2 as $k2 => $v2) {
            $bIsKeyInt = false;
            if (is_array($v2)) {
                foreach ($v2 as $k => $v) {
                    if (is_int($k)) {
                        $bIsKeyInt = true;
                        break;
                    }
                }
            }
            if (is_array($v2) and !$bIsKeyInt and isset($aArr1[$k2])) {
                $aRes[$k2] = $this->func_array_merge_assoc($aArr1[$k2], $v2);
            } else {
                $aRes[$k2] = $v2;
            }
        }
        return $aRes;
    }

    /**
     * Ограничиваем объект только одним экземпляром
     *
     * @static
     * @return Config
     */
    static public function getInstance()
    {
        if (self::$aInstance !== null) {
            return self::$aInstance;
        } else {
            self::$aInstance = new self();
            return self::$aInstance;
        }
    }

    /**
     * Define constants using config-constant mapping
     *
     * @param  string $sKey Ключ
     * @return bool
     */
    static public function DefineConstant($sKey = '')
    {
        if ($aKeys = self::getInstance()->GetKeys()) {
            foreach ($aKeys as $key) {
                $sName = isset(self::$aMapper[$key])
                    ? self::$aMapper[$key]
                    : strtoupper(str_replace('.', '_', $key));
                if ((substr($key, 0, strlen($sKey)) == strtoupper($sKey))
                    && !defined($sName)
                    && (self::isExist($key))
                ) {
                    $cfg = self::get($key);
                    if (is_scalar($cfg) || $cfg === NULL) define(strtoupper($sName), $cfg);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Find all keys recursivly in config array
     *
     * @return array|bool
     */
    public function GetKeys()
    {
        $cfg = $this->GetConfig();
        if (!is_array($cfg)) {
            return false;
        }
        return $this->func_array_keys_recursive($cfg);
    }

    /**
     * Возвращает текущий полный конфиг
     *
     * @return array
     */
    public function GetConfig(){
        return $this->aConfig;
    }

    /**
     * Рекурсивный вариант array_keys
     *
     * @param  array $array Массив
     * @return array|bool
     */
    protected function func_array_keys_recursive($array)
    {
        if (!is_array($array)) {
            return false;
        } else {
            $keys = array_keys($array);
            foreach ($keys as $k => $v) {
                if ($append = $this->func_array_keys_recursive($array[$v])) {
                    unset($keys[$k]);
                    foreach ($append as $new_key) {
                        $keys[] = $v . "." . $new_key;
                    }
                }
            }
            return $keys;
        }
    }

    /**
     * Try to find element by given key
     * Using function ARRAY_KEY_EXISTS (like in SPL)
     *
     * Workaround for http://bugs.php.net/bug.php?id=40442
     *
     * @param  string $sKey Path to needed value
     * @return bool
     */
    static public function isExist($sKey)
    {
        if ($sKey == '') {
            return (count((array)self::getInstance()->GetConfig()) > 0);
        }

        $aKeys = explode('.', $sKey);
        $cfg = self::getInstance()->GetConfig();
        foreach ((array)$aKeys as $sK) {
            if (array_key_exists($sK, $cfg)) {
                $cfg = $cfg[$sK];
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * Retrive information from configuration array
     *
     * @param  string $sKey Ключ
     * @return mixed
     */
    static public function get($sKey = '')
    {
        // Return all config array
        if ($sKey == '') {
            return self::getInstance()->GetConfig();
        }

        return self::getInstance()->GetValue($sKey);
    }

    /**
     * Получает значение из конфигурации по переданному ключу
     *
     * @param  string $sKey Ключ
     * @return mixed
     */
    public function GetValue($sKey)
    {
        // Return config by path (separator=".")
        $aKeys = explode('.', $sKey);

        $cfg = $this->GetConfig();
        foreach ((array)$aKeys as $sK) {
            if (isset($cfg[$sK])) {
                $cfg = $cfg[$sK];
            } else {
                return null;
            }
        }

        $cfg = self::KeyReplace($cfg);
        return $cfg;
    }

    /**
     * Заменяет плейсхолдеры ключей в значениях конфига
     *
     * @static
     * @param string|array $cfg Значения конфига
     * @return array|mixed
     */
    static public function KeyReplace($cfg)
    {
        if (is_array($cfg)) {
            foreach ($cfg as $k => $v) {
                $k_replaced = self::KeyReplace($k);
                if ($k == $k_replaced) {
                    $cfg[$k] = self::KeyReplace($v);
                } else {
                    $cfg[$k_replaced] = self::KeyReplace($v);
                    unset($cfg[$k]);
                }
            }
        } else {
            if (preg_match('~___([\S|\.|]+)___~Ui', $cfg))
                $cfg = preg_replace_callback(
                    '~___([\S|\.]+)___~Ui',
                    create_function('$value', 'return Config::Get($value[1]);'),
                    $cfg
                );
        }
        return $cfg;
    }
}