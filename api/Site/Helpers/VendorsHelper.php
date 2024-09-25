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
class VendorsHelper extends BaseHelper
{

    const schema = [
        "vendor_code" => SmartConst::SCHEMA_VARCHAR,
        "vendor_company" => SmartConst::SCHEMA_VARCHAR,
        "vendor_name" => SmartConst::SCHEMA_VARCHAR,
        "gst_no" => SmartConst::SCHEMA_VARCHAR,
        "pan_no" => SmartConst::SCHEMA_VARCHAR,
        "address_one" => SmartConst::SCHEMA_VARCHAR,        
        "address_two" => SmartConst::SCHEMA_VARCHAR,
        "state_name" => SmartConst::SCHEMA_VARCHAR,
        "pin_code" => SmartConst::SCHEMA_VARCHAR,
        "status" => SmartConst::SCHEMA_INTEGER,
        "created_by" => SmartConst::SCHEMA_CUSER_ID,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
        "last_modified_by" => SmartConst::SCHEMA_CUSER_ID,
        "last_modified_time" => SmartConst::SCHEMA_CDATETIME
    ];
    /**
     * 
     */
    const validations = [
        "vendor_code" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Vendor code"
            ]
        ],
        "vendor_company" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Vendor Company"
            ]
    
        ],
        "gst_no" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter GST Number"
            ]
    
            ],
        "pin_code" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Pin Code"
            ]
    
        ]
    ];

    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::VENDORS, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::VENDORS, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [],$select=[],$group_by = "", $count = false,$single=false)
    {
        $from = Table::VENDORS;
        $select = !empty($select) ? $select : ["*"];
       // $order_by="last_modified_time DESC";
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }
    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::VENDORS;
        $select = ["*"];
        $sql = "ID=:ID";
        $data_in = ["ID" => $id];
        $group_by = "";
        $order_by = "";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
        return $data;
    }
     /**
     * 
     */
    public function deleteOneId($id)
    {
        $from = Table::VENDORS;
        $this->deleteId($from,$id);
    }

}
