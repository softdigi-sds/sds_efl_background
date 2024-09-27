<?php 

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Site\Helpers\RoleHelper as RoleHelper;
use Site\Helpers\UserRoleHelper;


class RoleController extends BaseController{
    
    private RoleHelper $_helper;
      //
      private UserRoleHelper $_user_role_helper;

    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new RoleHelper($this->db);
          //
        $this->_user_role_helper = new UserRoleHelper($this->db);
    }

   /**
     * 
     */
    public function insert(){
        $columns = [ "role_name"];
        // do validations
        $this->_helper->validate(RoleHelper::validations,$columns,$this->post);
        // add other columns
        $columns[]="created_by"; 
        $columns[]="created_time"; 

         // Begin database transaction
         $this->db->_db->Begin();
            // insert and get id
            $id = $this->_helper->insert($columns,$this->post);
         // insert roles
         if(!($this->post["users"]) == NULL){
            $this->_user_role_helper->insertUsers($id, $this->post["users"]);
        }
        // Commit transaction
        $this->db->_db->commit();
        // // add log
        $this->addLog("ADDED A ROLE","",SmartAuthHelper::getLoggedInUserName());
        //
        $this->response($id);
    }
  
    /**
     * 
     */
    public function update(){
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if($id < 1){
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        $columns = ["role_name"];
        // do validations
        $this->_helper->validate(RoleHelper::validations,$columns,$this->post);
         // add other columns
         $columns[]="created_by"; 
        // insert and get id
        $this->_helper->update($columns,$this->post,$id);
        // update the roles
        if(!($this->post["users"]) == NULL){
            $this->_user_role_helper->insertUsers($id, $this->post["users"]);
        }
        else
        {
            // delete from user role table
            $this->_user_role_helper->deleteRoleUser($id);

        }       
        // add log
        $this->addLog("UPDATED A ROLE","",SmartAuthHelper::getLoggedInUserName());
        $this->response($id);
    }

    public function getAll(){      
        // insert and get id
        $data = $this->_helper->getAllData();
        foreach($data as $key=>$obj){
            $obj->users = $this->_user_role_helper->getSelectedUsersWithRoleId($obj->ID);
            $data[$key] = $obj;
        }
        $this->response($data);
    }
    /**
     * 
     */
    public function getOne(){  
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if($id < 1){
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }    
        // insert and get id
        $data = $this->_helper->getOneData($id);
        $this->response($data);
    }
    /**
     * 
     */
    public function deleteOne(){  
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if($id < 1){
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }    
        // delete from role table
        $this->_helper->deleteOneId($id);
        // delete from user_role table
        $this->_user_role_helper->deleteRoleUser($id);
        // add log
        $this->addLog("DELETED A ROLE","",SmartAuthHelper::getLoggedInUserName());
        //
        $out = new \stdClass();
        $out->msg = "Deleted Successfully";
        $this->response($out);
    }
    /**
     * 
     */
    public function getAllSelect(){      
        $select = ["ID as value,role_name as label "];
        $data = $this->_helper->getAllData("",[],$select);
        $roles =[ "value" => 10000000, "label" => "All Roles"];
        $users =[ "value" => 10000001, "label" => "All Users"];
        $data[] = $roles;
        $data[] = $users;
        $this->response($data);
    }

   


}