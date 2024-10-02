<?php

namespace Core;

class AutoLoader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $classPath =  str_replace('\\', '/', $class) . '.php';
            //  echo "class name ". $classPath ."<br/>";
            if (strpos($classPath, 'PhpOffice') !== false || strpos($classPath, 'mPDF') !== false) {
                $baseDir = 'vendor/PhpSpreadsheet/';
                // For PhpSpreadsheet classes, prepend the base directory
                $file = $baseDir . str_replace('PhpOffice/PhpSpreadsheet/', '', $classPath);
                if (file_exists($file)) {
                    require $file;
                }
            } else if (strpos($classPath, 'SimpleCache') !== false) {
                $baseDir = 'vendor/Psr/SimpleCache/';
                // For PhpSpreadsheet classes, prepend the base directory
                $file = $baseDir . str_replace('Psr/SimpleCache/', '', $classPath);
                if (file_exists($file)) {
                    require $file;
                }
            } else if (strpos($classPath, 'ZipStream') !== false) {
                $baseDir = 'vendor/ZipStream/';
                // For PhpSpreadsheet classes, prepend the base directory
                $file = $baseDir . str_replace('ZipStream/', '', $classPath);
                if (file_exists($file)) {
                    require $file;
                }
            } else if (file_exists($classPath)) {
                include($classPath);
            } else {
            }
        });
    }
}

AutoLoader::register();
