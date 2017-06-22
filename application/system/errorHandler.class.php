<?php
/**
 * Created by PhpStorm.
 * User: Azamat
 * Date: 08.06.2017
 * Time: 1:07
 */

class ErrorHandler {

    private $aErrorTemplate = [
        'head' => '<div style="margin:10px; background: #8b9dc3; color: #f7f7f7; font-family: \'Arial Black\', \'Arial Normal\', Gadget, sans-serif;">
            <div style="color: white; padding: 15px; font-weight: 600; font-family: \'Arial Black\', \'Arial Bold\', Gadget, sans-serif; background: #3b5998;">
            Произошла ошибка!
            </div>
            <div style="padding: 15px;">',
        'footer' => '</div></div>'
    ];

    public function register() {
        set_error_handler([$this, 'errorHandler']);
        register_shutdown_function([$this, 'fatalErrorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
    }

    public function errorHandler($iErrNo = false, $sErrStr = false, $sErrFile = false, $iErrLine = false) {
        if($iErrNo && $sErrStr && $sErrFile && $iErrLine) {
            $this->showError($iErrNo, $sErrStr, $sErrFile, $iErrLine);
            return true;
        }
        return false;
    }

    public function showError($iErrNo, $sErrStr, $sErrFile, $iErrLine) {
        //$this->putErrorLog($iErrNo, $sErrStr, $sErrFile, $iErrLine);
        $sOutput = $this->aErrorTemplate['head'];
        $sOutput .= '<div>Номер ошибки: '.$iErrNo.'</div>';
        $sOutput .= '<div>Ошибка: '.$sErrStr.'</div>';
        $sOutput .= '<div>Файл: '.$sErrFile.'</div>';
        $sOutput .= '<div>Строка: '.$iErrLine.'</div>';
        $sOutput .= $this->aErrorTemplate['footer'];
        echo $sOutput;
    }

    public function fatalErrorHandler() {
        if(!empty($aError = error_get_last()) AND $aError['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)) {
            ob_get_clean();
            $this->showError($aError['type'], $aError['message'], $aError['file'], $aError['line']);
        }
    }

    public function exceptionHandler(\Exception $e) {
        $this->showError(get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());
        return true;
    }

    private function putErrorLog($iErrNo, $sErrStr, $sErrFile, $iErrLine) {
        $sFileName = Config::get('log.dir') . 'errors_'.date('d_m_Y').'.log';
        $sLog = date('[d.m.Y H:i:s]') .' Номер:' . $iErrNo . ' Ошибка:' . $sErrStr . ' Файл:' . $sErrFile . ' Строка:' . $iErrLine.PHP_EOL;
        if(file_exists($sFileName)) {
            $sFile = file_get_contents($sFileName);
            $sFile .= $sLog;
            file_put_contents($sFileName, $sFile, FILE_APPEND);
        } else {
            file_put_contents($sFileName, $sLog);
        }
    }

}