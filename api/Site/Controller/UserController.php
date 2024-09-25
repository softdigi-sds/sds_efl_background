<?php

namespace Site\Controller;

use Core\BaseController;
use Core\Helpers\SmartGeneral;
use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartData as Data;
use Site\Helpers\UserHelper;
use Site\Helpers\UserRoleHelper;

class UserController extends BaseController
{
    //
    private UserHelper $_user_helper;
    private UserRoleHelper $_user_role_helper;

    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_user_helper = new UserHelper($this->db);
        //
        $this->_user_role_helper = new UserRoleHelper($this->db);
        //
        $this->_user_role_helper = new UserRoleHelper($this->db);

    }

    /**
     * 
     */
    public function insert()
    {
        $columns = ["ename", "euserid","mobile_no","profile_img","active_status"];
        // do validations
        $this->_user_helper->validate(UserHelper::validations, $columns, $this->post);
        //
        // here check user id already exists or not 
        $exists_data = $this->_user_helper->getOneDataWithUserId($this->post["euserid"]);
        if (isset($exists_data->ID)) {
            \CustomErrorHandler::triggerInvalid("ICNO Already Existed");
        }
        $this->db->_db->Begin();
        // add other columns 
        $columns[] = "emailid";
        $columns[] = "designation";
        $columns[] = "created_time";
        $columns[] = "change_pass";
        $columns[] = "epassword";
        $this->post["change_pass"] = 1;
        $this->post["epassword"] = SmartGeneral::hashPassword($this->post["euserid"]);
        // insert and get id
        $id = $this->_user_helper->insert($columns, $this->post);
        //
        $this->db->_db->commit();
        // add log
        $this->addLog("INSERTED A NEW USER","",SmartAuthHelper::getLoggedInUserName());
        // 
        // response 
        $this->response($id);
    }
    /**
     * 
     */
    public function update()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        $columns = ["ename", "mobile_no","profile_img",   "active_status"];
        // do validations
        $this->_user_helper->validate(UserHelper::validations, $columns, $this->post);
        // extra columns
        $columns[] = "emailid";
        $columns[] = "designation";
        // begin transition
        $this->db->_db->Begin();
          // insert and get id
        $id = $this->_user_helper->update($columns, $this->post, $id);
        // insert the roles selected in userdb roles stable
        // if(!($this->post["role"]) == NULL)
        // {
        //     $this->_user_role_helper->insertRoles($id, $this->post["role"]);
        // }     
       //
        $this->db->_db->commit();
        // add log
        $this->addLog("UPDATED A USER DETAIL","",SmartAuthHelper::getLoggedInUserName());
        // 
        $this->response($id);
    }
    /**
     * 
     */
    public function updateUserProfilePic(){
        $id = SmartAuthHelper::getLoggedInId();
        $columns = ["profile_img"];
        $this->_user_helper->validate(UserHelper::validations,$columns, $this->post);
        // begin transition
        $this->db->_db->Begin();
        // insert and get id
      $id = $this->_user_helper->update($columns, $this->post, $id);
      //
      $this->db->_db->commit();
      // add log
      $this->addLog("UPDATED PROFILE PICTURE","",SmartAuthHelper::getLoggedInUserName());
       // 
       $this->response("Profile picture updated successfully");


    }
  /**
     * 
     */
    public function updateUserProfileDetails(){
        $id = SmartAuthHelper::getLoggedInId();
        $columns = ["designation","mobile_no"];
        $this->_user_helper->validate(UserHelper::validations,$columns, $this->post);
        // add extra columns
        $columns[] = "emailid";
        $columns[] = "profile_img";
        // begin transition
        $this->db->_db->Begin();
        // insert and get id
      $id = $this->_user_helper->update($columns, $this->post, $id);
      //
      $this->db->_db->commit();
      // add log
      $this->addLog("UPDATED PROFILE DETAILS","",SmartAuthHelper::getLoggedInUserName());
       // 
       $this->response("Profile Details Updated Successfully");


    }

    public function getAll()
    {
        // insert and get id
        $data = $this->_user_helper->getAllData();
        $this->response($data);
    }
    /**
     * 
     */
    public function getOne()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        // insert and get id
        $data = $this->_user_helper->getOneData($id);
        $this->response($data);
    }
    /**
     * 
     */
    public function deleteOne()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        // to delete the user references from user_role table,
        // when user is getting deleted
        $this->_user_role_helper->deleteUserRole($id);

        // delete and get id
        $this->_user_helper->deleteOneId($id);
        // add log
        $this->addLog("DELETED A USER","",SmartAuthHelper::getLoggedInUserName());
        //
        $out = new \stdClass();
        $out->msg = "Deleted Successfully";
        $this->response($out);
    }

    /**
     * admin reset password
     */
    public function adminReset()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        // insert and get id
        $data = $this->_user_helper->getOneData($id);
        //
        if(!isset($data->ID)){
            \CustomErrorHandler::triggerInternalError("ID does not exists");
        }
        //echo "userid " . $data->euserid. "<br/>";
        //
        $post_data = ["epassword"=> SmartGeneral::hashPassword($data->euserid),"change_pass"=>0, "failed_attempts"=>0];
        //
       // var_dump($post_data);
        //
        $columns = ["epassword","change_pass","failed_attempts"];
        //
        $this->_user_helper->update($columns, $post_data, $id);
        // add log
        $this->addLog("PASSWORD RESETTED","",SmartAuthHelper::getLoggedInUserName()); 
        // retrun success mag
        $this->responseMsg("Reset Successfully");

    }


    public function userReset()
    {
        $id = SmartAuthHelper::getLoggedInId();
        //
        $columns = ["currentPassword","newPassword","confirmPassword"];
        // validate user data
        $this->_user_helper->validate(UserHelper::validations,$columns,$this->post);
        // 
        $newPassword =  Data::post_data("newPassword","STRING");
        $confirmPassword =  Data::post_data("confirmPassword","STRING");
        if($confirmPassword!==$newPassword){
            \CustomErrorHandler::triggerInternalError("New & Confirm Passwords should be same");
        }
        //
        $user_data = $this->_user_helper->getOneData($id);
        //
        $currentPassword = $this->post["currentPassword"];
        //
        //
        if(!password_verify($currentPassword,$user_data->epassword)){
            \CustomErrorHandler::triggerInternalError("Invalid Current Password");
        }
        //
        $post_data = ["epassword"=> SmartGeneral::hashPassword($this->post["newPassword"])];
        $updated_column =["epassword"];
        //
        $this->_user_helper->update($updated_column, $post_data, $id);
       // add log
       $this->addLog("USER RESETTED HIS PASSWORD","",SmartAuthHelper::getLoggedInUserName()); 
        // return success mag
        $this->responseMsg("Reset Successfully");

    }

    public function getOneUser()
    {
        $id = SmartAuthHelper::getLoggedInId();
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        // insert and get id
        $data = $this->_user_helper->getOneData($id);
        $this->response($data);
    }

    /**
     * 
     */
    public function getAllSelect(){      
        // insert and get id
        $select = ["t1.ID as value,t1.ename as label"];
        $data = $this->_user_helper->getAllData("",[],$select);
        $obj =[ "value" => 10000000, "label" => "All Users"];
        $data[] = $obj;
        $this->response($data);
    }

    public function getRecentLoggedInUsers(){   
        $data =  $this->_user_helper->getRecentUsers();
        $this->response($data);
    }

    public function getOneImage()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        // insert and get id
        $data = $this->_user_helper->getOneData($id);
        $out = new \stdClass();
        $out->img = $data->profile_img;
        $this->response($out);
    }
    
}
