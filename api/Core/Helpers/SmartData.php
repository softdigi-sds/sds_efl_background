<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Helpers;

/**
 * Description of Data
 * 
 *  class helps to get the data from post with specified type 
 *
 * @author kms
 */
class SmartData {
    //put your code here
    
    static public function post_data($name,$type){
        switch($type){
            case "INTEGER" : return self::post_int_data($name); 
            case "FLOAT" : return self::post_float_data($name);
            case "EMAIL" : return self::post_email_data($name);
            case "ARRAY" : return self::post_array_data($name);
            case "ARRAY_TO_STRING" : return self::post_array_to_string_data($name);
            case "SELECT_VALUE" : return self::post_select_value($name);
            case "DATE" : return self::post_date_data($name);
            case "STRING" : 
            default : return self::post_string_data($name);
        }
    }

    static public function post_date_data($name){
        if(isset($_POST[$name])){
            if(is_array($_POST[$name])){
                // it is array format set to date format 
                $date_arr = $_POST[$name];                           
                return $date_arr["year"] ."-" . $date_arr["month"] . "-" . $date_arr["day"];
            }else{
                // date format has to be changed to set to db
                return $_POST[$name];
            }
        }else{
            return null;
        }
        return  isset($_POST[$name]) ? htmlspecialchars($_POST[$name]) : null; 
    }

    static public function post_select_value($name){
        $id_arr = self::post_data($name,"ARRAY");
        return isset($id_arr["value"]) ? intval($id_arr["value"]):0;
    }

    /**
     * 
     * @param type $name
     * @return type
     */
    static public function post_string_data($name){      
          return  isset($_POST[$name]) ? htmlspecialchars($_POST[$name]) : null;
    }
    /**
     * 
     * @param type $name
     * @return type
     */
    static public function post_int_data($name){
        return  isset($_POST[$name]) ? filter_var ( $_POST[$name], FILTER_SANITIZE_NUMBER_INT) : null;
    }
   /**
    * 
    * @param type $name
    * @return type
    */
    static public function post_float_data($name){
        return  isset($_POST[$name]) ? filter_var ( $_POST[$name], FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION) : null;
    }
   /**
    * 
    * @param type $name
    * @return type
    */
    static public function post_email_data($name){
        return  isset($_POST[$name]) ? filter_var ( $_POST[$name], FILTER_SANITIZE_EMAIL) : null;
    }
    /**
     * 
     * @param type $name
     * @return type
     */
    static public function post_array_data($name){
        return  isset($_POST[$name]) && is_array($_POST[$name]) ? $_POST[$name] : []; 
    }
    /**
     * 
     */
    static public function post_array_to_string_data($name){
       $arr = self::post_array_data($name);
       return !empty($arr) && is_array($arr) ? implode(", " , $arr) : "";
    }
    
    
}
            