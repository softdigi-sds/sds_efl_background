<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Helpers;

use Core\BaseHelper;
use Core\Helpers\SmartConst;

//
use Site\Helpers\TableHelper as Table;

/**
 * Description of Data
 * 
 *  class helps to get the data from post with specified type 
 *
 * @author kms
 */
class SiteImageHelper extends BaseHelper
{

    static public function getServer()
    {
        if (isset($_ENV["API_URL"])) {
            return $_ENV["API_URL"];
        }
        return "";
    }

    static public function getImages()
    {

        $out = [
            "LOGO_IMAGE" => "logo.png",
            "QR_CODE" => ""
        ];
        return $out;
    }

    static public function replaceImages($html, $img = [])
    {
        $images = self::getImages();
        $_images = array_merge($images, $img);
        $api_url = self::getServer();
        foreach ($_images as $placeholder => $value) {
            $placeholder_modified = "[" . $placeholder . "]";
            if (strpos($html, $placeholder_modified) !== false) {
                $url =  $api_url . "/images/" . $value;
                $html = str_replace(
                    trim($placeholder_modified),
                    $url,
                    $html
                );
            }
        }
        return $html;
    }
}
