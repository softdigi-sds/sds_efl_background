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
class SmartGeneral
{

    static public function jwt_encode($payload)
    {
        $secret_key =  $_ENV["JWT"];
        return  JWT::encode($payload, $secret_key, 'HS256');
    }

    static public function hashString($string) {}
    /**
     * 
     */
    static public function hashPassword($password)
    {
        return  password_hash($password, PASSWORD_DEFAULT);
    }
    /**
     * 
     */
    static public function getCurrentDbDate()
    {
        return date("Y-m-d");
    }
    /**
     * 
     */
    static public function getCurrentDbDateTime()
    {
        return date("Y-m-d H:i:s");
    }

    static public function getMonth()
    {
        return date("m");
    }
    static public function getYear()
    {
        return date("Y");
    }
    /**
     * 
     */
    static public function getExt(string $fileName)
    {
        return pathinfo($fileName, PATHINFO_EXTENSION);
    }

    static public function getEnv(string $index_name)
    {
        return isset($_ENV[$index_name]) ? $_ENV[$index_name] : "";
    }

    static public function convertToWords($number)
    {
        $words = array(
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten', 
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 
            15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 
            20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 
            60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        );
    
        $places = ['', 'Thousand', 'Lakh', 'Crore'];
        
        if ($number == 0) {
            return 'Zero';
        }
    
        $output = '';
        $num = str_pad($number, ceil(strlen($number) / 2) * 2, '0', STR_PAD_LEFT);
        $numArray = str_split($num, 2);
        $placeLevel = count($numArray) - 1;
    
        foreach ($numArray as $index => $pair) {
            $pair = intval($pair);
            if ($pair > 0) {
                if ($pair < 20) {
                    $output .= $words[$pair] . " ";
                } else {
                    $output .= $words[floor($pair / 10) * 10] . " " . $words[$pair % 10] . " ";
                }
                if ($placeLevel > 0) {
                    $output .= $places[$placeLevel] . " ";
                }
            }
            $placeLevel--;
        }
        return trim(preg_replace('/\s+/', ' ', $output));
    }

    static public function convertToIndianCurrency($number) {
        $no = floor($number);
        $decimal = round($number - $no, 2) * 100;
        $decimalPart = ($decimal > 0) ? " and " . self::convertToWords($decimal) . " Paisa" : "";    
        return self::convertToWords($no) . " Rupees" . $decimalPart;
    }
}
