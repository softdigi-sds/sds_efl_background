<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Core\Helpers\SmartAuthHelper;

error_reporting(E_ALL);
// directory seperator
define("DS","/");

include "CorsEnable.php";
// inlcude the error handle which handles all the files
require_once 'Core/CustomErrorHandler.php';
require_once('vendor/jwt/JWT.php');
require_once('vendor/jwt/Key.php');
require_once('vendor/jwt/BeforeValidException.php');
require_once('vendor/jwt/ExpiredException.php');
require_once('vendor/jwt/SignatureInvalidException.php');
// load env
require_once "Core/SmartEnvLoader.php";
//
require_once "Core/SmartAuthLoader.php";
// load the autoloader class to laod the relevent classes 
require_once 'Core/AutoLoader.php';
//
require_once "Core/Router.php";
//

// route
//use \Core\Router;
/*
// load the router class 
$router_paramters = \Core\Router::add_route();
// extract route parameters 
extract($router_paramters);
//
$mod_class_name = "\\Site\\Controller\\" . $Mod_Name;
// check for class exists 
if (!class_exists("\\Site\\Controller\\" . $Mod_Name)) {
    trigger_error("Module With Name:$Mod_Name Not Avilable",E_USER_NOTICE);
}
// 
$Frame_Class_Instance = new $mod_class_name();
// method exits
if(!method_exists($Frame_Class_Instance, $Func_Name)){
    trigger_error("API funciton: $Func_Name Not Availble Module With Name:$Mod_Name",E_USER_NOTICE);
}
// global pametres setting
\Core\Helpers\SmartGLobals::setGlobals();
//$logged_in = 1;
// check for authentication i.e whether the module is api is acceptable beore login ro any role based and through unauthroised error
if(method_exists("\\Site\\Authentication", "check_authentication")){
        Site\Authentication::check_authentication($Mod_Name);
}
//var_dump($_POST);
// all are availble execute 
call_user_func(array($Frame_Class_Instance, $Func_Name), $Param_var);
// over make die of script 
die();

*/
//use Core\Router;

class IndexMain
{

    private $_route;
    //
    private $_auth_loader;
    // 
    private $_controller_name;
    //
    private $_action_name;
    //
    private $_parameters;
    //
    private $_method="GET";
    //
    private $_auth=[];
    //
    private $_base_path = "\\Site\\Controller\\";
    // controller instance
    private $_controller;
    // input parameters 
    private $_input_params=[];

    function __construct()
    {
    
        // get route parameters
        $this->_route = \Core\Router::getRoute();
        //       
        // load api function
        $this->load_api_function();
    }

    private function check_api_auth(){
        if(!\Core\Helpers\SmartAuthHelper::checkRole($this->_auth)){
            \CustomErrorHandler::triggerUnAuth();
        }
    }

    private function load_api_function()
    {
        // get controller
        $controller_arr = isset($this->_route[0]) ? $this->_route[0] : [];
        // var_dump($controller_arr);
        // exit();
        //  
        if(count($controller_arr)===5){            
            list($this->_method,$this->_auth,$this->_controller_name,$this->_action_name,$this->_input_params) =  $controller_arr;            
        } else if(count($controller_arr)===4){
            // added this to send some parameters to controller
            if(is_array($controller_arr[3])){
                list($this->_method,$this->_controller_name,$this->_action_name,$this->_input_params) =  $controller_arr;
            }else{
                list($this->_method,$this->_auth,$this->_controller_name,$this->_action_name) =  $controller_arr;
            }            
        }else if(count($controller_arr)===3){
            list($this->_method,$this->_controller_name,$this->_action_name) =  $controller_arr;
        } else if(count($controller_arr)===2) {
            list($this->_controller_name,$this->_action_name) =  $controller_arr;
        }else{
			\CustomErrorHandler::triggerNotFound("Invalid API Action.");
		} 
        if(!empty($this->_auth)){
            // check authorization first
            $this->check_api_auth();
        }
        //check method type
        $this->check_method();   
        // load the controller
        $this->_parameters = isset($this->_route[1]) ? $this->_route[1] : [];
        if(!empty($this->_input_params)){
            $this->_parameters = array_merge($this->_parameters,$this->_input_params);
        }
        // load settings before controller calling
        \Core\Helpers\SmartSiteSettings::loadSettings();

        // loading
        $this->load_controller();
        // action controller
        $this->load_action();
    }
    /**
     * 
     */
    private function check_method(){
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : "";
        if($method!==$this->_method){
            ob_clean();
           \CustomErrorHandler::triggerInvalidRequest();
        }
    }
    /**
     * 
     */
    private function load_controller()
    {
        $mod_class_name = $this->_base_path . $this->_controller_name;
        // check for class exists 
        if (!class_exists($this->_base_path . $this->_controller_name)) {
            \CustomErrorHandler::triggerNotFound("Module With Name: Not Available", E_USER_NOTICE);
        }
        $this->_controller = new $mod_class_name($this->_parameters);
        
    }

    private function load_action()
    {       
        // method exits
        if (!method_exists($this->_controller, $this->_action_name)) {
            \CustomErrorHandler::triggerNotFound("API function:  Not Available Module With Name:");
        }
        // call the action
        call_user_func(array( $this->_controller, $this->_action_name),  $this->_parameters );
    }
}



new IndexMain();

die();
