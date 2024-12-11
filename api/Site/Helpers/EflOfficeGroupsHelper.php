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
class EflOfficeGroupsHelper extends BaseHelper
{
    const schema = [
        "sd_efl_office_id" => SmartConst::SCHEMA_INTEGER,
        "sd_mt_userdb_id" => SmartConst::SCHEMA_INTEGER,
        "last_modified_time"  => SmartConst::SCHEMA_CDATETIME,
    ];
    /**
     * 
     */
    const validations = [
        "sd_efl_office_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter sd hub id"
            ]
        ],
        "sd_mt_userdb_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter sd_mt_userdb_id"
            ]
        ],






    ];


    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::EFLOFFICE_GROUPS, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::EFLOFFICE_GROUPS, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [], $select_in = [], $group_by = "", $count = false)
    {
        $from = Table::EFLOFFICE_GROUPS . " t1 LEFT JOIN " . Table::EFLOFFICE . " t2 ON t1.sd_efl_office_id = t2.ID 
        LEFT JOIN " . Table::USERS . " t3 ON t1.sd_mt_userdb_id=t3.ID";
        $select = ["t1.*", "t3.ename", "t2.office_city"];
        $order_by = "t1.last_modified_time DESC";
        if (!empty($select_in)) {
            $select = $select_in;
            $order_by = "";
        }
        return $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, false, [], $count);
    }
    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::EFLOFFICE_GROUPS;
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
        $from = Table::EFLOFFICE_GROUPS;
        $this->deleteId($from, $id);
    }

    /**
     * 
     */ public function deleteHubRole($id)
    {
        $this->deleteBySql(Table::EFLOFFICE_GROUPS, "sd_efl_office_id=:uid", ["uid" => $id]);
    }
    public function insertRoles(int $hub_id, $data)
    {
        // delete existing roles with Hub id
        $this->deleteBySql(Table::EFLOFFICE_GROUPS, "sd_efl_office_id=:uid", ["uid" => $hub_id]);
        // columns
        $columns = ["sd_efl_office_id", "sd_mt_userdb_id", "last_modified_time"];
        foreach ($data as $single_data) {
            $data_in = [];
            $data_in["sd_efl_office_id"] = $hub_id;
            $data_in["sd_mt_userdb_id"] = isset($single_data["value"]) ? $single_data["value"] : 0;
            $this->insert($columns, $data_in);
        }
    }

    public function getSelectedRolesWithHubId(int $hub_id)
    {

        $sql = "t1.sd_efl_office_id=:ID";
        $select = ["t1.sd_mt_userdb_id as value", "t3.ename as label"];
        return $this->getAllData($sql, ["ID" => $hub_id], $select);
    }
}
