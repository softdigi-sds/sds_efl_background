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
class StateDbHelper extends BaseHelper
{

    const schema = [
        "state_id" => SmartConst::SCHEMA_INTEGER,
        "state_name" => SmartConst::SCHEMA_VARCHAR
    ];
    /**
     * 
     */
    const validations = [
        "state_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter State ID"
            ]
        ],
        "state_name" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter State Name"
            ]
    
        ]
    ];

    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::STATEDB, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::STATEDB, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [],$select=[],$group_by = "", $count = false,$single=false)
    {
        $from = Table::STATEDB;
        $select = !empty($select) ? $select : ["*"];
       // $order_by="last_modified_time DESC";
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }
    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::STATEDB;
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
        $from = Table::STATEDB;
        $this->deleteId($from,$id);
    }

    public function checkStateExist($state_name)
    {
        $from = Table::STATEDB;
        $select = ["ID"];
        $sql = "state_name=:state_name";
        $data_in = ["state_name" => $state_name];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }

}
