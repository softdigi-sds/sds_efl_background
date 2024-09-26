<?php

namespace Site\Controller;

use Core\BaseController;
use Core\Helpers\SmartGeneral;
use Core\Helpers\SmartData as Data;
use Core\Helpers\SmartLogger as Logger;
use Site\Helpers\BackupHelper;
// site helpers
use Site\Helpers\UserHelper;
use Site\Helpers\UserRoleHelper;
use Site\Helpers\SiteHelper;


class AuthController extends BaseController
{

    private UserHelper $_user_helper;
    private UserRoleHelper $_user_role_helper;
    private SiteHelper $_site_helper;

    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_user_helper = new UserHelper($this->db);
        //
        $this->_user_role_helper = new UserRoleHelper($this->db);
        //
        $this->_site_helper = new SiteHelper($this->db);
    }

    private function getRoles($id, $initial)
    {
        $roles = $this->_user_role_helper->getSelectedRolesWithUserId($id);
        $role_names = $initial;
        $system_admin = $this->_site_helper->getOneValue("system_admin");

        foreach ($roles as $role) {
            $role_names[] = $role->label;
            if ($role->value == intval($system_admin)) {
                $role_names[] = "ADMIN";
            }
        }
        return $role_names;
    }


    private function get_response($user_data)
    {
        $payload = array(
            "USER" => $user_data
        );
        // jwt     
        $jwt = SmartGeneral::jwt_encode($payload);
        //    
        $db = new \stdClass();
        $db->accessToken = $jwt;
        $db->ename = $user_data->ename;
        $db->euserid = $user_data->euserid;
        $db->change_pass = $user_data->change_pass;
        $db->expiresInTime = 700;
        $db->id = $user_data->ID;
        $db->roles = $user_data->role;
        $roles = ["USER"];
        if ($user_data->euserid == "admin") {
            $roles[] = "ADMIN";
        }
        $roles = $this->getRoles($user_data->ID, $roles);
        $db->role =  $roles;
        return $db;
    }

    private function updateVisitorCount()
    {
        $key = "SITE_VISITOR_COUNT";
        $exists_data = $this->_site_helper->getOneSettingData($key);
        $data = [];
        if (!$exists_data) {
            $columns = ["setting_name", "setting_value", "created_by"];
            $data["setting_name"] = $key;
            $data["setting_value"] = 1;
            $id = $this->_site_helper->insert($columns, $data);
        } else {
            $id = $exists_data->ID;
            $columns =  ["setting_value", "last_modified_time"];
            $data["setting_value"] = intval($exists_data->setting_value) + 1;
            $this->_site_helper->update($columns, $data, $id);
        }
    }
    /**
     * 
     */
    public function login()
    {
        $columns = ["euserid", "epassword"];
        // do validations
        $this->_user_helper->validate(UserHelper::validations, $columns, $this->post);
        // take the data
        $userid = Data::post_data("euserid", "STRING");
        // get the data
        $user_data = $this->_user_helper->getOneDataWithUserId($userid);
        // 
        if (!isset($user_data->ID)) {
            \CustomErrorHandler::triggerInvalid("Invalid ICNO");
        }
        // get status
        $status = $user_data->active_status;
        // check failed password attempts
        if ($userid != "admin") {
            $this->_user_helper->checkFailedAttempts($user_data);
        }
        //
        $password = trim($this->post["epassword"]);
        //         
        if (!password_verify($password, $user_data->epassword)) {
            $this->addLog("INVALID PASSWORD", "", $user_data->ename);
            $this->_user_helper->updateFailedAttempts($user_data);
            \CustomErrorHandler::triggerInvalid("Invalid Password");
        }
        // check status active or inactive
        if ($status != 5) {
            $this->addLog("USER LOGGED IN BUT IN ACTIVE", "", $user_data->ename);
            \CustomErrorHandler::triggerInvalid("Status inactive");
        }
        // update the last login time 
        $this->_user_helper->updateLastLogin($user_data->ID);
        // user data
        $user_data->role = $userid != "admin" ? ["USER"] : ["ADMIN"];
        // updating the visitor count
        $this->updateVisitorCount();
        //
        $this->addLog("LOGIN", "", $user_data->ename);
        //
        $user_data->profile_img = "";
        // update the visitor count
        $this->_site_helper->updateSiteCount();
        // pay load             
        $this->response($this->get_response($user_data));
    }

    public function userReset()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        // get the data
        $user_data = $this->_user_helper->getOneData($id);
        if (!isset($user_data->ID)) {
            \CustomErrorHandler::triggerInvalid("Invalid ICNO");
        }
        //         
        $post_data = ["epassword" => SmartGeneral::hashPassword($user_data->euserid)];
        $update_columns = ["epassword", "last_reset_time"];
        //
        $this->_user_helper->update($update_columns, $post_data, $user_data->ID);
        //
        $this->addLog("PASS_RESET", "", "Admin");
        // 
        $user_data->profile_img = "";
        $this->response("user password resetted successfully!!!");
    }
    /**
     * 
     */
    public function getLog()
    {
        $year = isset($this->post["year"]) ? intval($this->post["year"]) : SmartGeneral::getYear();
        $month = isset($this->post["month"]) ? intval($this->post["month"]) : SmartGeneral::getMonth();
        $data = Logger::readLogFile($year, $month);
        $this->response($data);
    }

    public function getSiteSettings()
    {
        $settings = isset($GLOBALS["SD_SITE_SETTINGS"]) ? $GLOBALS["SD_SITE_SETTINGS"] : [];
        $this->response($settings);
    }

    public function takeBackup()
    {
        $backup = new BackupHelper($this->db);
        $backup_file = "test.sql";
        $backup->doBackUp($backup_file);
    }
}
