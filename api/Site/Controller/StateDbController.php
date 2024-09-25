<?php 

namespace Site\Controller;

use Core\BaseController;
use Site\Helpers\StateDbHelper as StateDbHelper;


class StateDbController extends BaseController{
    
    private StateDbHelper $_helper;

    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new StateDbHelper($this->db);
    }

   /**
     * 
     */
    public function insert(){
        $columns = [ "state_id", "state_name"];
        // do validations
        $this->_helper->validate(StateDbHelper::validations,$columns,$this->post);
         $this->db->_db->Begin();
        // insert and get id
        $id = $this->_helper->insert($columns,$this->post);
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
        $columns = [ "state_id", "state_name"];
        // do validations
        $this->_helper->validate(StateDbHelper::validations,$columns,$this->post);
        // insert and get id
        $this->_helper->update($columns,$this->post,$id);
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
        $out = new \stdClass();
        $out->msg = "Deleted Successfully";
        $this->response($out);
    }
    /**
     * 
     */
    public function getAllSelect(){      
        $select = ["state_id as value,state_name as label"];
        $data = $this->_helper->getAllData("",[],$select);
        $this->response($data);
    }


}