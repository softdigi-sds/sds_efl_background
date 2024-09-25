<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Routes;

use Core\Helpers\SmartGeneral as General;
use Core\Helpers\SmartConst;

class CommonRouter
{

    private $_routes = [];
    private $_admin_only = ["ADMIN", "USER"];
    private $_user_only = ["USER"];
    private $_admin_user = ["ADMIN", "USER"];

    /**
     * 
     */
    private function auth_routes()
    {
        $controller = "AuthController";
        $this->_routes["/auth/login"] = [SmartConst::REQUEST_POST, $controller, "login"];
        $this->_routes["/auth/user_reset"] = [SmartConst::REQUEST_POST, $controller, "userReset"];
        $this->_routes["/auth/get_log"] = [SmartConst::REQUEST_POST, $controller, "getLog"];
        $this->_routes["/auth/get_settings"] = [SmartConst::REQUEST_GET, $controller, "getSiteSettings"];
        $this->_routes["/auth/do_backup"] = [SmartConst::REQUEST_GET, $controller, "takeBackup"];
    }


    private function users_routes()
    {
        $controller = "UserController";
        $this->_routes["/user/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/user/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/user/update_profile_img"] = [SmartConst::REQUEST_POST, $this->_admin_user, $controller, "updateUserProfilePic"];
        $this->_routes["/user/update_profile_details"] = [SmartConst::REQUEST_POST, $this->_admin_user, $controller, "updateUserProfileDetails"];
        $this->_routes["/user/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/user/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/user/get_one_user"] = [SmartConst::REQUEST_GET, $this->_admin_user, $controller, "getOneUser"];
        $this->_routes["/user/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/user/admin_reset"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "adminReset"];
        $this->_routes["/user/user_reset"] = [SmartConst::REQUEST_POST, $this->_admin_user, $controller, "userReset"];
        $this->_routes["/user/get_all_select"] = [SmartConst::REQUEST_GET, $this->_admin_user, $controller, "getAllSelect"];
        $this->_routes["/user/get_logged_users"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getRecentLoggedInUsers"];
        $this->_routes["/user/get_one_image"] = [SmartConst::REQUEST_POST, $controller, "getOneImage"];
    }

    private function role_routes()
    {
        $controller = "RoleController";
        $this->_routes["/role/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/role/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/role/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/role/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/role/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/role/get_all_select"] = [SmartConst::REQUEST_GET, $this->_admin_user, $controller, "getAllSelect"];
    }


           
    private function user_role_routes()
    {
        $controller = "UserRoleController";
        $this->_routes["/userrole/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/userrole/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/userrole/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/userrole/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/userrole/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
    }
    private function efl_office_routes()
    {
        $controller = "EflOfficeController";
        $this->_routes["/efloffice/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/efloffice/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/efloffice/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/efloffice/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/efloffice/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
    }
    private function hubs_routes()
    {
        $controller = "HubsController";
        $this->_routes["/hubs/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/hubs/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/hubs/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/hubs/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/hubs/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
    }
    private function hub_groups_routes()
    {
        $controller = "HubGroupsController";
        $this->_routes["/hubgroups/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/hubgroups/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/hubgroups/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/hubgroups/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/hubgroups/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
    }
    private function state_db_routes()
    {
        $controller = "StateDbController";
        $this->_routes["/state_db/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/state_db/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/state_db/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/state_db/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/state_db/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
        $this->_routes["/state_db/get_all_select"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAllSelect"];
    }
    private function vendors_routes()
    {
        $controller = "VendorsController";
        $this->_routes["/vendors/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/vendors/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/vendors/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/vendors/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/vendors/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
    }
    private function vendor_rate_routes()
    {
        $controller = "VendorRateController";
        $this->_routes["/vendor_rate/insert"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "insert"];
        $this->_routes["/vendor_rate/update"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "update"];
        $this->_routes["/vendor_rate/get_all"] = [SmartConst::REQUEST_GET, $this->_admin_only, $controller, "getAll"];
        $this->_routes["/vendor_rate/get_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "getOne"];
        $this->_routes["/vendor_rate/delete_one"] = [SmartConst::REQUEST_POST, $this->_admin_only, $controller, "deleteOne"];
    }
    

    
    private function get_all_Routes()
    {
        $this->auth_routes();
        $this->users_routes();
        $this->role_routes();
        $this->user_role_routes();
        $this->efl_office_routes();
        $this->hubs_routes();
        $this->hub_groups_routes();
        $this->state_db_routes();
        $this->vendors_routes();
        $this->vendor_rate_routes();
        return $this->_routes;
    }


    /**
     * 
     */
    static public function getRoutes()
    {
        $obj = new self();
        return $obj->get_all_routes();
    }
}
