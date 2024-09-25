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
class SiteHelper extends BaseHelper
{

    const schema = [
        "setting_name" => SmartConst::SCHEMA_VARCHAR,
        "setting_value" => SmartConst::SCHEMA_TEXT,
        "created_by" => SmartConst::SCHEMA_CUSER_ID,
        "last_modified_time" => SmartConst::SCHEMA_CTIME
    ];
    /**
     * 
     */
    const validations = [
        "setting_name" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Setting Name"
            ],
            [
                "type" => SmartConst::VALID_STRING,
                "msg" => "Please Enter a valid string"
            ],
            [
                "type" => SmartConst::VALID_MAX_LENGTH,
                "max"=>20,
                "msg"=>"setting name Max character 20"
            ]
        ],
        "setting_value" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Setting value"
            ]
    
        ]
    ];

      // file handling 
    const FILE_FOLDER = "settings";
    const FILE_ORG_ONE = "org_one_file";
    const FILE_ORG_TWO = "org_two_file";

    public function getOrgFileOne($id)
    {
        return self::FILE_FOLDER . DS . $id . DS . self::FILE_ORG_ONE;
    }

    public function getOrgFileTwo($id)
    {
        return self::FILE_FOLDER . DS . $id . DS . self::FILE_ORG_TWO;
    }


    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::SITE, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::SITE, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [],$select=[],$group_by = "", $count = false,$single=false)
    {
        $from = Table::SITE;
        $select = !empty($select) ? $select : ["*"];
       // $order_by="last_modified_time DESC";
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }
    /**
     * 
     */
    public function getOneSettingData(string $setting_name){
        $sql = "setting_name=:setting_name";
        $data_in = ["setting_name"=>$setting_name];
        return $this->getAllData($sql,$data_in,[],"",false,true);        
    }

    public function getOneValue($name)
    {
        $from = Table::SITE;
        $select = ["setting_value"];
        $sql = "setting_name=:name";
        $data_in = ["name" => $name];
        $group_by = "";
        $order_by = "";
        $data =  $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
        return isset($data->setting_value) ? $data->setting_value : "";
    }


    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::SITE;
        $select = ["*"];
        $sql = "ID=:ID";
        $data_in = ["ID" => $id];
        $group_by = "";
        $order_by = "";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
        if(isset($data->ID)){
            $user_role_helper = new UserRoleHelper($this->db);
            $data->users = $user_role_helper->getSelectedUsersWithRoleId($id);
        }
        return $data;
    }
     /**
     * 
     */
    public function deleteOneId($id)
    {
        $from = Table::SITE;
        $this->deleteId($from,$id);
    }

    public function updateSiteCount(){
        $setting_name = "site_visitor_count";
        $exists_data = $this->getOneSettingData($setting_name);
        if(!$exists_data){
            $columns = [ "setting_name","setting_value","created_by"];
            $data["setting_name"] = $setting_name;
            $data["setting_value"] = 1;
            $this->insert($columns,$data);
        }else{
            $id = $exists_data->ID;
            $columns =  [ "setting_value","last_modified_time"];
            $data["setting_value"] = intval($exists_data->setting_value) + 1;
            $this->update($columns,$data,$id);
        } 
    }
  
}
