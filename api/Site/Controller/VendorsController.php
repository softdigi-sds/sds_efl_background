<?php 

namespace Site\Controller;

use Core\BaseController;
use Site\Helpers\VendorsHelper as VendorsHelper;
use Core\Helpers\SmartData as Data;
use Site\Helpers\InvoiceHelper as InvoiceHelper;


class VendorsController extends BaseController{
    
    private VendorsHelper $_helper;
    private InvoiceHelper $_invoice_helper;

    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new VendorsHelper($this->db);
        $this->_invoice_helper = new InvoiceHelper($this->db);
    }

   /**
     * 
     */
    public function insert(){
        $validate_columns = ["sd_hub_id","vendor_code","vendor_company","gst_no","pin_code"];
        // do validations
        $this->_helper->validate(VendorsHelper::validations,$validate_columns,$this->post);
        $columns = ["sd_hub_id","vendor_code","vendor_company","vendor_name","gst_no","pan_no","address_one","address_two","state_name","pin_code","status","created_by","created_time"];
        $this->post["status"] = 5;
        // $this->post["sd_hub_id"] = Data::post_select_value($this->post["sd_hub_id"]);
        // $this->post["state_name"] = Data::post_select_value($this->post["state_name"]);
        $hub_id = $this->post["sd_hub_id"];
        $this->post["sd_hub_id"]  = $hub_id["value"];
         $state_name = $this->post["state_name"];
        $this->post["state_name"]  = $state_name["value"];
        $data = $this->_helper->checkVendorByCodeCompany($this->post["vendor_code"], $this->post["vendor_company"]);
        if (!empty($data)) {
            \CustomErrorHandler::triggerInvalid("Vendor code and company already available ");
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
        $columns = ["gst_no","pin_code","status"];
        // do validations
        $this->_helper->validate(VendorsHelper::validations,$columns,$this->post);
        // insert and get id
        $columns[] = "last_modified_by";
        $columns[] =  "last_modified_time";
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
        $data = $this->_invoice_helper->checkInvoiceExist($id);
        if (!empty($data)) {
            \CustomErrorHandler::triggerInvalid("Cannot remove Vendor, invoice is registered for for this vendor");
        }  
        // insert and get id
        $this->_helper->deleteOneId($id);
        $out = new \stdClass();
        $out->msg = "Deleted Successfully";
        $this->response($out);
    }

    public function getAllSelectCompany(){  
        $id = isset($this->post["hub_id"]) ? intval($this->post["hub_id"]) : 0;
    if($id < 1){
            \CustomErrorHandler::triggerInvalid("Invalid Hub ID");
        }    
        // insert and get id
        $sql = "t1.sd_hub_id=:hub_id";
        $data_in = ["hub_id" => $id];
        $select = ["t1.ID AS value, t1.vendor_company AS  label"];
        $data = $this->_helper->getAllData($sql , $data_in, $select);
        $this->response($data);
    }
}