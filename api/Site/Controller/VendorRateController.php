<?php 

namespace Site\Controller;

use Core\Helpers\SmartData as Data;
use Core\BaseController;
use Site\Helpers\VendorRateHelper as VendorRateHelper;


class VendorRateController extends BaseController{
    
    private VendorRateHelper $_helper;

    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new VendorRateHelper($this->db);
    }

   /**
     * 
     */
    public function insert(){
        $columns = [ "sd_hubs_id","sd_vendors_id","unit_rate_type","parking_rate_type","effective_date"];
        // do validations
        $this->_helper->validate(VendorRateHelper::validations,$columns,$this->post);
       // columns
        $columns[] = "min_units";
        $columns[] = "unit_rate";
        $columns[] = "extra_unit_rate";
        $columns[] = "parking_min_count";
        $columns[] = "parking_rate_vehicle";
        $columns[] = "parking_extra_rate_vehicle";
        $columns[] = "created_time";
        $columns[] = "created_by";
        // data
        $this->post["sd_hubs_id"] = Data::post_select_value($this->post["sd_hubs_id"]);
        $this->post["sd_vendors_id"] = Data::post_select_value($this->post["sd_vendors_id"]);
        $data = $this->_helper->checkEffectiveDateClash($this->post["effective_date"]);
        // var_dump($data);exit();
        if (!empty($data)) {
            \CustomErrorHandler::triggerInvalid("There is already an Effective date available upto ". $data->effective_date);
        }
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
        $columns = ["unit_rate_type","parking_rate_type","effective_date"];
        // do validations
        $this->_helper->validate(VendorRateHelper::validations,$columns,$this->post);
        // insert and get id
        $columns[] = "min_units";
        $columns[] = "unit_rate";
        $columns[] = "extra_unit_rate";
        $columns[] = "parking_min_count";
        $columns[] = "parking_rate_vehicle";
        $columns[] = "parking_extra_rate_vehicle";
        $columns[] = "last_modified_time";
        $columns[] = "last_modified_by";
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
}