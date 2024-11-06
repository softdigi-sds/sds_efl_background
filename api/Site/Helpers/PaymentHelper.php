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
        return $this->insertDb(self::schema, Table::BILL, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::BILL, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    {
        $from = Table::BILL . " t1";
        $sql = "";
        $select = !empty($select) ? $select : ["t1.*"];
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }


    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::BILL . " t1";
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
        $from = Table::BILL;
        $this->deleteId($from, $id);
    }
}
