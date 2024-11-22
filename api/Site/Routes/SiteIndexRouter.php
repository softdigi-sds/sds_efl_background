<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Routes;


class SiteIndexRouter{    

    private function get_all_routes(){
        // common routes
        $common_routes = CommonRouter::getRoutes();
        // all routes
        return array_merge($common_routes);
    }


    /**
     * 
     */
    static public function getRoutes(){
        $obj = new self();
        return $obj->get_all_routes();
    } 

}

