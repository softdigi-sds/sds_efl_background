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
class MeterReadingsHelper extends BaseHelper
{
    const schema = [
        "sd_hub_id" => SmartConst::SCHEMA_INTEGER,
        "meter_year" => SmartConst::SCHEMA_INTEGER,
        "meter_month" => SmartConst::SCHEMA_INTEGER,
        "meter_start" => SmartConst::SCHEMA_FLOAT,
        "meter_end" => SmartConst::SCHEMA_FLOAT,
        "created_by" => SmartConst::SCHEMA_CUSER_ID,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
        "last_modified_by" => SmartConst::SCHEMA_CUSER_ID,
        "last_modified_time" => SmartConst::SCHEMA_CDATETIME,
    ];
    /**
     * 
     */
    const validations = [
        "sd_hub_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter sd hub id"
            ]
        ],



        "meter_year" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter meter year"
            ]
        ],


        "meter_month" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter meter month"
            ]
        ],

        "meter_start" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter meter start"
            ]
        ],
        "meter_end" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter meter end"
            ]
        ],


    ];


    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::METER_READINGS, $columns, $data);
    }
    /**
     * 
     */
  
    // public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    // {
    //     $from = Table::METER_READINGS;
    //     $select = !empty($select) ? $select : ["*"];
    //     // $order_by="last_modified_time DESC";
    //     return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    // }

    public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
{
    $from = Table::METER_READINGS;  


    $select = !empty($select) ? $select : [Table::METER_READINGS . ".*", Table::HUBS . ".hub_name"];

   
    $sql = !empty($sql) ? $sql : "LEFT JOIN " . Table::HUBS . " ON " . Table::METER_READINGS . ".sd_hub_id = " . Table::HUBS . ".hub_id";


    return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
}

    
    
    
  

   







  
}
