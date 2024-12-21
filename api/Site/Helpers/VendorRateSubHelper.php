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
use stdClass;

/**
 * Description of Data
 * 
 *  class helps to get the data from post with specified type 
 *
 * @author kms
 */
class VendorRateSubHelper extends BaseHelper
{

    const schema = [
        "sd_vendor_rate_id" => SmartConst::SCHEMA_INTEGER,
        "sd_hsn_id" => SmartConst::SCHEMA_INTEGER,
        "sd_vehicle_types_id" => SmartConst::SCHEMA_INTEGER,
        "rate_type" => SmartConst::SCHEMA_INTEGER,
        "min_start" => SmartConst::SCHEMA_INTEGER,
        "min_end" => SmartConst::SCHEMA_INTEGER,
        "price" => SmartConst::SCHEMA_FLOAT,
        "extra_price" => SmartConst::SCHEMA_FLOAT,
        "min_units_vehicle" => SmartConst::SCHEMA_INTEGER,
        "min_units_type" => SmartConst::SCHEMA_INTEGER
    ];
    /**
     * 
     */
    const validations = [
        "sd_vendor_rate_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Vendor ID"
            ]

        ],
        "sd_hsn_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "HSN Required"
            ]

        ],
        "rate_type" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter unit rate type"
            ]

        ],
        "effective_date" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter effective date"
            ]

        ]
    ];

    public function getRateTypes($id)
    {
        $_type = [
            1 => "Fixed",
            2 => "Minimum",
            3 => "Rate Per Unit"
        ];
        return isset($_type[$id]) ? $_type[$id] : "";
    }

    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::VENDOR_RATE_SUB, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::VENDOR_RATE_SUB, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    {
        $from = Table::VENDOR_RATE_SUB . "";
        $select = !empty($select) ? $select : ["t1.*"];
        $data =  $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
        return $data;
    }
    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::VENDOR_RATE_SUB . " t1 ";
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
        $from = Table::VENDOR_RATE_SUB;
        $this->deleteId($from, $id);
    }

    public function getTypes($index = 0)
    {
        $_types = [
            1 => "Parking & Charging",
            2 => "Parking",
            3 => "Charging (AC)",
            5 => "Charging (DC)",
            4 => "Rent",
            6 => "Infra Sharing",
            7 => "Charging(Office)"
        ];
        if ($index > 0) {
            return isset($_types[$index]) ? $_types[$index] : "";
        }
        return $_types;
    }

    public function getAllByVendorRateId($sd_vendor_rate_id)
    {
        $from = Table::VENDOR_RATE_SUB . " t1 
        LEFT JOIN " . Table::VEHICLE_TYPES . " t2 ON t1.sd_vehicle_types_id=t2.ID 
         LEFT JOIN " . Table::SD_EFL_HSN . " t3 ON t1.sd_hsn_id=t3.ID
        ";
        $select = ["t1.*,t2.vehicle_type,t3.title_label"];
        $sql = "t1.sd_vendor_rate_id=:id";
        $data_in = ["id" => $sd_vendor_rate_id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, false, []);
        // $out = [];
        foreach ($data as $key => $obj) {
            $hsn = $obj->sd_hsn_id;
            $obj->sd_hsn_id = ["value" => $hsn, "label" => $obj->title_label];
            $obj->sd_vehicle_types_id = ["value" => $obj->sd_vehicle_types_id, "label" => $obj->vehicle_type];
            $rate_type = $obj->rate_type;
            $obj->rate_type = ["value" => $rate_type, "label" => $this->getRateTypes($rate_type)];
            // $out[$key] = $obj;
        }
        return $data;
    }


    public function getOneByVendAndHsn($vend_rate_id, $hsn_id)
    {
        $from = Table::VENDOR_RATE_SUB;
        $select = ["*"];
        $sql = "sd_vendor_rate_id=:vend_rate_id AND sd_hsn_id=:sd_hsn_id";
        $data_in = ["vend_rate_id" => $vend_rate_id, "sd_hsn_id" => $hsn_id];
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
            "sd_vendor_rate_id",
            "sd_hsn_id",
            "sd_vehicle_types_id",
            "rate_type",
            "min_start",
            "min_end",
            "price",
            "extra_price",
            "min_units_vehicle"
        ];
        $id_inserted = $this->insert($columns_insert, $_data);
        return  $id_inserted;
    }

    public function insert_update_data($rate_id, $data)
    {
        // $exist_data = $this->getAllByVendorRateId($rate_id);
        //$ids = [];
        //echo "<br/><br/><br/> aLL";
        // var_dump($data);
        $this->deleteBySql(Table::VENDOR_RATE_SUB, "sd_vendor_rate_id=:id", ["id" => $rate_id]);
        foreach ($data as $rate_data) {

            $rate_data["sd_vendor_rate_id"] = $rate_id;
            $rate_data["sd_vehicle_types_id"] = isset($rate_data["sd_vehicle_types_id"]) && isset($rate_data["sd_vehicle_types_id"]["value"]) ? $rate_data["sd_vehicle_types_id"]["value"] : 0;

            $rate_data["sd_hsn_id"] = isset($rate_data["sd_hsn_id"]) && isset($rate_data["sd_hsn_id"]["value"]) ? $rate_data["sd_hsn_id"]["value"] : 0;
            $rate_data["rate_type"] = isset($rate_data["rate_type"]) && isset($rate_data["rate_type"]["value"]) ? $rate_data["rate_type"]["value"] : 0;
            //echo "<br/><br/><br/> sINGLE";
            // var_dump($rate_data);

            $ids[] = $this->insert_update_single($rate_data);
        }
        //   echo " <br/><br/><br/> iNSERT IDS ";
        //  var_dump($ids);
        // foreach ($exist_data as $obj) {
        //     if (!in_array($obj->ID, $ids)) {
        //         $this->deleteId(Table::VENDOR_RATE_SUB, $obj->ID);
        //     }
        // }
        //exit();
        // now comapare the ids and remove the data
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
        $from = Table::VENDOR_RATE;
        $select = ["ID,effective_date"];
        $sql = " effective_date >=:effective_date";
        $data_in = ["effective_date" => $effective_date];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }

    public function getVendorRateByHubVenID($hub_id, $vend_id)
    {
        $from = Table::VENDOR_RATE;
        $select = ["*"];
        $sql = "sd_hubs_id=:hub_id AND sd_vendors_id=:vend_id ";
        $data_in = ["hub_id" => $hub_id, "vend_id" => $vend_id,];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }



    /**** efl hsns  */
    public function getAllSelectHsns()
    {
        $from = Table::SD_EFL_HSN;
        $select = ["ID as value,title_label as label"];
        $sql = "ID<100";
        $data_in = [];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, false, []);
        return $data;
    }

    public function getAllHsnsDesc()
    {
        $from = Table::SD_EFL_HSN;
        $select = ["*"];
        $sql = "";
        $data_in = [];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, false, []);
        $out = new stdClass();
        $out->hsn_default = [];
        $out->desc_default = [];
        foreach ($data as $obj) {
            $out->hsn_default[$obj->ID] = $obj->hsn;
            $out->desc_default[$obj->ID] = $obj->bill_title;
        }
        return $out;
    }
}
