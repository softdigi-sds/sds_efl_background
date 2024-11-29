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
        "type_hsn" => SmartConst::SCHEMA_VARCHAR,
        "vehicle_id" => SmartConst::SCHEMA_INTEGER,
        "price" => SmartConst::SCHEMA_FLOAT,
        "count" => SmartConst::SCHEMA_FLOAT,
        "month_avg" => SmartConst::SCHEMA_FLOAT,
        "min_units" => SmartConst::SCHEMA_FLOAT,
        "allowed_units" => SmartConst::SCHEMA_FLOAT,
        "total" => SmartConst::SCHEMA_FLOAT,
        "total_units" => SmartConst::SCHEMA_FLOAT,
        "extra_units" => SmartConst::SCHEMA_FLOAT
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

    //  4 => "RENT(LOCATION MAINTENANCE FEE(R)) ",
    //  7=>  "CHARGING UNITS BILLLED AS PER SUB METER FOR LOCATION MAINTENANCE(E)


    public function getCustomerData($customer_id)
    {

        $customer_specific = [
            29 => [
                4 => "RENT(LOCATION MAINTENANCE FEE(R)) ",
                7 => "CHARGING UNITS BILLED AS PER SUB METER FOR LOCATION MAINTENANCE(E)"
            ]
        ];
        return isset($customer_specific[$customer_id]) ? $customer_specific[$customer_id] : [];
    }

    public function modifyTypes($types, $customer_id)
    {
        $customer_specific = $this->getCustomerData($customer_id);
        // var_dump($customer_specific);
        if (empty($customer_specific)) {
            return $types;
        }
        // var_dump( $customer_specific);
        //exit();
        foreach ($types as $key => $desc) {
            $types[$key] = isset($customer_specific[$key]) ? $customer_specific[$key] : $desc;
        }
        return $types;
    }


    public function getInvoiceDesc($_data, $vehicle_id)
    {
        $vehicle_type = new VehiclesTypesHelper($this->db);
        $id = $_data["type"];
        $bill_type = isset($_data["bill_type"]) ? $_data["bill_type"] : "CMS";
        $customer_id = isset($_data["sd_customer_id"]) ? $_data["sd_customer_id"] : 0;
        // var_dump($_data);
        $_type_default = [
            1 => "ELECTRIC VEHICLE CHARGING+PARKING FEE",
            2 => "ELECTRIC VEHICLE PARKING FEE",
            3 => "UNITS BILLED AS PER ",
            4 => "RENT FOR ACCOMMODATION  ",
            5 => "UNITS BILLED AS PER ",
            6 => "RENT FOR ACCOMMODATION ",
            7 => "AC UNITS CONSUMED FOR OFFICE AND FACILITY ",
            8 => "SUPPORT SERVICES FEE ",
            100 => "EXTRA UNITS BILLED AS PER  "
        ];
        $_type = $this->modifyTypes($_type_default, $customer_id);
        $desc = isset($_type[$id]) ? $_type[$id] : "";
        $cms_sub_meter = $bill_type == "SUB_METER" ? "SUB METER" : "CMS";
        if ($id == 1 || $id == 2) {
            // add vehicle type also in brackets
            $vh_desc = $vehicle_type->getVehicleTypeNameWithId($vehicle_id);
            $desc = $desc . "(" . $vh_desc . ")";
        }
        if ($id == 3) {
            $desc = $desc .  $cms_sub_meter . "(AC)";
        }
        if ($id == 5) {
            $desc = $desc .  $cms_sub_meter . "(DC)";
        }
        if ($id == 100) {
            $desc = $desc .  $cms_sub_meter . "";
        }

        return $desc;
    }

    public function getInvoiceHSN($id)
    {
        $_type = [
            1 => "996743",
            2 => "996743",
            3 => "998714",
            4 => "997212",
            5 => "998714",
            6 => "997212",
            7 => "998714",
            8 => "995461",
            100 => "998714"
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
        $from = Table::SD_INVOICE_SUB . " t1";
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
        $data_in = ["id" => $invoice_id];
        $data = $this->getAllData($sql, $data_in);
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

    public function deleteWithInvoiceId($_id)
    {
        $sql = "sd_invoice_id=:id";
        $data_in = ["id" => $_id];
        $this->deleteBySql(Table::SD_INVOICE_SUB, $sql, $data_in);
    }


    public function insert_update_single($_data)
    {
        $columns_insert = [
            "sd_invoice_id",
            "type",
            "type_desc",
            "type_hsn",
            "vehicle_id",
            "price",
            "count",
            "month_avg",
            "min_units",
            "allowed_units",
            "total",
            "total_units",
            "extra_units"
        ];
        $vh_id = isset($_data["vehicle_id"]) ? $_data["vehicle_id"] : 0;
        $_data["type_desc"] = isset($_data["type"]) ? $this->getInvoiceDesc($_data,   $vh_id) : $_data["type_desc"];
        $_data["type_hsn"] =  isset($_data["type"]) ? $this->getInvoiceHSN($_data["type"]) :  $_data["type_hsn"];
        $id_inserted = $this->insert($columns_insert, $_data);
        return  $id_inserted;
    }

    public function insert_update_data($_id, $data, $_data)
    {
        $this->deleteBySql(Table::SD_INVOICE_SUB, "sd_invoice_id=:id", ["id" => $_id]);
        foreach ($data as $rate_data) {
            $rate_data["sd_invoice_id"] = $_id;
            $rate_data["bill_type"] = $_data["bill_type"];
            $rate_data["sd_customer_id"] = $_data["sd_customer_id"];
            $this->insert_update_single($rate_data);
        }
    }
}
