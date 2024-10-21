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

    const schema_sub = [
        "sd_efl_consumption_id" => SmartConst::SCHEMA_INTEGER,
        "sd_meter_types_id" => SmartConst::SCHEMA_INTEGER,
        "count" => SmartConst::SCHEMA_INTEGER
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
        $sub_data = $data["sub_data"];
        if (isset($exist_data->ID)) {
            $this->update($update_cols, $data, $exist_data->ID);
            $this->insert_update_data($exist_data->ID, $sub_data);
        } else {
            $id =  $this->insert($insert_cols, $data);
            $this->insert_update_data($id, $sub_data);
        }
    }

    public function insertUpdateNew($_data){      
        $insert_columns = ["sd_hub_id", "sd_vendors_id", "sd_date", "unit_count", "created_by", "created_time"];
        $update_columns = ["unit_count", "last_modified_by", "last_modified_time"];
        $exist_data = $this->checkExists($_data["sd_vendors_id"], $_data["sd_date"]);
        $sub_data = $_data["sub_data"];
        if (isset($exist_data->ID)) {
            $this->update(  $update_columns, $_data, $exist_data->ID);
            $this->insert_update_data($exist_data->ID, $sub_data);
        } else {
            $id = $this->insert($insert_columns , $_data);
            $this->insert_update_data($id, $sub_data);
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
           // if (isset($ven_data->ID)) {
                $select = ["unit_count AS count,ID"];
                $from = Table::EFL_CONSUMPTION;
                $sql = " sd_hub_id=:ID AND sd_vendors_id=:ven_id AND sd_date=:date";
                $data_in = ["ID" => $hub_id, "ven_id" => $ven_data->ID, "date" => $date];
                $count = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
                $ven_data->sd_vendors_id = $ven_data->ID;
                // $ven_data->count = isset($count->count) ? $count->count : 0;
                $ven_data->unit_count = isset($count->count) ? $count->count : 0;
                $ven_data->ID = isset($count->ID) ? $count->ID : 0;
               // $ven_data->sd_vendors_id = $ven_data->ID;
              //  $ven_data->hub_id = $hub_id;
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
        INNER JOIN ".Table::EFL_CONSUMPTION ." t2 ON t2.ID=t1.sd_efl_consumption_id";
        $sql = "t2.sd_hub_id=:ID AND  YEAR(t2.sd_date) =:year AND MONTH(t2.sd_date) =:month GROUP BY date ";
        $data_in = ["ID" => $id, "month" => $month, "year" => $year];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, false, [], false);
        return $data;
    }

    public function getConsumptionInvoiceByDateVendor($vendor_id,$start_date, $end_date)
    {
        $select = [
            "t2.sd_date AS date, DAY(t2.sd_date) AS day_number ",
             "SUM(t1.count) as count"
        ];
        $from = Table::EFL_CONSUMPTION_SUB . " t1 
        INNER JOIN ".Table::EFL_CONSUMPTION ." t2 ON t2.ID=t1.sd_efl_consumption_id";
        $sql = "t1.sd_vendors_id=:id AND t1.sd_date BETWEEN :start_date AND :end_date";      
        $data_in = ["id"=>$vendor_id,  "start_date" => $start_date, "end_date" => $end_date];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, [], false);
       // var_dump($data_in);
        //var_dump($data);
        return isset($data->count) ? $data->count : 0;
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
        if (isset($exist_data->ID)) {
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
            // var_dump($_data);
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
                $this->deleteId(Table::EFL_CONSUMPTION_SUB, $obj->ID);
            }
        }
        //exit();
        // now comapare the ids and remove the data
    }

    public function ConsumptionTypeCount($id){
        $from = Table::METER_TYPES ." t1 
        LEFT JOIN ".Table::EFL_CONSUMPTION_SUB." t2 ON 
        t2.sd_meter_types_id =t1.ID AND t2.sd_efl_consumption_id=".$id."";
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


}
