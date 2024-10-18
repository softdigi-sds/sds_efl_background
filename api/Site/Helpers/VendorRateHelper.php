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
class VendorRateHelper extends BaseHelper
{

    const schema = [
        "sd_hubs_id" => SmartConst::SCHEMA_INTEGER,
        "sd_vendors_id" => SmartConst::SCHEMA_INTEGER,
        "sd_hsn_id" => SmartConst::SCHEMA_INTEGER,
        "rate_type" => SmartConst::SCHEMA_INTEGER,
        "min_start" => SmartConst::SCHEMA_INTEGER,
        "min_end" => SmartConst::SCHEMA_INTEGER,
        "price" => SmartConst::SCHEMA_FLOAT,
        "extra_price" => SmartConst::SCHEMA_FLOAT,
        "effective_date" => SmartConst::SCHEMA_DATE,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
        "created_by" => SmartConst::SCHEMA_CUSER_ID,
        "last_modified_by" => SmartConst::SCHEMA_CUSER_ID,
        "last_modified_time" => SmartConst::SCHEMA_CDATETIME
    ];
    /**
     * 
     */
    const validations = [
        "sd_hubs_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Hub ID"
            ]
        ],
        "sd_vendors_id" => [
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

    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::VENDOR_RATE, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::VENDOR_RATE, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    {
        $from = Table::VENDOR_RATE . " t1 
        LEFT JOIN " . Table::HUBS . " t2 ON t1.sd_hubs_id=t2.ID LEFT JOIN " . Table::VENDORS . " t3 ON t1.sd_vendors_id=t3.ID ";
        $select = !empty($select) ? $select : ["t1.*, t2.hub_id, t3.vendor_company"];
        $data =  $this->getAll($select, $from, $sql, $group_by, "t1.effective_date DESC", $data_in, $single, [], $count);
        return $data;
    }
    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::VENDOR_RATE . " t1 LEFT JOIN " . Table::HUBS . " t2 ON t1.sd_hubs_id=t2.ID LEFT JOIN " . Table::VENDORS . " t3 ON t1.sd_vendors_id=t3.ID ";
        $select = ["t1.*, t2.hub_id, t3.vendor_company"];
        $sql = "t1.ID=:ID";
        $data_in = ["ID" => $id];
        $group_by = "";
        $order_by = "";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
        if (isset($data->ID)) {
            $hub_id = $data->sd_hubs_id;
            $data->sd_hubs_id = [];
            $data->sd_hubs_id["value"] = $hub_id;
            $data->sd_hubs_id["label"] = $data->hub_id;
            $vendor_id = $data->sd_vendors_id;
            $data->sd_vendors_id = [];
            $data->sd_vendors_id["value"] = $vendor_id;
            $data->sd_vendors_id["label"] = $data->vendor_company;
        }
        return $data;
    }
    /**
     * 
     */
    public function deleteOneId($id)
    {
        $from = Table::VENDOR_RATE;
        $this->deleteId($from, $id);
    }

    public function insert_update_single($_data)
    {
        $exist_data = $this->getOneByVendorHsn($_data["sd_hubs_id"], $_data["sd_vendors_id"], $_data["sd_hsn_id"]);

        if (isset($exist_data->ID)) {
            // exisitng so need to update
            $columns_update = ["rate_type", "min_start", "min_end", "price", "extra_price", "effective_date"];
            $this->update($columns_update, $_data, $exist_data->ID);
            return  $exist_data->ID;
        } else {
            $columns_insert = [
                "sd_hubs_id",
                "sd_vendors_id",
                "sd_hsn_id",
                "rate_type",
                "min_start",
                "min_end",
                "price",
                "extra_price",
                "effective_date"
            ];
            $id_inserted = $this->insert($columns_insert, $_data);
            return  $id_inserted;
        }
    }

    public function insert_update_data($data)
    {
        $exist_data = $this->getAllVendorHubDate($data["sd_hubs_id"], $data["sd_vendors_id"], $data["effective_date"]);
        $ids = [];
        foreach ($data["rate_data"] as $rate_data) {
            $rate_data["sd_hubs_id"] = $data["sd_hubs_id"];
            $rate_data["sd_vendors_id"] = $data["sd_vendors_id"];
            $rate_data["effective_date"] = $data["effective_date"];
            $ids[] = $this->insert_update_single($rate_data);
        }
        // now comapare the ids and remove the data
    }


    public function getOneByVendorHsn($hub_id, $vendor_id, $hsn_id)
    {
        $from = Table::VENDOR_RATE;
        $select = ["*"];
        $sql = "sd_hubs_id=:hub_id AND sd_vendors_id=:vend_id AND sd_hsn_id=:sd_hsn_id";
        $data_in = ["hub_id" => $hub_id, "vend_id" => $vendor_id, $hsn_id => $hsn_id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }

    public function getAllVendorHubDate($hub_id, $vend_id, $effective_date)
    {
        $from = Table::VENDOR_RATE;
        $select = ["*"];
        $sql = "sd_hubs_id=:hub_id AND sd_vendors_id=:vend_id AND effective_date=:effective_date";
        $data_in = ["hub_id" => $hub_id, "vend_id" => $vend_id, "effective_date" => $effective_date];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, false, []);
        return $data;
    }

    public function getVendorHubDetails($hub_id, $vend_id, $effective_date)
    {
        // echo  $hub_id . "b " . $vend_id .  "effective_date " . $effective_date;
        $from = Table::VENDOR_RATE;
        $select = ["*"];
        $sql = "sd_hubs_id=:hub_id AND sd_vendors_id=:vend_id";
        $data_in = ["hub_id" => $hub_id, "vend_id" => $vend_id];
        $data = $this->getAll($select, $from, $sql, "", "effective_date DESC", $data_in, TRUE, []);
        return $data;
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
}
