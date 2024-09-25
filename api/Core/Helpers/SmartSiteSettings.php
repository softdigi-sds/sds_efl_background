<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Database
 *
 * @author kms
 */
namespace Core\Helpers;

use Core\BaseHelper;


class SmartSiteSettings {

     /**
     * 
     */
    public SmartModel $db;

    private static $_instance = null;

    private $_site_settings_table="sd_site_settings";

    public static function get_instance(){
        if(!isset(self::$_instance)){
           self::$_instance = new SmartSiteSettings();
        }
        return self::$_instance;
    }
    /**
     * 
     */
  private function  load_settings(){
     $modal = new SmartModel();
     $db =  new BaseHelper( $modal);
     $data = $db->getAll([],$this->_site_settings_table,"","", "", [], false, [], false);
     $out = [];
     foreach($data as $obj){
        $out[$obj->setting_name] = $obj->setting_value;
     }
     $GLOBALS["SD_SITE_SETTINGS"] = $out;
  }
   /**
    * 
    */
  static public function loadSettings(){
    $obj = new self();
    if(strlen($obj->_site_settings_table)>3){
        $obj->load_settings();
    }    
  }
  /**
   * 
   */
  static public function getSetting(string $index,$default=""){
    return isset($GLOBALS["SD_SITE_SETTINGS"]) &&  isset($GLOBALS["SD_SITE_SETTINGS"][$index]) ? $GLOBALS["SD_SITE_SETTINGS"][$index] : $default;
  }


}