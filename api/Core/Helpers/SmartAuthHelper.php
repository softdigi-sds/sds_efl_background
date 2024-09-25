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

use Core\Helpers\SmartLogger as Logger;

use PDO;

class SmartAuthHelper {

    private static $_instance = null;

    public static function get_instance(){
        if(!isset(self::$_instance)){
           self::$_instance = new SmartAuthHelper();
        }
        return self::$_instance;
    }
    /**
     * 
     */
    public static function isLoggedIn(){
        return isset($GLOBALS["USER"]) && isset($GLOBALS["USER"]->USER) ? true : false;
    }
    /**
     * 
     */
    public static function getLoggedInUserData(string $param){
       // var_dump($GLOBALS["USER"]);
        return isset($GLOBALS["USER"]) && isset($GLOBALS["USER"]->USER) && isset($GLOBALS["USER"]->USER->{$param}) ? $GLOBALS["USER"]->USER->{$param} : null;
    }
    /**
     * 
     */
    public static function getLoggedInId(){
       $id = self::getLoggedInUserData("ID");
      // echo " logged in ID " . $id . "<br/>";
       return $id!==NULL ? $id : 0; 
    }

    public static function getLoggedInUserId(){
        $id = self::getLoggedInUserData("euserid");
        return $id!==NULL ? $id : 0; 
     }

    /**
     * 
     */
    public static function getLoggedInUserName(){
        $id = self::getLoggedInUserData("ename");
        return $id!==NULL ? $id : 0; 
     }

    /**
     * 
     */
    public static function getRoles(){
        $roles = self::getLoggedInUserData("role");
        return $roles!==NULL ? $roles : [];
    }

    public static function getRolesArr(){
        $roles = self::getLoggedInUserData("roles");
        return $roles!==NULL ? $roles : [];
    }

   
    /**
     * 
     */
    public static function checkRole(array $roles){
        $user_roles = self::getRoles();
       // var_dump($user_roles);
        $result = array_intersect($user_roles, $roles);
        return (!empty($result)) ? true : false;
    }

}