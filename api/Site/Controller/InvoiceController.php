<?php 

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Site\Helpers\InvoiceHelper;



class InvoiceController extends BaseController{
  
  private InvoiceHelper $_helper;
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new InvoiceHelper($this->db);
    }

   /**
     * 
     */
    public function insert(){
        $columns = [  "sd_bill_id", "sd_hub_id", "sd_vendors_id", "total_units","total_vehicals", "unit_amount", "vechical_amount", "gst_percentage", "gst_amount", "total_amount"];
        // do validations
        $this->_helper->validate(InvoiceHelper::validations,$columns,$this->post);
        $columns[] = "status";
        $this->post["status"] = 5;
        // insert and get id
         $id = $this->_helper->insert($columns,$this->post);
       
        //
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
        $columns = ["sd_bill_id", "sd_hub_id", "sd_vendors_id", "total_units","total_vehicals", "unit_amount", "vechical_amount", "gst_percentage", "gst_amount", "total_amount"];
        // do validations
        $this->_helper->validate(InvoiceHelper::validations, $columns, $this->post);
        // extra columns
        $columns[] = "status";
        $this->post["status"] = 5;
        // begin transition
        $this->db->_db->Begin();
          // insert and get id
        $id = $this->_helper->update($columns, $this->post, $id);
        $this->db->_db->commit();
        $this->response($id);
    }
 /**
     * 
     */
    public function getAll(){      
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
        $out->msg = "Removed Successfully";
        $this->response($out);

    }    
     /**
     * 
     */


}