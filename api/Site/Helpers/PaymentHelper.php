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
        "payment_date" => SmartConst::SCHEMA_CDATE,
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
        "payment_mode" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Specify payment mode"
            ]
        ],

        "payment_amount" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Specify payment amount"
            ]
        ],
        "payment_date" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Specify payment_date"
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
        $from = Table::SD_PAYMENT . " t1 
        INNER JOIN " . Table::INVOICE . " t2 ON t1.sd_invoice_id = t2.ID 
        INNER JOIN " . Table::SD_CUSTOMER . " t3 ON t1.sd_customer_id = t3.ID";
        // Define the default selection if not provided
        $select = !empty($select) ? $select : ["t1.*, t1.sd_invoice_id, t2.invoice_number, t1.sd_customer_id,t3.vendor_company"];
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

    /**
     * 
     */
    public function getAllWithCustomerId($_id){
        $sql = "t1.sd_customer_id=:id";
        $data_in= ["id"=>$_id];
        $select =["t1.payment_date as date,t1.payment_mode as ref_no,t1.payment_amount as amount,'2' as status"];
        return $this->getAllData($sql,$data_in,$select);
    }
}
