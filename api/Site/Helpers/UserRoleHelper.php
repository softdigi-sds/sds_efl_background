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
class UserRoleHelper extends BaseHelper
{

    const schema = [
        "sd_mt_userdb_id" => SmartConst::SCHEMA_INTEGER,
        "sd_mt_role_id" => SmartConst::SCHEMA_INTEGER,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
        "created_by" => SmartConst::SCHEMA_CUSER_ID
    ];
    /**
     * 
     */
    const validations = [
        "sd_mt_userdb_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter User Id"
            ]
        ],
        "sd_mt_role_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Role Id"
            ]],
        "created_by" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter created_by"
            ]
        ],


    ];
    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::USERROLE, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::USERROLE, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [], $select_in=[],$group_by = "", $count = false)
    {
        $from = Table::USERROLE." t1 LEFT JOIN ".Table::USERS." t2 ON t1.sd_mt_userdb_id=t2.ID 
        LEFT JOIN ".Table::ROLES." t3 ON t1.sd_mt_role_id=t3.ID";
        $select = ["t1.*","t3.role_name","t2.ename"];
        $order_by="t1.created_time DESC";
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
        $from = Table::USERROLE. " t1 INNER JOIN ".Table::USERS." t2 ON t1.sd_mt_userdb_id = t2.ID";
        $select = ["t1.*,t2.ename as created_by"];
        $sql = "t1.ID=:ID";
        $data_in = ["ID" => $id];
        $group_by = "";
        $order_by = "";
        return $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
    }
    public function getUserRoleWithUserID($id)
    {
        $from = Table::USERROLE;
        $select = ["*"];
        $sql = "sd_mt_userdb_id=:ID";
        $data_in = ["ID" => $id];
        $group_by = "";
        $order_by = "";
        return $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
    }
    /**
     * 
     */
    public function deleteOneId($id)
    {
        $from = Table::USERROLE;
        $this->deleteId($from, $id);
    }
    /**
     * 
     */
    // to delete the user references from user_role table,
    // when user is getting deleted
    public function deleteUserRole($id)
    {
        $this->deleteBySql(Table::USERROLE,"sd_mt_userdb_id=:uid",["uid"=>$id]);
    }
    /**
     * 
     */public function deleteRoleUser($id)
    {
        $this->deleteBySql(Table::USERROLE,"sd_mt_role_id=:uid",["uid"=>$id]);
    }
    /**
     * 
     */
    public function insertRoles(int $st_user_id,$data)
    {
        // delete existing roles with user id
        $this->deleteBySql(Table::USERROLE,"sd_mt_userdb_id=:uid",["uid"=>$st_user_id]);
        // columns
        $columns = ["sd_mt_userdb_id","sd_mt_role_id","created_time","created_by"];       
        foreach($data as $single_data){
            $data_in = [];
            $data_in["sd_mt_userdb_id"] = $st_user_id;
            $data_in["sd_mt_role_id"] = isset($single_data["value"]) ? $single_data["value"] : 0;          
            $this->insert($columns,$data_in);
        }
       // return $this->insertDb(self::schema, Table::USERROLE, $columns, $data);
    }
    /**
     * 
     */
    public function insertUsers(int $st_role_id, array $data)
    {
        // delete existing roles with role id
        $this->deleteBySql(Table::USERROLE,"sd_mt_role_id=:uid",["uid"=>$st_role_id]);
        // columns
        $columns = ["sd_mt_userdb_id","sd_mt_role_id","created_time","created_by"];       
        foreach($data as $single_data){
            $data_in = [];
            $data_in["sd_mt_role_id"] = $st_role_id;
            $data_in["sd_mt_userdb_id"] = isset($single_data["value"]) ? $single_data["value"] : 0;          
            $this->insert($columns,$data_in);
        }
       
    }
    
    public function getSelectedRolesWithUserId(int $user_id){
        
        $sql = "t1.sd_mt_userdb_id=:ID";
        $select = ["t1.sd_mt_role_id as value","t3.role_name as label"];
        return $this->getAllData($sql,["ID"=>$user_id],$select);
    }
    /**
     * 
     */
    public function getSelectedUsersWithRoleId(int $role_id){
        $sql = "t1.sd_mt_role_id=:ID";
        $select = ["t1.sd_mt_userdb_id as value","t2.ename as label"];
        return $this->getAllData($sql,["ID"=>$role_id],$select);
    }
   
}
