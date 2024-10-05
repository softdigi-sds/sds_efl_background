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
    private UserHelper $_helper;
    private UserRoleHelper $_user_role_helper;

    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new UserHelper($this->db);
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
        
        $columns = ["ename", "mobile_no","epassword"];
        // do validations
        $this->_helper->validate(UserHelper::validations, $columns, $this->post);
        //
        // here check user id already exists or not 
        $exists_data = $this->_helper->getOneDataWithUserId($this->post["euserid"]);
        if (isset($exists_data->ID)) {
            \CustomErrorHandler::triggerInvalid("ICNO Already Existed");
        }
        $this->db->_db->Begin();
        // add other columns 
        $columns[] = "active_status";
        $columns[] = "profile_img";
        $columns[] = "emailid";
        $columns[] = "designation";
        $columns[] = "created_time";
        $columns[] = "change_pass";
        $columns[] = "epassword";
        $columns[] = "euserid";
        $this->post["euserid"] = "none";
        $this->post["active_status"] = 5;
        $this->post["change_pass"] = 1;
        $this->post["epassword"] = SmartGeneral::hashPassword($this->post["epassword"]);
        // insert and get id
        $id = $this->_helper->insert($columns, $this->post);
        // insert roles
        if(!($this->post["role"]) == NULL){
            $this->_user_role_helper->insertRoles($id, $this->post["role"]);
            }
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
        $columns = ["ename", "mobile_no", "active_status" ];
        // do validations
        $this->_helper->validate(UserHelper::validations, $columns, $this->post);
        // extra columns
        $columns[] = "emailid";
        $columns[] = "designation";
        // begin transition
        $this->db->_db->Begin();
          // insert and get id
        $id = $this->_helper->update($columns, $this->post, $id);
        // insert the roles selected in userdb roles stable
        if(!($this->post["role"]) == NULL)
        {
            $this->_user_role_helper->insertRoles($id, $this->post["role"]);
        }   else
        {
            // delete from user role table
            $this->_user_role_helper->deleteUserRole($id);

        }      
       
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
        $this->_helper->validate(UserHelper::validations,$columns, $this->post);
        // begin transition
        $this->db->_db->Begin();
        // insert and get id
      $id = $this->_helper->update($columns, $this->post, $id);
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
        $this->_helper->validate(UserHelper::validations,$columns, $this->post);
        // add extra columns
        $columns[] = "emailid";
        $columns[] = "profile_img";
        // begin transition
        $this->db->_db->Begin();
        // insert and get id
      $id = $this->_helper->update($columns, $this->post, $id);
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
        $data = $this->_helper->getAllData();
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
        $data = $this->_helper->getOneData($id);
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
        $this->_helper->deleteOneId($id);
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
//     public function adminReset()
//     {
//         $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
//         if ($id < 1) {
//             \CustomErrorHandler::triggerInvalid("Invalid ID");
//         }
//         // insert and get id
//         $data = $this->_helper->getOneData($id);
//         //
//         if(!isset($data->ID)){
//             \CustomErrorHandler::triggerInternalError("ID does not exists");
//         }
//         //echo "userid " . $data->euserid. "<br/>";
//         //
//         $post_data = ["epassword"=> SmartGeneral::hashPassword($data->euserid),"change_pass"=>0, "failed_attempts"=>0];
//         //
//        // var_dump($post_data);
//         //
//         $columns = ["epassword","change_pass","failed_attempts"];
//         //
//         $this->_helper->update($columns, $post_data, $id);
//         // add log
//         $this->addLog("PASSWORD RESETTED","",SmartAuthHelper::getLoggedInUserName()); 
//         // retrun success mag
//         $this->responseMsg("Reset Successfully");

//    }
    public function adminReset()
{
    $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
    if ($id < 1) {
        \CustomErrorHandler::triggerInvalid("Invalid ID");
    }
    $new_pass = isset($this->post['new_pass']) ? $this->post['new_pass'] : null;
    $confirm_pass = isset($this->post['confirm_pass']) ? $this->post['confirm_pass'] : null;

    if (!$new_pass || !$confirm_pass) {
        \CustomErrorHandler::triggerInvalid("Both new password and confirm password are required");
    }

    if ($new_pass !== $confirm_pass) {
        \CustomErrorHandler::triggerInvalid("Passwords do not match");
    }

    $data = $this->_helper->getOneData($id);

    if (!isset($data->ID)) {
        \CustomErrorHandler::triggerInternalError("ID does not exist");
    }
    $hashedPassword = SmartGeneral::hashPassword($new_pass);
    $post_data = [
        "epassword" => $hashedPassword,
        "change_pass" => 0,
        "failed_attempts" => 0
    ];
    $columns = ["epassword", "change_pass", "failed_attempts"];

    $this->_helper->update($columns, $post_data, $id);
    $this->addLog("PASSWORD RESET", "", SmartAuthHelper::getLoggedInUserName());
    $this->responseMsg("Password reset successfully");
}



    public function userReset()
    {
        $id = SmartAuthHelper::getLoggedInId();
        //
        $columns = ["currentPassword","newPassword","confirmPassword"];
        // validate user data
        $this->_helper->validate(UserHelper::validations,$columns,$this->post);
        // 
        $newPassword =  Data::post_data("newPassword","STRING");
        $confirmPassword =  Data::post_data("confirmPassword","STRING");
        if($confirmPassword!==$newPassword){
            \CustomErrorHandler::triggerInternalError("New & Confirm Passwords should be same");
        }
        //
        $user_data = $this->_helper->getOneData($id);
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
        $this->_helper->update($updated_column, $post_data, $id);
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
        $data = $this->_helper->getOneData($id);
        $this->response($data);
    }

    /**
     * 
     */
    public function getAllSelect(){      
        // insert and get id
        $select = ["t1.ID as value,t1.ename as label"];
        $data = $this->_helper->getAllData("",[],$select);
        $obj =[ "value" => 10000000, "label" => "All Users"];
        $data[] = $obj;
        $this->response($data);
    }

    public function getRecentLoggedInUsers(){   
        $data =  $this->_helper->getRecentUsers();
        $this->response($data);
    }

    public function getOneImage()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        // insert and get id
        $data = $this->_helper->getOneData($id);
        $out = new \stdClass();
        $out->img = $data->profile_img;
        $this->response($out);
    }
    
}
