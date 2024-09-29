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
class HubGroupsHelper extends BaseHelper
{
    const schema = [
        "sd_hub_id" => SmartConst::SCHEMA_INTEGER,
        "sd_mt_role_id"=> SmartConst::SCHEMA_INTEGER,
        "last_modified_time"  => SmartConst::SCHEMA_CDATETIME,
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
            
        
        
     "sd_mt_role_id" => [    
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter sd_mt_role_id"
            ]],
    
      
    
    

        
    ];


    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::HUB_GROUPS, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::HUB_GROUPS, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [], $select_in=[],$group_by = "", $count = false)
    {
        $from = Table::HUB_GROUPS." t1 LEFT JOIN ".Table::HUBS." t2 ON t1.sd_hub_id = t2.ID 
        LEFT JOIN ".Table::ROLES." t3 ON t1.sd_mt_role_id=t3.ID";
        $select = ["t1.*","t3.role_name","t2.hub_id","t2.hub_name"];
        $order_by="t1.last_modified_time DESC";
        if(!empty($select_in)){
            $select = $select_in;
            $order_by="";
        }
        return $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, false, [], $count);
    }
    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::HUB_GROUPS;
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
        $from = Table::HUB_GROUPS;
        $this->deleteId($from,$id);
    }
  
      /**
     * 
     */public function deleteHubRole($id)
     {
        $this->deleteBySql(Table::HUB_GROUPS,"sd_hub_id=:uid",["uid"=>$id]);
    }
    public function insertRoles(int $hub_id,$data)
    {
        // delete existing roles with Hub id
        $this->deleteBySql(Table::HUB_GROUPS,"sd_hub_id=:uid",["uid"=>$hub_id]);
        // columns
        $columns = ["sd_hub_id","sd_mt_role_id","last_modified_time"];       
        foreach($data as $single_data){
            $data_in = [];
            $data_in["sd_hub_id"] = $hub_id;
            $data_in["sd_mt_role_id"] = isset($single_data["value"]) ? $single_data["value"] : 0;          
            $this->insert($columns,$data_in);
        }
    }
    
    public function getSelectedRolesWithHubId(int $hub_id){
        
        $sql = "t1.sd_hub_id=:ID";
        $select = ["t1.sd_mt_role_id as value","t3.role_name as label"];
        return $this->getAllData($sql,["ID"=>$hub_id],$select);
    }
    
}