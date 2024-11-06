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
class PaymentHelper extends BaseHelper
{
    const schema = [
        "sd_invoice_id" => SmartConst::SCHEMA_INTEGER,
        "sd_customer_id" => SmartConst::SCHEMA_INTEGER,
        "payment_date" => SmartConst::SCHEMA_DATE,
        "payment_mode" => SmartConst::SCHEMA_TEXT,
        "payment_amount" => SmartConst::SCHEMA_FLOAT,
        "created_by" => SmartConst::SCHEMA_CUSER_ID,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
        "last_modified_by" => SmartConst::SCHEMA_CUSER_ID,
        "last_modified_time" => SmartConst::SCHEMA_CDATETIME

    ];
    /**
     * 
     */
    const validations = [
        "sd_invoice_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Specify sd invoice id"
            ]
        ],
        "sd_customer_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Specify sd customer id"
            ]
        ],
        "payment_date"=> [
           [
              "type" => SmartConst::VALID_REQUIRED,
              "msg" => "Please Specify payment date"
           ]
        ],

        "payment_amount"=> [
    [
        "type" => SmartConst::VALID_REQUIRED,
        "msg" => "Please Specify payment amount"
    ]
],


    ];


    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::SD_PAYMENT, $columns, $data);
    }
    /**
     * 
     */
  
    /**
     * 
     */
    
    public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    {
        // Define the tables and joins
        $from = Table::SD_PAYMENT . " t1 LEFT JOIN 
        " . Table::INVOICE . " t2 ON t1.sd_invoice_id = t2.ID 
        LEFT JOIN " . Table::SD_CUSTOMER . " t3 ON t1.sd_customer_id = t3.ID";    
        // Define the default selection if not provided
        $select = !empty($select) ? $select : ["t1.*, t2.sd_invoice_id, t2.invoice_number, t3.sd_customer_id"];
        // Execute the query and return the result
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }


    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::SD_PAYMENT . " t1";
        $select = ["t1.*"];
        $sql = " t1.ID=:ID";
        $data_in = ["ID" => $id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }
    /**
     * 
     */
    public function deleteOneId($id)
    {
        $from = Table::SD_PAYMENT;
        $this->deleteId($from, $id);
    }
   
}
