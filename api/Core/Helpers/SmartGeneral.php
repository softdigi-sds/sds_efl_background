<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Helpers;
// others
use \Firebase\JWT\JWT;
/**
 * Description of Validator
 *
 * @author kms
 */
class SmartGeneral {

    static public function jwt_encode($payload){
        $secret_key =  $_ENV["JWT"];
        return  JWT::encode($payload, $secret_key, 'HS256');
    }

    static public function hashString($string){

    }
    /**
     * 
     */
    static public function hashPassword($password){
        return  password_hash($password, PASSWORD_DEFAULT);
    }
    /**
     * 
     */
    static public function getCurrentDbDate(){
        return date("Y-m-d");
    }
    /**
     * 
     */
    static public function getCurrentDbDateTime(){
        return date("Y-m-d H:i:s");
    }

    static public function getMonth(){
        return date("m");
    }
    static public function getYear(){
        return date("Y");
    }
    /**
     * 
     */
    static public function getExt(string $fileName){
       return pathinfo($fileName, PATHINFO_EXTENSION);
    }
   
}