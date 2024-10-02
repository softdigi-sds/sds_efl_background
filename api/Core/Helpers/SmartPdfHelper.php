<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Helpers;
/**
 * Description of Validator
 *
 * @author kms
 */
class SmartPdfHelper {



static public function createDirectories($dirpath){         
          $str = explode("/", $dirpath);
         // var_dump($str);
          $dir = '';
          foreach($str as $dpath){
              $ext = self::getExt($dpath);
            //  echo "ext".$dpath." - ".$ext."<br/>";
              if(strlen($ext) < 1){
              $dir .= $dpath ;
             // echo "".$dir;
               if(!file_exists($dir) && strlen($dir) > 4){ 
                mkdir($dir);
                
               }
              }
          }
     }
    
    static public function getExt($path){
         $ext = pathinfo($path, PATHINFO_EXTENSION);
         if(str_getcsv(trim($ext)) > 1){
             return $ext;
         }else{
             return NULL;             
         }
     }
    //put your code here
    static public function genPdf($html,$location,$settings=[]){
         $location_full = SmartFileHelper::getDataPath(). $location;
         SmartFileHelper::createDirectoryRecursive($location_full);
         ini_set('memory_limit','2048M');
         $format = isset($settings["pagesize"]) ? $settings["pagesize"] : "A4";
         $font_size = isset($settings["fontsize"]) ? $settings["fontsize"] : 10;
         $unicode = isset($settings["unicode"]) && intval($settings["unicode"])==1 ? true : false;
         $footer = isset($settings["footer"])  ? $settings["footer"] : "";
         $header = isset($settings["header"])  ? $settings["header"] : "";
         require_once "vendor/vendor/autoload.php";
         $config = [
           'mode' => 'utf-8',
           'format' =>$format,
           'default_font_size' => $font_size,
           //'tempDir' => '/tmp',          
          ];
          $mpdf = new \Mpdf\Mpdf($config);
          $mpdf->autoScriptToLang = true;
          $mpdf->autoLangToFont = true;
          //$mpdf->SetFont('freeserif');  
          if(strlen($header) > 1){ 
             $mpdf->SetHTMLHeader($header);
          }
          if(strlen($footer) > 1){
             $mpdf->SetHTMLFooter($footer);
          }else{
            $mpdf->setFooter('{PAGENO} / {nb}');
          }      
         
          if($unicode===true){
              $html = utf8_encode($html);
          }
        $mpdf->WriteHTML($html);         
        $mpdf->Output($location_full,'F'); 
    }

}