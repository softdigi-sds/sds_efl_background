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
        "hub_location" => SmartConst::SCHEMA_TEXT,
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
            ]
        ],

        "hub_location" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter hub location"
            ]
        ],


        "sd_efl_office_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter sd efl office id"
            ]
        ],


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
    public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    {
        $from = Table::HUBS . " t1 
        INNER JOIN " . Table::EFLOFFICE . " t2 ON t1.sd_efl_office_id=t2.ID ";
        $select = !empty($select) ? $select : ["t1.*, t2.office_city "];
        // $order_by="last_modified_time DESC";
        $data =  $this->getAll($select, $from, $sql, $group_by, "office_city ASC", $data_in, $single, [], $count);
        $city = [];
        if(!empty($data)){
            foreach ($data as $dt) {
                if (isset($dt->ID)) {
                    //$city["value"] = $dt->sd_efl_office_id;
                    //$city["label"] = $dt->office_city;
                    $_hub_grp_helper = new HubGroupsHelper($this->db);
                    $dt->role = $_hub_grp_helper->getSelectedRolesWithHubId($dt->ID);
                    //$dt->city = $city;
                }
        }
        
        }
        return $data;
    }


    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::HUBS . " t1 INNER JOIN " . Table::EFLOFFICE . " t2 ON t1.sd_efl_office_id=t2.ID ";
        $select = ["t1.*, t2.office_city "];
        $sql = "t1.ID=:ID";
        $data_in = ["ID" => $id];
        $group_by = "";
        $order_by = "";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
        if (isset($data->ID)) {
            $_hub_grp_helper = new HubGroupsHelper($this->db);
            $data->role = $_hub_grp_helper->getSelectedRolesWithHubId($data->ID);
            $city_id = $data->sd_efl_office_id;
            $data->sd_efl_office_id = ["value" => $city_id, "label" => $data->office_city];
        }
        return $data;
    }
    /**
     * 
     */
    public function deleteOneId($id)
    {
        $from = Table::HUBS;
        $this->deleteId($from, $id);
    }
    /**
     * 
     */
    public function checkHubExist($hub_id)
    {
        $from = Table::HUBS;
        $select = ["ID"];
        $sql = "hub_id=:ID";
        $data_in = ["ID" => $hub_id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }
    /**
     * 
     */
    public function checkHubByOfficeId($id)
    {
        $from = Table::HUBS;
        $select = ["ID"];
        $sql = "sd_efl_office_id=:ID";
        $data_in = ["ID" => $id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }

    public function getHubID($hub_id)
    {
        $from = Table::HUBS;
        $select = ["ID"];
        $sql = "hub_id=:ID";
        $data_in = ["ID" => $hub_id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return !empty($data) ? intval($data->ID) : 0;
    }
      public function insertUpdateNew($_data){
        $insert_columns = ["hub_id", "hub_name", "sd_efl_office_id", "created_by", "created_time"];
        $update_columns = [ "last_modified_by", "last_modified_time"];
        $exist_data = $this->checkExists($_data["sd_efl_office_id"], $_data["hub_id"]);
        if (isset($exist_data->ID)) {
            $this->update(  $update_columns, $_data, $exist_data->ID);
        } else {
            $this->insert($insert_columns , $_data);
        }
    }


    public function checkExists($office_id, $hub_id)
    {
        $sql = "sd_efl_office_id=:office_id AND hub_id=:hub_id ";
        $data_in = [ "office_id" =>$office_id, "hub_id" =>$hub_id];
        $exist_data = $this->getAllData($sql, $data_in, ["t1.ID"], "", false, true);
        return $exist_data;
    }

}
