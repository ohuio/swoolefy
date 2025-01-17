<?php
/**
+----------------------------------------------------------------------
| swoolefy framework bases on swoole extension development, we can use it easily!
+----------------------------------------------------------------------
| Licensed ( https://opensource.org/licenses/MIT )
+----------------------------------------------------------------------
| Author: bingcool <bingcoolhuang@gmail.com || 2437667702@qq.com>
+----------------------------------------------------------------------
*/

namespace Swoolefy\Core;

class SwoolefyException {
	/**
	 * fatalError 致命错误捕获,两种情况触发
     * a)代码中执行exit(),die()原生函数，在swoole中是禁止使用这个两个函数的，因为会导致worker退出
     * b)代码中发生异常，throw
     * c)代码执行完毕，由于在这里是worker常驻内存的，register_shutdown_function所注册是在worker进程中的，所以代码执行完毕不会触发，在php-fpm中代码会执行
	 * @return 
	 */
    public static function fatalError() {
        if($e = error_get_last()) {
            switch($e['type']){
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    @ob_end_clean();
                    static::shutHalt($e['message'], $errorType = 'error');
                break;
            }
        }
    }

	/**
     * appException 自定义异常处理
     * @param mixed $e 异常对象
     */
    public static function appException($e) {
        $error = array();
        $error['message']   =   $e->getMessage();
        $trace              =   $e->getTrace();
        if('E' == $trace[0]['function']) {
            $error['file']  =   $trace[0]['file'];
            $error['line']  =   $trace[0]['line'];
        }else{
            $error['file']  =   $e->getFile();
            $error['line']  =   $e->getLine();
        }
        $error['trace']     =   $e->getTraceAsString();
        $errorStr = $error['file'].' 第'.$error['line'].'行:'.$error['message'];
        static::shutHalt($errorStr);
    }

    /**
     * appError 获取用户程序错误
     * @param    int       $errno
     * @param    string    $errstr
     * @param    string    $errfile
     * @param    int       $errline
     * @return   void
     */
    public static function appError($errno, $errstr, $errfile, $errline) {
    	$errorStr = "$errfile 第 $errline 行: $errstr";
      	switch ($errno) {
            case E_ERROR:
          		static::shutHalt($errorStr, $errorType = 'notice');
          		break;
            case E_WARNING:
          		static::shutHalt($errorStr, $errorType = 'warning');
          		break;
            case E_NOTICE:
            	static::shutHalt($errorStr, $errorType = 'notice');
           		break;
            default:
            break;
      	}
      return ;
    }

    /**
     * shutHalt 输出错误日志
     * @param string      $errorMsg
     * @param string $errorType
     */
    public static function shutHalt($errorMsg, $errorType = 'error') {
        if(!defined('LOG_PATH')) {
            define('LOG_PATH', START_DIR_ROOT.DIRECTORY_SEPARATOR.APP_NAME);
        }
        $logFilePath = rtrim(LOG_PATH,'/').'/runtime.log';
        if(is_file($logFilePath)) {
            $logFilesSize = filesize($logFilePath);
        }
        // 定时清除这个log文件
        if($logFilesSize > 1024 * 20) {
            @file_put_contents($logFilePath,'');
        }

        $log = new \Swoolefy\Tool\Log;

        switch($errorType) {
            case 'error':
                  $log->setChannel('Application')->setLogFilePath($logFilePath)->addError($errorMsg);
                 break;
            case 'warning':
                  $log->setChannel('Application')->setLogFilePath($logFilePath)->addWarning($errorMsg);
                 break;
            case 'notice':
                  $log->setChannel('Application')->setLogFilePath($logFilePath)->addNotice($errorMsg);
                 break;
            case 'info':
                 $log->setChannel('Application')->setLogFilePath($logFilePath)->addInfo($errorMsg);
                 break;
        }
        if(in_array(SWOOLEFY_ENV, [SWOOLEFY_DEV, SWOOLEFY_GRA])) {
             throw new \Exception(_each($errorMsg));
        }
       
        return;
    }
}