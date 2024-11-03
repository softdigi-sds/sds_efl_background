<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Helpers;

require_once 'vendor/phpqrcode/qrlib.php';
/**
 * Description of Validator
 *
 * @author kms
 */
class SmartQrCodeHelper
{

  static function generateQrCode($text, $filePath)
  {
    $finalPath =  SmartFileHelper::getDataPath() . $filePath;
    SmartFileHelper::createDirectoryRecursive($finalPath);
    \QRcode::png("" . $text, $finalPath, QR_ECLEVEL_H, 3);
    return $finalPath;
  }

  static function generateQrImage($text, $filePath)
  {
    // $finalPath =  SmartFileHelper::getDataPath() . $filePath;
    //SmartFileHelper::createDirectoryRecursive($finalPath);
    \QRcode::png("" . $text, $filePath, QR_ECLEVEL_H, 3);
    return $filePath;
  }
}
