<?php 

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartData as Data;
use Site\Helpers\EflConsumptionHelper;



class EflConsumptionController extends BaseController{
  
  private EflConsumptionHelper $_helper;
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new EflConsumptionHelper($this->db);
    }

   /**
     * 
     */
    public function insert(){
        $columns = ["sd_hub_id","sd_vendors_id" ,"sd_date" ,"unit_count"];
        // do validations
        $this->_helper->validate(EflConsumptionHelper::validations,$columns,$this->post);
        $columns[] = "created_by" ;
        $columns[] = "created_time" ;
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
        $columns = ["sd_hub_id","sd_vendors_id" ,"sd_date" ,"unit_count"];
        // do validations
        $this->_helper->validate(EflConsumptionHelper::validations, $columns, $this->post);
        // extra columns
        $columns[] = "last_modified_by";
        $columns[] = "last_modified_time";
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
    public function getOneConsumptionData(){
        $id = isset($this->post["hub_id"]) ? ($this->post["hub_id"]) : 0;
        // $month = isset($this->post["month"]) ? intval($this->post["month"]) : "";
        // $year = isset($this->post["year"]) ? intval($this->post["year"]) : "";
        $date = isset($this->post["date"]) ? trim($this->post["date"]) : "";
        if($id < 1){
            \CustomErrorHandler::triggerInvalid("Invalid Hub ID");
        }    
        // if($month < 0 || $year < 0 ){
        //     \CustomErrorHandler::triggerInvalid("Invalid month or date ");
        // } 
           if(strlen($date) < 0){
            \CustomErrorHandler::triggerInvalid("Invalid date ");
        } 
        $hub_id = Data::post_select_value($id );
        // $data = $this->_helper->getVendorsByHubId($hub_id,$month, $year);
        $data = $this->_helper->getVendorsByHubId($hub_id,$date);
        $this->response($data);

    }
      /**
     * 
     */
    public function getAllConsumptionData(){
        $id = isset($this->post["hub_id"]) ? ($this->post["hub_id"]) : 0;
        $month = isset($this->post["month"]) ? intval($this->post["month"]) : "";
        $year = isset($this->post["year"]) ? intval($this->post["year"]) : "";
        if($id < 1){
            \CustomErrorHandler::triggerInvalid("Invalid Hub ID");
        }    
        if($month < 0 || $year < 0 ){
            \CustomErrorHandler::triggerInvalid("Invalid month or date ");
        } 
        $hub_id = Data::post_select_value($id );
        $data = $this->_helper->getCountByHubAndDate($hub_id,$month, $year);
        $this->response($data);

    }
  

 
}