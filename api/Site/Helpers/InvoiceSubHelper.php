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
class InvoiceSubHelper extends BaseHelper
{

    const schema = [
        "sd_invoice_id" => SmartConst::SCHEMA_INTEGER,
        "type" => SmartConst::SCHEMA_INTEGER,
        "type_desc" => SmartConst::SCHEMA_VARCHAR,
        "vehicle_id" => SmartConst::SCHEMA_INTEGER,
        "price" => SmartConst::SCHEMA_FLOAT,
        "count" => SmartConst::SCHEMA_FLOAT,
        "month_avg" => SmartConst::SCHEMA_FLOAT,
        "min_units" => SmartConst::SCHEMA_FLOAT,
        "allowed_units" => SmartConst::SCHEMA_FLOAT,
        "total" => SmartConst::SCHEMA_FLOAT
    ];
    /**
     * 
     */
    const validations = [
        "sd_invoice_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Vendor ID"
            ]

        ],
    ];

    public function getInvoiceDesc($id)
    {
        $_type = [
            1 => "Parking & Charging",
            2 => "Parking",
            3 => "AC UNTS",
            4 => "Rental",
            5 => "DC units",
            100 => "Extra Units"
        ];
        return isset($_type[$id]) ? $_type[$id] : "";
    }

    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::SD_INVOICE_SUB, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::SD_INVOICE_SUB, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    {
        $from = Table::SD_INVOICE_SUB . "";
        $select = !empty($select) ? $select : ["t1.*"];
        $data =  $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
        return $data;
    }
    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::SD_INVOICE_SUB . " t1 ";
        $select = ["t1.*"];
        $sql = "t1.ID=:ID";
        $data_in = ["ID" => $id];
        $group_by = "";
        $order_by = "";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
        return $data;
    }


    public function getAllByInvoiceId($invoice_id)
    {
        $sql = "t1.sd_invoice_id=:id";
        $data_in = ["id"=>$invoice_id];
        $data = $this->getAllData($sql,$data_in);
        return $data;
    }
    /**
     * 
     */
    public function deleteOneId($id)
    {
        $from = Table::SD_INVOICE_SUB;
        $this->deleteId($from, $id);
    }


    public function insert_update_single($_data)
    {
        $columns_insert = [
            "sd_invoice_id",
            "type",
            "type_desc",
            "vehicle_id",
            "price",
            "count",
            "month_avg",
            "min_units",
            "allowed_units",
            "total"
        ];
        $_data["type_desc"]= $this->getInvoiceDesc($_data["type"]);
        $id_inserted = $this->insert($columns_insert, $_data);
        return  $id_inserted;
    }

    public function insert_update_data($_id, $data)
    {
       $this->deleteBySql(Table::SD_INVOICE_SUB, "sd_invoice_id=:id", ["id" => $_id]);
        foreach ($data as $rate_data) {
            $rate_data["sd_invoice_id"] = $_id;
           $this->insert_update_single($rate_data);
        }
    }
}
