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
class RoleHelper extends BaseHelper
{

    const schema = [
        "role_name" => SmartConst::SCHEMA_VARCHAR,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
        "created_by" => SmartConst::SCHEMA_CUSER_ID
    ];
    /**
     * 
     */
    const validations = [
        "role_name" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Rolename"
            ],
            [
                "type" => SmartConst::VALID_STRING,
                "msg" => "Please Enter a valid string"
            ],
            [
                "type" => SmartConst::VALID_MAX_LENGTH,
                "max"=>100,
                "msg"=>" Role Name Maximum 100  characters"
            ] 
        ],
     
    ];
    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::ROLES, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::ROLES, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [],$select=[],$group_by = "", $count = false)
    {
        $from = Table::ROLES;
        $select = !empty($select) ? $select : ["*"];
        $order_by="role_name ASC";
        return $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, false, [], $count);
    }
    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::ROLES;
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
        $from = Table::ROLES;
        $this->deleteId($from,$id);
    }

   
  
}
