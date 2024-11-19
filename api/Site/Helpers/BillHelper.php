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
class BillHelper extends BaseHelper
{
    const schema = [
        "bill_start_date" => SmartConst::SCHEMA_DATE,
        "bill_end_date" => SmartConst::SCHEMA_DATE,
        "created_by" => SmartConst::SCHEMA_CUSER_ID,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
        "total_invoices" => SmartConst::SCHEMA_INTEGER,
        "unit_amount" => SmartConst::SCHEMA_FLOAT,
        "vehicle_amount" => SmartConst::SCHEMA_FLOAT,
        "others" => SmartConst::SCHEMA_FLOAT,
        "gst_amount" => SmartConst::SCHEMA_FLOAT,
        "total_amount" => SmartConst::SCHEMA_FLOAT
    ];
    /**
     * 
     */
    const validations = [
        "bill_start_date" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Specify bill start date"
            ]
        ],



        "bill_end_date" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Specify bill end date"
            ]
        ]



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

    /**
     * 
     */
    public function updateBillData($id, $data)
    {
        $columns = [
            "total_invoices",
            "unit_amount",
            "vehicle_amount",
            "others",
            "gst_amount",
            "total_amount"
        ];
        $this->update($columns, $data, $id);
    }

    public function checkBillExists($start_date, $end_date)
    {
        $from = Table::BILL . " t1";
        $select = ["t1.*"];
        $sql = "NOT (" . $start_date . " > t1.bill_end_date OR '" . $end_date . "' < t1.bill_start_date);";
        $data_in = [];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }
}
