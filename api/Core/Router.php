<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of router
 *
 * @author kms
 */

namespace Core;

use Site\Routes\SiteIndexRouter;

class Router
{

    private static $_instance = null;
    //
    private $_routes = [];
    //
    private  $_currentUrl;

    public static function get_instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    static public function getRoute()
    {
        $obj = self::get_instance();
        //
        return $obj->get_route_parameters();
    }

    private function get_route_parameters()
    {
        // Get the current URL
        $this->_currentUrl = $_SERVER['REQUEST_URI'];
        //
        $this->_currentUrl = str_replace("/api","",$this->_currentUrl);
        // get routes
        $this->get_routes();
        // process and return routes
        return $this->process_route();
    }

    private function get_routes()
    {
        $this->_routes = SiteIndexRouter::getRoutes();
        //
       // var_dump($this->_routes);
    }

    private function pattern_to_regular_expression($pattern_input){
        // Convert the pattern into a regular expression
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<\1>[^/]+)', $pattern_input);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';
        return $pattern;
    }

    private function process_route()
    {
        // Loop through the routes to find a match
        foreach ($this->_routes as $pattern => $controllerArr) {
            // Convert the pattern into a regular expression
            $pattern = $this->pattern_to_regular_expression($pattern);
            //
          //  echo "pattern = "  . $pattern . "<br/>";
          //  echo "url " . $this->_currentUrl . "<br/>";
            // Check if the URL matches the pattern
            if (preg_match($pattern, $this->_currentUrl, $matches)) {
                // Extract named parameters from the match
                $parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                //
                return [$controllerArr,$parameters];
                // 
                break;
            }
        }
    }
}
