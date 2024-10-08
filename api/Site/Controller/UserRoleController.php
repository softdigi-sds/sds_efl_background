<?php 

namespace Site\Controller;

use Core\BaseController;

use Site\Helpers\UserRoleHelper;


class UserRoleController extends BaseController{
    private UserRoleHelper $_helper;

     function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new UserRoleHelper($this->db);
    }

   /**
     * 
     */
    public function insert(){
        $columns = ["sd_mt_userdb_id","sd_mt_role_id","created_by"];
        // do validations
        $this->_helper->validate(UserRoleHelper::validations,$columns,$this->post);
        // add other columns
        $columns[]="created_time"; 
        
         // Begin database transaction
         $this->db->_db->Begin();
        // insert and get id
        $id = $this->_helper->insert($columns,$this->post);
         // Commit transaction
         $this->db->_db->commit();
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
        $columns = ["created_by"];
        // do validations
        $this->_helper->validate(UserRoleHelper::validations,$columns,$this->post);
        // insert and get id
        $id = $this->_helper->update($columns,$this->post,$id);
        $this->response($id);
    }

    public function getAll(){      
        // insert and get id
        $data = $this->_helper->getAllData();
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
        // insert and get id
        $this->_helper->deleteOneId($id);
        //
        $out = new \stdClass();
        $out->msg = "Deleted Successfully";
        $this->response($out);
    }

   


}