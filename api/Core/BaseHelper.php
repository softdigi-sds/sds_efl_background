<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core;

use Core\Helpers\SmartValidator;

use \Core\Helpers\SmartModel;

/**
 * Description of CustomEroorHandler
 *
 * @author kms
 */
class BaseHelper
{
    /**
     * 
     */
    public SmartModel $db;

    /**
     * 
     */
    function __construct(SmartModel $db)
    {
        // set the db
        $this->db = $db;
    }

    /**
     * 
     */
    public function insertDb(array $schema, string $table, array $columns, array $data)
    {
        $this->db->clear();
        $this->db->Schema($schema);
        $this->db->Table($table);
        $this->db->Columns($columns);
        return $this->db->insert($data);
    }
    /**
     * 
     */
    public function updateDb(array $schema, string $table, array $columns, array $data, $id)
    {
        $this->db->clear();
        $this->db->Schema($schema);
        $this->db->Table($table);
        $this->db->Columns($columns);
        return $this->db->update($data, $id);
    }
    /**
     * 
     */
    public function getAll(
        array $select,
        string $from,
        string $sql,
        string $group_by,
        string $order_by,
        array $data_in,
        $single=false,
        $limit = [],
        $count = false
    ) {
        $this->db->clear();
        $this->db->Select($select);
        $this->db->From($from);
        $this->db->Where($sql);
        $this->db->GroupBy($group_by);
        $this->db->OrderBy($order_by);
        if($count===true){
            $this->db->Select(["COUNT(*) as total_count"]);
            // $this->db->One();
        }
        if($single===true){
            $this->db->One();
        }
        $data = $this->db->getDbData($data_in);
        return $data;
    }
    /**
     * 
     */
    public function deleteId($table,$id){
        $this->db->clear();
        $this->db->Table($table);
        $this->db->deleteDbId($id);
    }

    public function deleteBySql($table,$sql,$data){
        $this->db->clear();
        $this->db->Table($table);
        $this->db->deleteDbSql($sql,$data);
    }


    /**
     * 
     */
    public function validate(array $rules, array $columns, array $post = [])
    {
        $errors = [];
        foreach ($columns as $column_index) {
            $rule = isset($rules[$column_index]) ? $rules[$column_index] : [];
            if (!empty($rule)) {
                $this->validate_single_rule($rule, $column_index, $post);
            } else {
                \CustomErrorHandler::triggerInternalError("Invalid Rules Set " . $column_index);
            }
        }
    }
    /**
     * 
     */
    private function validate_single_rule($rules, $column_index, $post)
    {
        $obj = new SmartValidator();
        foreach ($rules as $rule) {
            list($error, $msg) =  $obj->valid_field($rule, $column_index, $post);
            if ($error === true) {
                \CustomErrorHandler::triggerInvalid($msg);
            }
        }
    }
}
