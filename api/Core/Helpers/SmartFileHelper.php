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


class SmartFileHelper {

    static public function createDirectoryRecursive($fullFilePath) {
        // Extract the directory path from the full file path
        $directoryPath = dirname($fullFilePath);
    
        // Split the directory path into an array of individual directories
        $directories = explode(DS, $directoryPath);
    
        // Initialize a variable to keep track of the current directory being processed
        $currentDirectory = '';
    
        // Iterate through the directories and create them recursively
        foreach ($directories as $directory) {
            $currentDirectory .= $directory . DS;
            if (!is_dir($currentDirectory)) {
                if (!mkdir($currentDirectory, 0755, true)) { // You can adjust the permissions (0755) as needed
                    return false; // Return false if directory creation fails
                }
            }
        }    
        return true; // Return true if all directories were created successfully
    }
    /**
     * 
     */
    static public function  getDataPath(){
        if(isset($_ENV["DATA_PATH"])){
            return $_ENV["DATA_PATH"];
        }else{
            \CustomErrorHandler::triggerInternalError("Invalid Data Path");
        }
    }
    /**
     * 
     */
    public static function moveSingleFile(string $index,string $store_path){
         // make full path 
         $dest_path = self::getDataPath() . $store_path;
         // get stored extension
         $ext = SmartGeneral::getExt($store_path);
         if(strlen($ext) < 1){
            // get the extenstion from upload file and attach to full path before creation of directories
            $name = isset($_FILES[$index]) ? $_FILES[$index]["name"] : "";
            $ext =SmartGeneral::getExt($name);
            $dest_path = $dest_path . "." . $ext;
         }
         // create a directory if not exits 
         self::createDirectoryRecursive($dest_path);
         // try to move the file 
         $temp_path = isset($_FILES[$index]) ? $_FILES[$index]["tmp_name"] : "";
         // check file availble in temporray directory
         if(file_exists($temp_path)){
            move_uploaded_file($temp_path ,$dest_path);
            return basename($dest_path);
         }else{
            \CustomErrorHandler::triggerInternalError("Error Uploading File");
         }
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
    public static function getRoles(){
        $roles = self::getLoggedInUserData("role");
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