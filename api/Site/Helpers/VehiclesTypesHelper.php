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
class VehiclesTypesHelper extends BaseHelper
{
    const schema = [
        "vehicle_type" => SmartConst::SCHEMA_DATE,
        "created_by" => SmartConst::SCHEMA_CUSER_ID,
        "created_time" => SmartConst::SCHEMA_CDATETIME
    ];
    /**
     * 
     */
    const validations = [
        "vehicle_type" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Specify vehicle type"
            ]
        ]
    ];


    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::VEHICLE_TYPES, $columns, $data);
    }
    /**
     * 
     */

    public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    {
        $from = Table::VEHICLE_TYPES . " t1";
        $sql = "";
        $select = !empty($select) ? $select : ["t1.*"];
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }
    

    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::VEHICLE_TYPES . " t1";
        $select = ["t1.*"];
        $sql = " t1.ID=:ID";
        $data_in = ["ID" => $id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }

    public function getVehicleTypeNameWithId($id){
        $_data = $this->getOneData($id);
        return isset($_data->vehicle_type) ? $_data->vehicle_type :"";
    }
    

}
