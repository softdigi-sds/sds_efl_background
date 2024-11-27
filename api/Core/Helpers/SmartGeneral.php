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

    static public function convertTwoDigits($number, $words) {
        $num = intval($number);
        if ($num < 20) {
            return $words[$num];
        } else {
            return $words[($num - $num % 10)] . ' ' . $words[$num % 10];
        }
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
        $numStr = strval($number);
        $length = strlen($numStr);
        $placeLevel = 0;
    
        // Handle first 3 digits separately to match the Indian numbering system
        $prefixLength = $length % 2 === 0 ? 2 : 1;
        if ($length > 3) {
            $output .= self::convertTwoDigits(substr($numStr, 0, $prefixLength), $words) . ' ';
            if ($prefixLength === 1) {
                $placeLevel = 1; // Thousand level
            } else {
                $placeLevel = 2; // Lakh level
            }
        }
        
        $numStr = substr($numStr, $prefixLength);
        $numGroups = str_split($numStr, 2);
    
        foreach ($numGroups as $group) {
            if (intval($group) > 0) {
                $output .= self::convertTwoDigits($group, $words) . ' ' . ($placeLevel > 0 ? $places[$placeLevel] . ' ' : '');
            }
            $placeLevel++;
        }
    
        return trim(preg_replace('/\s+/', ' ', $output));
    }

    static public function convertToIndianCurrency($number) {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(0 => '', 1 => 'one', 2 => 'two',
            3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
            7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve',
            13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
            16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
            19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
            40 => 'forty', 50 => 'fifty', 60 => 'sixty',
            70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
        $digits = array('', 'hundred','thousand','lakh', 'crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            } else $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
        return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
    }
}
