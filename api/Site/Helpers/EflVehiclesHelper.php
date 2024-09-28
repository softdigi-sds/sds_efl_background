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
class EflVehiclesHelper extends BaseHelper
{
    const schema = [
        "sd_hub_id" => SmartConst::SCHEMA_INTEGER,
        "sd_vendors_id" => SmartConst::SCHEMA_INTEGER,
        "sd_date"=> SmartConst::SCHEMA_CDATE,
        "vehicle_count" => SmartConst::SCHEMA_INTEGER,
        "created_by"=> SmartConst::SCHEMA_CUSER_ID,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
        "last_modified_by"=> SmartConst::SCHEMA_INTEGER,
        "last_modified_time"=> SmartConst::SCHEMA_CDATETIME,
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
            ]],
            "sd_date" => [    
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Specify sd date"
            ]],
            "vehicle_count" => [    
                [
                    "type" => SmartConst::VALID_REQUIRED,
                    "msg" => "Please Enter vehicle count"
                ]]
    
        
      
    ];


    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::EFL_VEHICLES, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::EFL_VEHICLES, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [],$select=[],$group_by = "", $count = false,$single=false)
    {
        $from = Table::EFL_VEHICLES;
        $select = !empty($select) ? $select : ["*"];
       // $order_by="last_modified_time DESC";
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }


    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::EFL_VEHICLES;
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
        $from = Table::EFL_VEHICLES;
        $this->deleteId($from,$id);
    }
  
    public function getVendorsByHubId($id, $date)
    {

        $select = [" SUM(t1.vehicle_count) AS count, t1.sd_date AS date, t2.vendor_company "];
        $from = Table::EFL_VEHICLES." t1 LEFT JOIN ".Table::VENDORS." t2 ON t2.sd_hub_id=t1.sd_hub_id ";
        $sql = " t1.sd_hub_id=:ID AND t1.sd_date=:date";
        $data_in = ["ID" => $id, "date" =>$date];
        $group_by = " sd_date ";
        $count = $this->getAll($select, $from, $sql, $group_by, "", $data_in, false, [], false);
        return $count;
    }
    public function getCountByHubAndDate($id, $month, $year)
    {
        $select = [" SUM(vehicle_count) AS count, sd_date AS date "];
        $from = Table::EFL_VEHICLES;
        $sql = " sd_hub_id=:ID AND  YEAR(sd_date) =:year AND MONTH(sd_date) =:month ";
        $data_in = ["ID" => $id, "month" =>$month, "year" => $year];
        $group_by = " sd_date ";
        $count = $this->getAll($select, $from, $sql, $group_by, "", $data_in, false, [], false);
        return $count;
    }
}