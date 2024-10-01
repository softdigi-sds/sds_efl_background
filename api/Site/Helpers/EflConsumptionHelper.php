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
class EflConsumptionHelper extends BaseHelper
{
    const schema = [
        "sd_hub_id" => SmartConst::SCHEMA_INTEGER,
        "sd_vendors_id" => SmartConst::SCHEMA_INTEGER,
        "sd_date" => SmartConst::SCHEMA_DATE,
        "unit_count" => SmartConst::SCHEMA_FLOAT,
        "created_by" => SmartConst::SCHEMA_CUSER_ID,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
        "last_modified_by" => SmartConst::SCHEMA_CUSER_ID,
        "last_modified_time" => SmartConst::SCHEMA_CDATETIME,
    ];
    /**
     * 
     */
    const validations = [
        "sd_hub_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter sd hub id"
            ]
        ],



        "sd_vendors_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter sd vendors id"
            ]
        ],


        "sd_date" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter sd date"
            ]
        ],

        "unit_count" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter unit count"
            ]
        ],

    ];


    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::EFL_CONSUMPTION, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::EFL_CONSUMPTION, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    {
        $from = Table::EFL_CONSUMPTION;
        $select = !empty($select) ? $select : ["*"];
        // $order_by="last_modified_time DESC";
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }


    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::EFL_CONSUMPTION;
        $select = ["*"];
        $sql = "ID=:ID";
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
        $from = Table::EFL_CONSUMPTION;
        $this->deleteId($from, $id);
    }

    public function insertUpdate($data, $insert_cols, $update_cols)
    {
        $sql = " sd_hub_id=:sd_hub_id AND sd_vendors_id=:sd_vendors_id AND sd_date=:sd_date ";
        $data_in = ["sd_hub_id" => $data["sd_hub_id"], "sd_vendors_id" => $data["sd_vendors_id"], "sd_date" => $data["sd_date"]];
        $exist_data = $this->getAllData($sql, $data_in, ["ID"], "", false, true);
        if (isset($exist_data->ID)) {
            $this->update($update_cols, $data, $exist_data->ID);
        } else {
            $this->insert($insert_cols, $data);
        }
    }

    public function insertUpdateNew($_data){      
        $insert_columns = ["sd_hub_id", "sd_vendors_id", "sd_date", "unit_count", "created_by", "created_time"];
        $update_columns = ["unit_count", "last_modified_by", "last_modified_time"];
        $exist_data = $this->checkExists($_data["sd_vendors_id"], $_data["sd_date"]);
        if (isset($exist_data->ID)) {
            $this->update(  $update_columns, $_data, $exist_data->ID);
        } else {
            $this->insert($insert_columns , $_data);
        }
    }


    public function checkExists($vendor_id, $date)
    {
        $sql = "sd_vendors_id=:sd_vendors_id AND sd_date=:sd_date ";
        $data_in = [ "sd_vendors_id" =>$vendor_id, "sd_date" =>$date];
        $exist_data = $this->getAllData($sql, $data_in, ["ID"], "", false, true);
        return $exist_data;
    }


    public function getVendorsByHubId($hub_id, $date)
    {
        $_venoder_helper = new VendorsHelper($this->db);
        $data = $_venoder_helper->getVendorsByHubId($hub_id);
        foreach ($data as $ven_data) {
            if (isset($ven_data->ID)) {
                $select = ["unit_count AS count"];
                $from = Table::EFL_CONSUMPTION;
                $sql = " sd_hub_id=:ID AND sd_vendors_id=:ven_id AND sd_date=:date";
                $data_in = ["ID" => $hub_id, "ven_id" => $ven_data->ID, "date" => $date];
                $count = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
                // $ven_data->count = isset($count->count) ? $count->count : 0;
                $ven_data->unit_count = isset($count->count) ? $count->count : 0;
                $ven_data->sd_vendors_id = $ven_data->ID;
                $ven_data->hub_id = $hub_id;
                $ven_data->date = $date;
            }
        }
        return $data;
    }

    public function getCountByHubAndDate($id, $month, $year)
    {
        $select = [" SUM(unit_count) AS count, sd_date AS date "];
        $from = Table::EFL_CONSUMPTION;
        $sql = " sd_hub_id=:ID AND YEAR(sd_date)=:year AND MONTH(sd_date)=:month GROUP BY sd_date ";
        $data_in = ["ID" => $id, "month" => $month, "year" => $year];
        $count = $this->getAll($select, $from, $sql, "", "", $data_in, false, [], false);
        return $count;
    }

    public function getConsumptionInvoiceByDate($strt_date, $end_date)
    {
        $select = [" t1.*,SUM(t1.unit_count) AS count "];
        $from = Table::EFL_CONSUMPTION . " t1 ";
        $sql = "  t1.sd_date BETWEEN :strt_date AND :end_date GROUP BY t1.sd_vendors_id";
        $data_in = ["strt_date" => $strt_date, "end_date" => $end_date];
        $date = $this->getAll($select, $from, $sql, "", "", $data_in, false, [], false);
        return $date;
    }
}
