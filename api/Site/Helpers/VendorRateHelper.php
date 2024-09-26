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
class VendorRateHelper extends BaseHelper
{

    const schema = [
        "sd_hubs_id" => SmartConst::SCHEMA_INTEGER,
        "sd_vendors_id" => SmartConst::SCHEMA_INTEGER,
        "unit_rate_type" => SmartConst::SCHEMA_VARCHAR,
        "min_units" => SmartConst::SCHEMA_INTEGER,
        "unit_rate" => SmartConst::SCHEMA_FLOAT,
        "extra_unit_rate" => SmartConst::SCHEMA_FLOAT,        
        "parking_rate_type" => SmartConst::SCHEMA_VARCHAR,
        "parking_min_count" => SmartConst::SCHEMA_INTEGER,
        "parking_rate_vehicle" => SmartConst::SCHEMA_FLOAT,
        "effective_date" => SmartConst::SCHEMA_DATE,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
        "created_by" => SmartConst::SCHEMA_CUSER_ID,
        "last_modified_by" => SmartConst::SCHEMA_CUSER_ID,
        "last_modified_time" => SmartConst::SCHEMA_CDATETIME
    ];
    /**
     * 
     */
    const validations = [
        "sd_hubs_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Hub ID"
            ]
        ],
        "sd_vendors_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Vendor ID"
            ]
    
        ],
        "unit_rate_type" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter unit rate type"
            ]
    
            ],
        "min_units" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter units per minutes"
            ]
    
        ],
        "unit_rate" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter rate per unit"
            ]
    
        ],
        "extra_unit_rate" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter extra units"
            ]
    
        ],
        "parking_rate_type" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter parking rate type"
            ]
    
        ],
        "parking_min_count" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter parking minutes"
            ]
    
        ],
        "parking_rate_vehicle" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter parking rate vehicle"
            ]
    
        ],
        "effective_date" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter effective date"
            ]
    
        ]
    ];

    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::VENDOR_RATE, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::VENDOR_RATE, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [],$select=[],$group_by = "", $count = false,$single=false)
    {
        $from = Table::VENDOR_RATE;
        $select = !empty($select) ? $select : ["*"];
       // $order_by="last_modified_time DESC";
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }
    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::VENDOR_RATE;
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
        $from = Table::VENDOR_RATE;
        $this->deleteId($from,$id);
    }
    /**
     * 
     */
    public function checkVenodrByHubId($id)
    {
        $from = Table::VENDOR_RATE;
        $select = ["ID"];
        $sql = "sd_hubs_id=:ID";
        $data_in = ["ID" => $id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }
}
