<?php

namespace Core\Helpers;

// const
define("DATA","/data/igcdoc/");
// mpdf path 
define("MPDF_PATH","../../../common/vendor/autoload.php");
// mpdf path
define("MPDF_VERSION","9");
/**
 * Description of PdfHelper
 *
 *
 */
class PdfHelper {
    
    static public function createDirectories($dirpath){         
          $str = explode("/", $dirpath);
         var_dump($str);
          $dir = '';
          foreach($str as $dpath){
              $ext = self::getExt($dpath);
            //  echo "ext".$dpath." - ".$ext."<br/>";
              if(strlen($ext) < 1){
              $dir .= "/". $dpath ;
               if(!file_exists($dir)){ mkdir($dir);}
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
    //
    static public function genPdf($html,$location,$settings=[]){
         $location_full = DATA . $location;
        //  echo $location_full;
        //  exit();
         self::createDirectories($location_full);
         ini_set('memory_limit','2048M');
         $format = isset($settings["pagesize"]) ? $settings["pagesize"] : "A4";
         $font_size = isset($settings["fontsize"]) ? $settings["fontsize"] : 10;
         $unicode = isset($settings["unicode"]) && intval($settings["unicode"])==1 ? true : false;
         require_once MPDF_PATH;
         $config = [
           'mode' => 'utf-8',
           'format' =>$format,
           'default_font_size' => $font_size,
           'tempDir' => '/tmp',          
          ];
          $mpdf = new \Mpdf\Mpdf($config);
          $mpdf->autoScriptToLang = true;
          $mpdf->autoLangToFont = true;
          $mpdf->SetFont('freeserif');
          if(MPDF_VERSION ==8){
                $mpdf=new \mPDF($mode='utf-8',$format,$default_font_size=10);  
          }
           $mpdf->setFooter('{PAGENO} / {nb}');
            if($unicode===true){
                $html = utf8_encode($html);
            }
            $mpdf->WriteHTML($html);
          //  echo $html;
           // exit();
        $mpdf->Output($location_full,'F'); 
    }
}
