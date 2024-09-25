<?php 

namespace Core;

class AutoLoader {
    public static function register() {
        spl_autoload_register(function($class) {
            $classPath =  str_replace('\\', '/', $class) . '.php';
          //  echo "class name ". $classPath ."<br/>";
            if (file_exists($classPath)) {
                include($classPath);
            }else{
                
            }
        });
    }
}

AutoLoader::register();