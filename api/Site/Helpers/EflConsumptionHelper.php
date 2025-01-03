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
        "sd_customer_id" => SmartConst::SCHEMA_INTEGER,
        "sd_date" => SmartConst::SCHEMA_DATE,
        "unit_count" => SmartConst::SCHEMA_FLOAT,
        "extra_units" => SmartConst::SCHEMA_INTEGER,
        "created_by" => SmartConst::SCHEMA_CUSER_ID,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
        "last_modified_by" => SmartConst::SCHEMA_CUSER_ID,
        "last_modified_time" => SmartConst::SCHEMA_CDATETIME,
    ];

    const schema_sub = [
        "sd_efl_consumption_id" => SmartConst::SCHEMA_INTEGER,
        "sd_meter_types_id" => SmartConst::SCHEMA_INTEGER,
        "count" => SmartConst::SCHEMA_FLOAT
    ];
    const schema_cms = [
        "txn_id" => SmartConst::SCHEMA_INTEGER,
        "date_time" => SmartConst::SCHEMA_DATE,
        "phone" => SmartConst::SCHEMA_VARCHAR,
        "name" => SmartConst::SCHEMA_VARCHAR,
        "vendor_name" => SmartConst::SCHEMA_VARCHAR,
        "email" => SmartConst::SCHEMA_VARCHAR,
        "charging_station" => SmartConst::SCHEMA_VARCHAR,
        "lat_lng" => SmartConst::SCHEMA_VARCHAR,
        "charge_point"  => SmartConst::SCHEMA_VARCHAR,
        "charge_point_code" => SmartConst::SCHEMA_VARCHAR,
        "connector_id" => SmartConst::SCHEMA_INTEGER,
        "connector_type" => SmartConst::SCHEMA_VARCHAR,
        "charge_point_type" => SmartConst::SCHEMA_VARCHAR,
        "charging_station_category"  => SmartConst::SCHEMA_VARCHAR,
        "start_time" => SmartConst::SCHEMA_DATE,
        "stop_time" => SmartConst::SCHEMA_DATE,
        "duration_seconds"  => SmartConst::SCHEMA_DATE,
        "duration_hh_mm"  => SmartConst::SCHEMA_DATE,
        "meter_start_wh"  => SmartConst::SCHEMA_FLOAT,
        "meter_stop_wh" => SmartConst::SCHEMA_FLOAT,
        "start_soc" => SmartConst::SCHEMA_FLOAT,
        "stop_soc" => SmartConst::SCHEMA_FLOAT,
        "energy_delivered_kWh"  => SmartConst::SCHEMA_FLOAT,
        "unit_rate_applicable" => SmartConst::SCHEMA_INTEGER,
        "charging_session_cost"  => SmartConst::SCHEMA_FLOAT,
        "service_fee" => SmartConst::SCHEMA_FLOAT,
        "service_fee_for_minutes"  => SmartConst::SCHEMA_FLOAT,
        "gst" => SmartConst::SCHEMA_FLOAT,
        "previous_unpaid_amount" => SmartConst::SCHEMA_FLOAT,
        "payment_processing_fee" => SmartConst::SCHEMA_FLOAT,
        "total" => SmartConst::SCHEMA_FLOAT,
        "refund_amount"  => SmartConst::SCHEMA_FLOAT,
        "payment_method" => SmartConst::SCHEMA_VARCHAR,
        "fleet" => SmartConst::SCHEMA_VARCHAR,
        "vehicle_number_plate" => SmartConst::SCHEMA_VARCHAR,
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



        "sd_customer_id" => [
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
    public function insertCms(array $columns, array $data)
    {
        return $this->insertDb(self::schema_cms, Table::CMS_DATA, $columns, $data);
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


    public function insertUpdateNew($_data, $extra = 0)
    {
        // var_dump($_data);
        //echo "<br/><br/>";
        $insert_columns = ["sd_hub_id", "sd_customer_id", "sd_date", "unit_count", "extra_units", "created_by", "created_time"];
        $update_columns = ["unit_count", "last_modified_by", "last_modified_time"];
        $exist_data = $this->checkExists($_data["sd_hub_id"], $_data["sd_customer_id"], $_data["sd_date"], $extra);
        // echo "<br/> date : " . $_data["sd_date"] . " customer " . $_data["sd_customer_id"] . "<br/>";
        $sub_data = $_data["sub_data"];
        if (isset($exist_data->ID)) {
            //  echo " <br/> Exists" . $exist_data->ID . "<br/>";
            $this->update($update_columns, $_data, $exist_data->ID);
            $this->insert_update_data($exist_data->ID, $sub_data);
        } else {
            $id = $this->insert($insert_columns, $_data);
            //  echo " <br/> New " . $id . "<br/>";
            $this->insert_update_data($id, $sub_data);
        }
    }


    public function checkExists($hub_id, $vendor_id, $date, $extra = 0)
    {
        $sql = "sd_hub_id=:sd_hub_id AND sd_customer_id=:sd_customer_id AND sd_date=:sd_date AND extra_units=:extra_dt";
        $data_in = ["sd_hub_id" => $hub_id, "sd_customer_id" => $vendor_id, "sd_date" => $date, "extra_dt" => $extra];
        $exist_data = $this->getAllData($sql, $data_in, ["ID"], "", false, true);
        return $exist_data;
    }


    public function getVendorsByHubId($hub_id, $date, $extra = 2)
    {
        $helper = new VendorRateHelper($this->db);
        $data = $helper->getAllWithHubId($hub_id);
        foreach ($data as $ven_data) {
            // if (isset($ven_data->ID)) {
            $select = ["unit_count AS count,ID"];
            $from = Table::EFL_CONSUMPTION;
            $sql = "sd_hub_id=:ID AND sd_customer_id=:ven_id AND sd_date=:date";
            $data_in = ["ID" => $hub_id, "ven_id" => $ven_data->sd_customer_id, "date" => $date];
            if ($extra != 2) {
                $sql .= " AND extra_units=:extra";
                $data_in["extra"] = $extra;
            }

            $count = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
            $ven_data->sd_customer_id = $ven_data->sd_customer_id;
            $ven_data->unit_count = isset($count->count) ? $count->count : 0;
            $ven_data->ID = isset($count->ID) ? $count->ID : 0;
            $ven_data->date = $date;
            //  }
        }
        return $data;
    }


    public function getCountByHubAndDate($id, $month, $year)
    {
        $select = [
            "t2.sd_date AS date, DAY(t2.sd_date) AS day_number ",
            "SUM(t1.count) as count"
        ];
        $from = Table::EFL_CONSUMPTION_SUB . " t1 
        INNER JOIN " . Table::EFL_CONSUMPTION . " t2 ON t2.ID=t1.sd_efl_consumption_id";
        $sql = "t2.sd_hub_id=:ID AND  YEAR(t2.sd_date) =:year AND MONTH(t2.sd_date) =:month GROUP BY date ";
        $data_in = ["ID" => $id, "month" => $month, "year" => $year];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, false, [], false);
        return $data;
    }

    public function getCountByHubAndStartEndDate($id, $start_date, $end_date)
    {
        $select = [
            "t2.sd_date AS date, DAY(t2.sd_date) AS day_number,t2.extra_units ",
            "SUM(t1.count) as count"
        ];
        $from = Table::EFL_CONSUMPTION_SUB . " t1 
        INNER JOIN " . Table::EFL_CONSUMPTION . " t2 ON t2.ID=t1.sd_efl_consumption_id";
        $sql = "t2.sd_hub_id=:ID AND t2.sd_date BETWEEN :start_date AND :end_date GROUP BY date,extra_units ";
        $data_in = ["ID" => $id, "start_date" => $start_date, "end_date" => $end_date];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, false, [], false);
        return $data;
    }

    public function hubTotal($sub_data)
    {
        $total = array_reduce($sub_data, function ($carry, $item) {
            return $carry + $item->count;
        }, 0);
        return $total;
    }


    public function getConsumptionInvoiceByDateVendor($hub_id, $vendor_id, $start_date, $end_date, $extra = 2)
    {
        $select = [
            "t2.sd_date AS date, DAY(t2.sd_date) AS day_number,sd_meter_types_id,t2.extra_units",
            "SUM(t1.count) as count,t1.sd_meter_types_id as ID"
        ];
        $from = Table::EFL_CONSUMPTION_SUB . " t1 
        INNER JOIN " . Table::EFL_CONSUMPTION . " t2 ON t2.ID=t1.sd_efl_consumption_id";
        $sql = "t2.sd_hub_id=:hub_id AND t2.sd_customer_id=:id AND t2.sd_date BETWEEN :start_date AND :end_date";
        if ($extra != 2) {
            $sql .= " AND t2.extra_units='" . $extra . "'";
        }
        $sql .= " GROUP BY sd_meter_types_id";
        $data_in = ["hub_id" => $hub_id, "id" => $vendor_id,  "start_date" => $start_date, "end_date" => $end_date];
        // var_dump($data_in);
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, false, [], false);
        return $data;
    }





    /**
     *  
     *  SUB HELPER FUNCTIONS
     * 
     */
    public function insertSub(array $columns, array $data)
    {
        return $this->insertDb(self::schema_sub, Table::EFL_CONSUMPTION_SUB, $columns, $data);
    }
    /**
     * 
     */
    public function updateSub(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema_sub, Table::EFL_CONSUMPTION_SUB, $columns, $data, $id);
    }

    public function getOneByConsumptionId($sd_efl_vehicles_id, $sd_vehicle_types_id)
    {
        $from = Table::EFL_CONSUMPTION_SUB;
        $select = ["*"];
        $sql = "sd_efl_consumption_id=:id AND sd_meter_types_id=:type_id";
        $data_in = ["id" => $sd_efl_vehicles_id, "type_id" => $sd_vehicle_types_id];
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


    public function getAllByConsumptionCountId($sd_efl_vehicles_id)
    {
        $from = Table::EFL_CONSUMPTION_SUB;
        $select = ["*"];
        $sql = "sd_efl_consumption_id=:id";
        $data_in = ["id" => $sd_efl_vehicles_id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, false, []);
        return $data;
    }

    public function insert_update_single($_data)
    {
        $exist_data = $this->getOneByConsumptionId($_data["sd_efl_consumption_id"], $_data["sd_meter_types_id"]);
        //echo " <br/><br/>";
        //var_dump($exist_data);
        // echo " <br/><br/>";
        //  echo " <br/>eid = " . $_data["sd_efl_consumption_id"] . " mid " .  $_data["sd_meter_types_id"] . " c =" . $_data["count"]; 

        if (isset($exist_data->ID)) {
            // echo "<br/>existed</br>";
            // exisitng so need to update
            $columns_update = ["count"];
            $this->updateSub($columns_update, $_data, $exist_data->ID);
            return  $exist_data->ID;
        } else {
            $columns_insert = [
                "sd_efl_consumption_id",
                "sd_meter_types_id",
                "count",
            ];
            // echo "<br/>new</br>";
            //var_dump($_data);
            // exit();
            $id_inserted = $this->insertSub($columns_insert, $_data);
            return  $id_inserted;
        }
    }

    public function insert_update_data($id, $data)
    {
        $exist_data = $this->getAllByConsumptionCountId($id);
        $ids = [];
        foreach ($data as $rate_data) {
            $rate_data["sd_efl_consumption_id"] = $id;
            // var_dump($rate_data);
            $ids[] = $this->insert_update_single($rate_data);
        }
        foreach ($exist_data as $obj) {
            if (!in_array($obj->ID, $ids)) {
                // $this->deleteId(Table::EFL_CONSUMPTION_SUB, $obj->ID);
            }
        }
        //exit();
        // now comapare the ids and remove the data
    }

    public function ConsumptionTypeCount($id)
    {
        $from = Table::METER_TYPES . " t1 
        LEFT JOIN " . Table::EFL_CONSUMPTION_SUB . " t2 ON 
        t2.sd_meter_types_id =t1.ID AND t2.sd_efl_consumption_id=" . $id . "";
        // echo  $from;
        $select = ["t1.*,t1.ID as sd_meter_types_id,t2.count"];
        $sql = "";
        $data_in = [];
        $data = $this->getAll(
            $select,
            $from,
            $sql,
            "",
            "",
            $data_in,
            false,
            []
        );
        return $data;
    }

    public function insertCmsData($data)
    {
        $columns = array_keys($data);
        $this->insertCms($columns, $data);
    }
}
