<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Core\Helpers;

/**
 * Description of SmartLogger
 *
 * @author SUBBA RAJU
 */
class SmartLogger
{
    //put your code here
    private static $_instance = null;

    const INFO = "INFO";
    const WARNING = "INFO";
    const ERROR = "ERROR";
    const AUTH = "AUTH";


    //const UNAUTH="UNAUTH";

    private $_msg = "";
    private $_mode = "";
    private $_module = "";
    private $_user = "";

    //private $_logged_in = 0;
    // $obj->_logged_in

    public static function get_instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new SmartLogger();
        }
        return self::$_instance;
    }
    /**
     * 
     * @param type $msg
     * @param type $module
     */
    static public function info($msg, $module = null, $user = "")
    {
        $obj = self::get_instance();
        $obj->_msg = $msg;
        $obj->_mode = self::INFO;
        $obj->_module =  $module;
        $obj->_user = $user;
        $obj->write_log_file();
    }
    /**
     * 
     * @param type $msg
     * @param type $module
     */
    static public function error($msg, $module = null)
    {
        $obj = self::get_instance();
        $obj->_msg = $msg;
        $obj->_mode = self::ERROR;
        $obj->_module =  $module;
        $obj->write_log_file();
    }

    /**
     * 
     * @param type $msg
     * @param type $module
     */
    static public function warning($msg, $module = null)
    {
        $obj = self::get_instance();
        $obj->_msg = $msg;
        $obj->_mode = self::WARNING;
        $obj->_module =  $module;
        $obj->write_log_file();
    }

    /**
     * 
     * @param type $msg
     * @param type $module
     */
    static public function Auth($msg, $module = null)
    {
        $obj = self::get_instance();
        $obj->_msg = $msg;
        $obj->_mode = self::AUTH;
        $obj->_module =  $module;
        $obj->write_log_file();
    }


    static public function readLogFile($year, $month)
    {
        $obj = self::get_instance();
        if ($month < 10) {
            $month = "0" . intval($month);
        }
        $log_path = $year . "-" . $month;
        return $obj->read_log($log_path);
    }

    private function read_log($log_path)
    {
        $logFile = $this->prepare_log_file($log_path);
        $out = [];
        if (file_exists($logFile)) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines !== false) {
                foreach ($lines as $line) {
                    $out[] = json_decode($line);
                }
            }
        }
       
        return  array_reverse($out);
    }

    /**
     * 
     * @return type
     */
    private function prepare_log_message()
    {
        $db = new \stdClass();
        $db->datetime = date("Y-m-d H:i:s");
        $db->remoteIp = self::get_client_ip();
        $db->module = $this->_module;
        $db->mode = $this->_mode;
        $db->msg = $this->_msg;
        $db->emp_name = strlen($this->_user) > 1 ? $this->_user : SmartAuthHelper::getLoggedInUserName();
        //$db->output = $this->output;
        return json_encode($db);
    }

    /**
     * 
     * @return string
     */
    private function prepare_log_file($file_name = "")
    {
        // echo "LOG_DIR =" . LOG_DIR . "<br/>";
        $log_dir =  sys_get_temp_dir();
        // 
        // var_dump($_ENV);
        if (isset($_ENV["LOG_DIR"])) {
            $log_dir = $_ENV["LOG_DIR"];
        }
        $log_dir = $log_dir . "log/";
        //echo $log_dir;
        if (!file_exists($log_dir)) {
            mkdir($log_dir);
        }
        $log_file_path = strlen($file_name) > 1 ? $file_name : date("Y-m");
        // 
        $logPath = $log_dir . $log_file_path . ".log";

        return $logPath;
    }

    /**
     * 
     */
    private function write_log_file()
    {

        $logFile = $this->prepare_log_file();
        // echo $logFile;
        $message = $this->prepare_log_message();
        try {
            $file_pointer = fopen($logFile, 'a') or die("Unable to open file!");
            fwrite($file_pointer, $message . PHP_EOL);
            fclose($file_pointer);
        } catch (\Exception $ex) {
            // var_dump($ex);
            exit();
        }
    }

    public static function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}
