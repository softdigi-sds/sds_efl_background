<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Helpers;

use Core\BaseHelper;
use Core\Helpers\SmartConst;

//
use Site\Helpers\TableHelper as Table;

/**
 * Description of Data
 * 
 *  class helps to get the data from post with specified type 
 *
 * @author kms
 */
class CustomerHelper extends BaseHelper
{

    const schema = [
        "vendor_company" => SmartConst::SCHEMA_VARCHAR,
        "vendor_name" => SmartConst::SCHEMA_VARCHAR,
        "pan_no" => SmartConst::SCHEMA_VARCHAR,
        "status" => SmartConst::SCHEMA_INTEGER,
        "created_by" => SmartConst::SCHEMA_CUSER_ID,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
        "last_modified_by" => SmartConst::SCHEMA_CUSER_ID,
        "last_modified_time" => SmartConst::SCHEMA_CDATETIME
    ];

    const address_schema = [
        "sd_customers_id" => SmartConst::SCHEMA_INTEGER,
        "billing_to" => SmartConst::SCHEMA_VARCHAR,
        "gst_no" => SmartConst::SCHEMA_VARCHAR,
        "address_one" => SmartConst::SCHEMA_VARCHAR,
        "address_two" => SmartConst::SCHEMA_VARCHAR,
        "state_name" => SmartConst::SCHEMA_VARCHAR,
        "pin_code" => SmartConst::SCHEMA_VARCHAR
    ];
    /**
     * 
     */
    const validations = [
        "vendor_company" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Vendor Company"
            ]
        ],
        "gst_no" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "GST Required"
            ]
        ],
        "pin_code" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Pin Code"
            ]
        ],
        "status" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter status"
            ]
        ],

    ];

    /**
     * 
     */
    public function  insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::SD_CUSTOMER, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::SD_CUSTOMER, $columns, $data, $id);
    }
    /**
     * 
     */
    // public function getAllData($sql = "", $data_in = [],$select=[],$group_by = "", $count = false,$single=false)
    // {
    //     $from = Table::VENDORS." t1 LEFT JOIN ".Table::HUBS." t2 ON t1.sd_hub_id=t2.ID LEFT JOIN ".Table::STATEDB." t3 ON t1.state_name=t3.ID ";
    //     $select = !empty($select) ? $select : ["t1.*, t2.hub_id, t2.hub_name, t3.state_name "];
    //    // $order_by="last_modified_time DESC";
    //     return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    // }
    public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    {
        // Define the tables and joins
        $from = Table::SD_CUSTOMER . " t1";
        // Define the default selection if not provided
        $select = !empty($select) ? $select : ["t1.*"];
        // Execute the query and return the result
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }




    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::SD_CUSTOMER . " t1";
        $select = ["t1.*"];
        $sql = "t1.ID=:ID";
        $data_in = ["ID" => $id];
        $data = $this->getAll($select,  $from, $sql, "", "", $data_in, true, []);
        return $data;
    }
    /**
     * 
     */
    public function deleteOneId($id)
    {
        $from = Table::SD_CUSTOMER;
        $this->deleteId($from, $id);
    }

    /**
     * 
     */
    public function checkVendorExists($company)
    {
        $from = Table::SD_CUSTOMER;
        $select = ["ID"];
        $sql = "vendor_company=:company";
        $data_in = ["company" => $company];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }


    public function insertSub(array $columns, array $data)
    {
        return $this->insertDb(self::address_schema, Table::SD_CUSTOMER_ADDRESS, $columns, $data);
    }
    /**
     * 
     */
    public function updateSub(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::address_schema, Table::SD_CUSTOMER_ADDRESS, $columns, $data, $id);
    }

    public function deleteOneIdSub($id)
    {
        $from = Table::SD_CUSTOMER_ADDRESS;
        $this->deleteId($from, $id);
    }

    public function getOneAddressData($id)
    {
        $from = Table::SD_CUSTOMER_ADDRESS . " t1";
        $select = ["t1.*"];
        $sql = "t1.ID=:ID";
        $data_in = ["ID" => $id];
        $data = $this->getAll($select,  $from, $sql, "", "", $data_in, true, []);
        return $data;
    }

    public function getAllAddressData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    {
        // Define the tables and joins
        $from = Table::SD_CUSTOMER_ADDRESS . " t1 LEFT JOIN " . Table::STATEDB . " t2 ON t2.ID=t1.state_name";
        // Define the default selection if not provided
        $select = !empty($select) ? $select : ["t1.*,t2.state_name as stateName"];
        // Execute the query and return the result
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }

    public function updateId($id, $customer_id)
    {
        $columns = [
            "sd_hub_id"
        ];
        $_data = ["sd_hub_id" => $customer_id];
        $this->updateSub($columns,  $_data, $id);
    }



    // migration functions

}
