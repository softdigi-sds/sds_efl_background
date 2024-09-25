<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Core\Helpers\SmartDatabase;

/**
 * Description of CustomEroorHandler
 *
 * @author kms
 */
class CustomErrorHandler extends ErrorException
{
    //put your code here
    private $_errno;
    private $_errstr;
    private $_error_file;
    private $_error_line;

    private static $_instance = null;

    public static function get_instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    static private function rollBackDb(){
        $db = SmartDatabase::get_instance();
        $db->RollBack();
    }
    /**
     * 
     */
    static private function display_output($json, $error_code)
    {
       //  debug_print_backtrace();
        self::rollBackDb();
        ob_clean();
        http_response_code($error_code);
        echo json_encode($json);
        exit();
    }

    /**
     * 
     */
    static public function triggerInvalid($err)
    {
        $json = new stdClass();
        $json->status = "Validation Error";
        $json->message = is_array($err) ? json_encode($err) : $err;
        self::display_output($json, 400);
    }
    /**
     * 
     */
    static public function triggerInternalError($err)
    {
        $json = new stdClass();
        $json->status = "Internal Error";
        $json->message = is_array($err) ? json_encode($err) : $err;
        self::display_output($json, 500);
    }
    /**
     * 
     */
    static public function triggerDbError($err)
    {
        $json = new stdClass();
        $json->status = "DB Error";
        $json->message = is_array($err) ? json_encode($err) : $err;
        self::display_output($json, 500);
    }

    /**
     * 
     */
    static public function triggerUnAuth()
    {
        $json = new stdClass();
        $json->status = "Unauthorized Access";
        $json->message= " You Cannot Access This module";
        self::display_output($json, 401);
    }
    /**
     * 
     */
    static public function triggerInvalidRequest()
    {
        $json = new stdClass();
        $json->status = "Invalid Request Type";
        $json->message= " Request type is not Invalid";
        self::display_output($json, 405);
    }
    /**
     * 
     */
    static public function triggerNotFound($msg = "")
    {
        $json = new stdClass();
        $json->status = "Not Found";
        $json->message= strlen($msg) > 2 ? $msg : "Not Found";
        self::display_output($json, 405);
    }
    /**
     * 
     */
    static public function exceptionHandler($ex)
    {
        $json = new stdClass();
        $json->status = "Internal Server Error";
        $json->message = $ex->getMessage();
        self::display_output($json, 500);
    }
    /**
     * 
     */
    static public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        // Your custom error handling logic
        $json = new stdClass();
        $json->status = "Internal Server Error";
        $json->message =  $errstr;
        self::display_output($json, 500);
    }


    // fatolr error for shutdown cases
    static public function handleShutdownFunction()
    {
        $error = error_get_last(); // Retrieve the last error that occurred

        // Check if it's a fatal error
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $obj = new stdClass();
            $obj->msg = $error['message'];
            $obj->_error_line = $error["line"];
            $obj->_error_file = $error['file'];
            self::display_output($obj, 500);
        }
        die();
    }

}
// we are handling errors here 
set_error_handler(array("CustomErrorHandler", "errorHandler"));

set_exception_handler(array("CustomErrorHandler", "exceptionHandler"));

register_shutdown_function(array("CustomErrorHandler", "handleShutdownFunction"));
