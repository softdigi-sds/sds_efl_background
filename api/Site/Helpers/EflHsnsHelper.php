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
class EflHsnsHelper extends BaseHelper
{
    const schema = [
        "hsn" => SmartConst::SCHEMA_VARCHAR,
        "bill_title" => SmartConst::SCHEMA_VARCHAR,
        "last_modified_time" => SmartConst::SCHEMA_CDATETIME,
    ];
    /**
     * 
     */
    const validations = [
        "hsn" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter hsn"
            ]
        ],



        "bill_title" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter sd vendors id"
            ]
        ]

      

    ];
  /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::EFLHSNS, $columns, $data);
    }

    /**
     * 
     */

    public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    {
        $from = Table::EFLHSNS;
        $select = !empty($select) ? $select : ["*"];
        // $order_by="last_modified_time DESC";
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }


    
   


}