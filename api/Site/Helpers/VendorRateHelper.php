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
        "parking_extra_rate_vehicle" => SmartConst::SCHEMA_FLOAT,
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
        $from = Table::VENDOR_RATE." t1 LEFT JOIN ".Table::HUBS." t2 ON t1.sd_hubs_id=t2.ID LEFT JOIN ".Table::VENDORS." t3 ON t1.sd_vendors_id=t3.ID ";
        $select = !empty($select) ? $select : ["t1.*, t2.hub_id, t3.vendor_company"];
        $data =  $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
        $hub = $vendor = $consumption = $parking = [];
        foreach($data as $dt )
        {    if(isset($dt->ID)){
            $hub["value"] = $dt->sd_hubs_id;
            $hub["label"] = $dt->hub_id;

            $vendor["value"] = $dt->sd_vendors_id;
            $vendor["label"] = $dt->vendor_company;

            $consumption["value"] = $dt->unit_rate_type;
            $consumption["label"] = $dt->unit_rate_type;

            $parking["value"] = $dt->parking_rate_type;
            $parking["label"] = $dt->parking_rate_type;
            
            $dt->hub = $hub;
            $dt->vendor = $vendor; 
            $dt->consumption_type = $consumption;
            $dt->parking_type = $parking; 
           
        }
    
    }
    return $data;
    }
    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::VENDOR_RATE." t1 LEFT JOIN ".Table::HUBS." t2 ON t1.sd_hubs_id=t2.ID LEFT JOIN ".Table::VENDORS." t3 ON t1.sd_vendors_id=t3.ID ";
        $select = ["t1.*, t2.hub_id, t3.vendor_company"];
        $sql = "t1.ID=:ID";
        $data_in = ["ID" => $id];
        $group_by = "";
        $order_by = "";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
        if(isset($data->ID)){
            $data->hub = [];
            $data->hub["value"] = $data->sd_hubs_id;
            $data->hub["label"] = $data->hub_id;

            $data->vendor = [];
            $data->vendor["value"] = $data->sd_vendors_id;
            $data->vendor["label"] = $data->vendor_company;
        }
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
    public function checkEffectiveDateClash($effective_date)
    {
        $dateTime = new \DateTime($effective_date);
        $date = $dateTime->format('Y-m-d');
        // echo $date;exit();
        $from = Table::VENDOR_RATE;
        $select = ["ID,effective_date"];
        $sql = " effective_date <=:effective_date";
        $data_in = ["effective_date" => $date];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }
}
