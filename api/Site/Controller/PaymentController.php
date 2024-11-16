<?php

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;

use Site\Helpers\PaymentHelper;

use Core\Helpers\SmartData as Data;
use Core\Helpers\SmartData;
use Site\Helpers\InvoiceHelper;

class PaymentController extends BaseController
{

    private PaymentHelper $_helper;
    private InvoiceHelper $_invoice_helper;

    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new PaymentHelper($this->db);
        $this->_invoice_helper = new InvoiceHelper($this->db);
    }

    /**
     * 
     */
    public function insert()
    {
        $columns = ["sd_invoice_id", "sd_customer_id", "payment_amount", "payment_mode", "payment_date"];
        // do validations
        $this->_helper->validate(PaymentHelper::validations, $columns, $this->post);
        $columns[] = "created_by";
        $columns[] = "created_time";

        $this->post["sd_invoice_id"] = Data::post_select_value("sd_invoice_id");
        $this->post["sd_customer_id"] = Data::post_select_value("sd_customer_id");
        $this->db->_db->Begin();
        $id = $this->_helper->insert($columns, $this->post);
        $this->db->_db->commit();        //
        $this->response($id);
    }
    /**
     * 
     */

    /**
     * 
     */
    public function getAll()
    {
        $data = $this->_helper->getAllData();
        $this->response($data);
    }

    public function getAllReport()
    {
        $bill_id = SmartData::post_select_value("sd_bill_id");
        $sd_customer_id = SmartData::post_select_value("sd_customer_id");
        $sd_hub_id = SmartData::post_select_value("sd_hub_id");
        $sql_arr = [];
        $data_in = [];
        if ($bill_id > 0) {
            $sql_arr[] = "t1.sd_bill_id=:bill_id";
            $data_in["bill_id"] = $bill_id;
        }
        if ($sd_customer_id > 0) {
            $sql_arr[] = "t1.sd_customer_id=:sd_customer_id";
            $data_in["sd_customer_id"] = $sd_customer_id;
        }
        if ($sd_hub_id > 0) {
            $sql_arr[] = "t1.sd_hub_id=:sd_hub_id";
            $data_in["sd_hub_id"] = $sd_hub_id;
        }
        $sql = implode(" AND ", $sql_arr);
        $_invoice_data = $this->_invoice_helper->getAllReport($sql, $data_in);
        $_data =  new \stdClass();
        $_data->invoice_count = count($_invoice_data);
        $_data->invoice_amount = 0;
        $_data->paid_amount = 0;
        $_data->rem_amount = 0;
        // loop over invoice data 
        foreach ($_invoice_data as $obj) {
            $obj->rem = $obj->total_amount - floatval($obj->paid);
            $_data->invoice_amount += $obj->total_amount;
            $_data->paid_amount += floatval($obj->paid);
            $_data->rem_amount  +=  $obj->rem;
        }
        $_data->paid_amount = round($_data->paid_amount, 2);
        $_data->invoice_amount = round($_data->invoice_amount, 2);
        $_data->rem_amount = round($_data->rem_amount, 2);
        $out = new \stdClass();
        $out->_invoice_data = $_invoice_data;
        $out->_payment_data = $_data;
        //
        $this->response($out);
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
