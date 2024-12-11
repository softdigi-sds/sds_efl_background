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
class CustomerSubHelper extends BaseHelper
{

    const schema = [
        "sd_customer_id" => SmartConst::SCHEMA_INTEGER,
        "sd_efl_hsns_id" => SmartConst::SCHEMA_INTEGER,
        "hsn" => SmartConst::SCHEMA_VARCHAR,
        "title" => SmartConst::SCHEMA_VARCHAR
    ];
    /**
     * 
     */
    const validations = [
        "sd_customer_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Vendor ID"
            ]

        ],
        "sd_efl_hsns_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "HSN Required"
            ]

        ],
        "hsn" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter unit rate type"
            ]

        ],
        "title" => [
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
        return $this->insertDb(self::schema, Table::SD_CUSTOMER_HSN, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::SD_CUSTOMER_HSN, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    {
        $from = Table::SD_CUSTOMER_HSN . "";
        $select = !empty($select) ? $select : ["t1.*"];
        $data =  $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
        return $data;
    }
    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::SD_CUSTOMER_HSN . " t1 ";
        $select = ["t1.*"];
        $sql = "t1.ID=:ID";
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
        $from = Table::SD_CUSTOMER_HSN;
        $this->deleteId($from, $id);
    }



    public function getAllByCustomerId($_id)
    {
        $from = Table::SD_CUSTOMER_HSN . " t1 
        LEFT JOIN " . Table::SD_EFL_HSN . " t2 ON t1.sd_efl_hsns_id=t2.ID";
        $select = ["t1.*,t2.title_label"];
        $sql = "t1.sd_customer_id=:id";
        $data_in = ["id" => $_id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, false, []);
        // $out = [];
        foreach ($data as $obj) {
            $obj->sd_efl_hsns_id = ["value" =>  $obj->sd_efl_hsns_id, "label" =>  $obj->title_label];
        }
        return $data;
    }

    public function getAllByCustomerIdInvoice($_id)
    {
        $from = Table::SD_CUSTOMER_HSN . " t1";
        $select = ["t1.*"];
        $sql = "t1.sd_customer_id=:id";
        $data_in = ["id" => $_id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, false, []);
        $out = [];
        foreach ($data as $obj) {
            $out[$obj->sd_efl_hsns_id] = $obj->title;
        }
        return   $out;
    }



    public function getOneByCustomerId($customer_id)
    {
        $from = Table::SD_CUSTOMER_HSN;
        $select = ["*"];
        $sql = "sd_customer_id=:sd_customer_id";
        $data_in = ["sd_customer_id" => $customer_id];
        $data = $this->getAll(
            $select,
            $from,
            $sql,
            "",
            "",
            $data_in,
            true,
            []
        );
        return $data;
    }


    public function insert_update_single($_data)
    {

        $columns_insert = [
            "sd_customer_id",
            "sd_efl_hsns_id",
            "hsn",
            "title"
        ];
        $id_inserted = $this->insert($columns_insert, $_data);
        return  $id_inserted;
    }

    public function insert_update_data($_id, $data)
    {
        $this->deleteBySql(Table::SD_CUSTOMER_HSN, "sd_customer_id=:id", ["id" => $_id]);
        // var_dump($data);
        foreach ($data as $rate_data) {
            $rate_data["sd_customer_id"] = $_id;
            $rate_data["sd_efl_hsns_id"] = isset($rate_data["sd_efl_hsns_id"]) && isset($rate_data["sd_efl_hsns_id"]["value"]) ? $rate_data["sd_efl_hsns_id"]["value"] : 0;
            $ids[] = $this->insert_update_single($rate_data);
        }
    }


    /**** efl hsns  */
    public function getAllSelectHsns()
    {
        $from = Table::SD_EFL_HSN;
        $select = ["ID as value,title_label as label"];
        $sql = "ID<100";
        $data_in = [];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }
}
