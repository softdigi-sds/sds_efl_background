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
class HubsHelper extends BaseHelper
{
    const schema = [
        "hub_id" => SmartConst::SCHEMA_VARCHAR,
        "hub_name" => SmartConst::SCHEMA_VARCHAR,
        "hub_location"=> SmartConst::SCHEMA_TEXT,
        "sd_efl_office_id" => SmartConst::SCHEMA_INTEGER,
        "created_by"  => SmartConst::SCHEMA_CUSER_ID,
        "created_time"  => SmartConst::SCHEMA_CDATETIME,
        "last_modified_by"  => SmartConst::SCHEMA_CUSER_ID,
        "last_modified_time"  => SmartConst::SCHEMA_CDATETIME,
    ];
    /**
     * 
     */
    const validations = [
        "hub_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter hub id"
            ]
            ],
            
        
        
       "hub_name" => [    
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter hub name"
            ]],
    
        "hub_location" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter hub location"
            ]],
    
    
        "sd_efl_office_id"=> [
        [
            "type"=> SmartConst::VALID_REQUIRED,
            "msg"=> "Please Enter sd efl office id"
        ]],

        
    ];


    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::HUBS, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::HUBS, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [],$select=[],$group_by = "", $count = false,$single=false)
    {
        $from = Table::HUBS;
        $select = !empty($select) ? $select : ["*"];
       // $order_by="last_modified_time DESC";
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }


    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::HUBS;
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
        $from = Table::HUBS;
        $this->deleteId($from,$id);
    }
  
}