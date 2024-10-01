<?php 

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Site\Helpers\BillHelper;
use Site\Helpers\InvoiceHelper;



class BillController extends BaseController{
  
  private BillHelper $_helper;
  private InvoiceHelper $_invoice_helper;
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new BillHelper($this->db);
        //
        $this->_invoice_helper = new InvoiceHelper($this->db);
    }

   /**
     * 
     */
    public function insert(){
        $columns = [ "bill_start_date","bill_end_date" ];
        // do validations
        $this->_helper->validate(BillHelper::validations,$columns,$this->post);
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
        $columns = ["bill_start_date","bill_end_date" ];
        // do validations
        $this->_helper->validate(BillHelper::validations, $columns, $this->post);
        // extra columns
        $columns[] = "created_by";
        $columns[] = "created_time";
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
        if(isset($data->ID)){
            $data->invoice_data =  $this->_invoice_helper->getInvoiceByBillId($data->ID);
        }
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