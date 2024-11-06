<?php

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;

use Site\Helpers\PaymentHelper;

use Core\Helpers\SmartData as Data;




class PaymentController extends BaseController
{

    private PaymentHelper $_helper;
 
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new PaymentHelper($this->db);
   
    }

    /**
     * 
     */
    public function insert()
    {
        $columns = ["sd_invoice_id","sd_customer_id","payment_date","payment_amount"];
        // do validations
        $this->_helper->validate(PaymentHelper::validations, $columns, $this->post);
        $columns[] = "created_by";
        $columns[] = "created_time";
        // insert and get id
        $id = $this->_helper->insert($columns, $this->post);

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
        $columns = ["sd_invoice_id","sd_customer_id","payment_date","payment_amount"];
        // do validations
        $this->_helper->validate(PaymentHelper::validations, $columns, $this->post);
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
    public function getAll()
    {
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
        // insert and get id
        $this->_helper->deleteOneId($id);
        $out = new \stdClass();
        $out->msg = "Removed Successfully";
        $this->response($out);
    }

  



    
}
