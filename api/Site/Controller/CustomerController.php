<?php

namespace Site\Controller;

use Core\BaseController;
use Core\Helpers\SmartData;
use Site\Helpers\CustomerHelper;
use Core\Helpers\SmartData as Data;
use Site\Helpers\InvoiceHelper as InvoiceHelper;
use Site\Helpers\HubsHelper;
use Site\Helpers\StateDbHelper;
use Core\Helpers\SmartFileHelper;
use Core\Helpers\SmartExcellHelper;
use Site\Helpers\ImportHelper;
use Site\Helpers\TableHelper as Table;
use Site\Helpers\CustomerSubHelper;

class CustomerController extends BaseController
{

    private CustomerHelper $_helper;
    private CustomerSubHelper $_sub_helper;
    private InvoiceHelper $_invoice_helper;
    private HubsHelper $_hubs_helper;
    private StateDbHelper $_state_helper;
    private ImportHelper $_import_helper;


    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new CustomerHelper($this->db);
        //
        $this->_sub_helper = new CustomerSubHelper($this->db);
    }

    /**
     * 
     */
    public function insert()
    {
        $validate_columns = ["vendor_company",  "vendor_name", "pan_no"];
        // do validations
        $this->_helper->validate(CustomerHelper::validations, $validate_columns, $this->post);
        $columns = ["vendor_company", "vendor_name",  "pan_no", "single_bill","single_bill_hsn",
        "single_bill_title", "status", "created_by", "created_time"];
        $this->post["status"] = 5;
        $data = $this->_helper->checkVendorExists($this->post["vendor_company"]);
        if (isset($data->ID)) {
            \CustomErrorHandler::triggerInvalid("Customer Already Exists For Hub");
        }
        $this->post["single_bill"] = SmartData::post_select_value("single_bill");
        $this->db->_db->Begin();
        // insert and get id
        $id = $this->_helper->insert($columns, $this->post);
        $sub_data = Data::post_array_data("sub_data"); 
        if( $this->post["single_bill"]!=1){
            $sub_data =[];
        }       // 
        $this->_sub_helper->insert_update_data($id, $sub_data);
        $this->db->_db->commit();
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
        $columns = ["vendor_company", "vendor_name",  "pan_no"];
        // do validations
        $this->_helper->validate(CustomerHelper::validations, $columns, $this->post);
        // insert and get id
        $columns[] = "single_bill";
        $columns[] = "single_bill_hsn";
        $columns[] = "single_bill_title";
        $columns[] = "last_modified_by";
        $columns[] =  "last_modified_time";
        $this->post["single_bill"] = SmartData::post_select_value("single_bill");
        $this->db->_db->Begin();
        $this->_helper->update($columns, $this->post, $id);
        $sub_data = Data::post_array_data("sub_data");     
        if( $this->post["single_bill"]!=1){
            $sub_data =[];
        }    // 
        $this->_sub_helper->insert_update_data($id, $sub_data);
        $this->db->_db->commit();
        $this->response($id);
    }

    public function updateStatus()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        $status = SmartData::post_data("status", "INTEGER");
        $columns = ["status"];
        $data_in = ["status" => $status];
        $this->_helper->update($columns,  $data_in, $id);
        $this->responseMsg("Status Updated Successfully");
    }

    public function getAll()
    {
        // Fetch data using the helper function
        $data = $this->_helper->getAllData();
        // Send response
        $this->response($data);
    }

    public function getOne()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        // insert and get id
        $data = $this->_helper->getOneData($id);
        if(isset($data->ID)){
            $data->single_bill = ["value"=> "".$data->single_bill, "label"=> $data->single_bill==2 ? "Yes":"No"];
            $data->sub_data = $this->_sub_helper->getAllByCustomerId($data->ID);
        }
        $this->response($data);
    }


    public function getAllSelect()
    {
        $select = ["t1.ID AS value, t1.vendor_company AS  label"];
        $data = $this->_helper->getAllData("", [], $select);
        $this->response($data);
    }




    public function insert_address()
    {
        $validate_columns = ["gst_no"];
        // do validations
        $this->_helper->validate(CustomerHelper::validations, $validate_columns, $this->post);
        $columns = [
            "sd_customers_id",
            "billing_to",
            "gst_no",
            "address_one",
            "address_two",
            "state_name",
            "pin_code",
            "created_by",
            "created_time"
        ];
        $this->post["state_name"] = SmartData::post_select_value("state_name");
        $this->post["status"] = 5;
        $this->db->_db->Begin();
        // insert and get id
        $id = $this->_helper->insertSub($columns, $this->post);
        $this->db->_db->commit();
        //
        $this->response($id);
    }

    public function update_address()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        $validate_columns = ["gst_no"];
        // do validations
        $this->_helper->validate(CustomerHelper::validations, $validate_columns, $this->post);
        $columns = [
            "billing_to",
            "gst_no",
            "address_one",
            "address_two",
            "state_name",
            "pin_code",
            "created_by",
            "created_time"
        ];
        $this->post["state_name"] = SmartData::post_select_value("state_name");
        $this->_helper->updateSub($columns, $this->post, $id);
        //
        $this->response($id);
    }

    public function updateAddressStatus()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        $status = SmartData::post_data("status", "INTEGER");
        $columns = ["status"];
        $data_in = ["status" => $status];
        $this->_helper->updateSub($columns,  $data_in, $id);
        $this->responseMsg("Status Updated Successfully");
    }



    public function getAllAddress()
    {
        $id = SmartData::post_data("customer_id", "INTEGER");
        $sql = "t1.sd_customers_id=:id";
        $data_in["id"] =  $id;
        // Fetch data using the helper function
        $data = $this->_helper->getAllAddressData($sql, $data_in);
        // Send response
        $this->response($data);
    }

    public function getAllAddressSelect()
    {
        $id = isset($this->post["customer_id"]) ? intval($this->post["customer_id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid Vendor ID");
        }
        // insert and get id
        $sql = "t1.sd_customers_id=:id";
        $data_in = ["id" => $id];
        $select = ["t1.ID AS value, CONCAT(t2.state_name,' - ',t1.address_one) AS  label"];
        $data = $this->_helper->getAllAddressData($sql, $data_in, $select);
        $this->response($data);
    }



    /**
     * 
     */
    public function getOneAddress()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        // insert and get id
        $data = $this->_helper->getOneAddressData($id);
        $this->response($data);
    }

    public function migrate_old()
    {
        $_add_data = $this->_helper->getAllAddressData();
        $columns = ["vendor_company", "vendor_name",  "pan_no", "status", "created_by", "created_time"];
        foreach ($_add_data as $obj) {
            $_data = [
                "vendor_company" => $obj->vendor_company,
                "vendor_name" => $obj->vendor_name,
                "pan_no" => $obj->pan_no,
                "status" => 5
            ];
            $data = $this->_helper->checkVendorExists($_data["vendor_company"]);
            if (!isset($data->ID)) {
                $_vendor_id = $this->_helper->insert($columns,  $_data);
                // update venodr_id
                $this->_helper->updateId($obj->ID, $_vendor_id);
            } else {
                // venodr_id
                $this->_helper->updateId($obj->ID, $data->ID);
            }
        }
    }

    private function replace($add_id, $removal_required)
    {
        $ids = implode(",", $removal_required);
        $sql = "UPDATE " . Table::VENDOR_RATE . " SET sd_customer_address_id=" . $add_id . " WHERE sd_customer_address_id IN (" . $ids . ") ";
        echo $sql . "<br/>";
        $this->_helper->db->exec($sql);

        $invoice_sql = "UPDATE " . Table::INVOICE . " SET sd_customer_address_id=" . $add_id . " WHERE sd_customer_address_id IN (" . $ids . ") ";
        echo $invoice_sql . "<br/>";
        $this->_helper->db->exec($invoice_sql);
        //$this->_helper->db->exec($sql);
        $delete_customer_address = "DELETE FROM " . Table::SD_CUSTOMER_ADDRESS . " WHERE ID IN (" . $ids . ")";
        echo  $delete_customer_address . "<br/>";
        $this->_helper->db->exec($delete_customer_address);
    }

    public function migrate()
    {
        $customers = $this->_helper->getAllData();
        foreach ($customers as $_obj) {
            $address = $this->_helper->getAddressDataWithCustomerId($_obj->ID);
            $gst_data = [];
            foreach ($address as $_db) {
                if (isset($gst_data[$_db->gst_no])) {
                    $gst_data[$_db->gst_no][] = $_db->ID;
                } else {
                    $gst_data[$_db->gst_no] = [$_db->ID];
                }
            }
            //if(count($gst))
            foreach ($gst_data as $gst_number => $gst_ids) {
                // actual id required is 
                $actual_id_required = $gst_ids[0];
                // 
                if (count($gst_ids) > 1) {
                    $removal_required = array_slice($gst_ids, 1);
                    $this->replace($actual_id_required, $removal_required);
                }
                // now first replace this in vendor rate and invoice tables
            }


            $_obj->gst_data =  $gst_data;
        }
        //var_dump($customers);
    }
}
