<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Database
 *
 * @author kms
 */

namespace Core\Helpers;


class SmartModel 
{

    private array $_schema = [];
    private string $_table = "";
    private array $_columns = [];
    private string $_sql = "";
    private array $_data = [];
    private bool $_single = false;
    private string $_from_sql = "";
    private string $_group_sql = "";
    private string $_order_sql = "";

    public SmartDatabase $_db;

    function __construct()
    {
        $this->_db = SmartDatabase::get_instance();
    }

    public function clear()
    {
        $this->_schema = [];
        $this->_table = "";
        $this->_columns = [];
        $this->_sql = "";
        $this->_data = [];
        $this->_single = false;
        $this->_from_sql = "";
        $this->_group_sql = "";
        $this->_order_sql = "";
    }

    public function Schema(array $schema)
    {
        $this->_schema = $schema;
        return $this;
    }

    /**
     * 
     */
    public function Table(string $table)
    {
        $this->_table = $table;
        return $this;
    }

    /**
     * 
     */
    public function Columns(array $columns)
    {
        $this->_columns = $columns;
        return $this;
    }

    /**
     * 
     */
    public function Select(array $select)
    {
        $this->_columns = $select;
        return $this;
    }

    /**
     * 
     */
    public function Where(string $sql)
    {
        $this->_sql = $sql;
        return $this;
    }
    /**
     * 
     */
    public function From(string $sql)
    {
        $this->_from_sql = $sql;
        return $this;
    }
    /**
     * 
     */
    public function One()
    {
        $this->_single = true;
        return $this;
    }
    /**
     * 
     */
    public function GroupBy(string $sql)
    {
        $this->_group_sql = $sql;
        return $this;
    }
    /**
     * 
     */
    public function OrderBy(string $sql)
    {
        $this->_group_sql = $sql;
        return $this;
    }

    /**
     * 
     */
    public function getDbData(array $data_in = [])
    {
        // select query
        $select = !empty($this->_columns) ? implode(",", $this->_columns) : " * ";
        // from sql 
        $this->_from_sql = strlen($this->_from_sql) > 1 ?  $this->_from_sql : $this->_table;
        // print other values
        $sql_str = sprintf("SELECT %s FROM %s ", $select, $this->_from_sql);
        //echo $sql_str;
        if (strlen($this->_sql) > 1) {
            $sql_str .= " WHERE " . $this->_sql;
        }
        if (strlen($this->_group_sql) > 1) {
            $sql_str .= " GROUP BY " . $this->_group_sql;
        }
        if (strlen($this->_order_sql) > 1) {
            $sql_str .= " ORDER BY " . $this->_order_sql;
        }

        $data =   $this->_db->getData($sql_str, $data_in, $this->_single);
        //
        return $data;
    }

    /**
     * 
     */
    public function insert(array $data)
    {
        $this->_data = $data;
        $final_data = $this->process_schema();
       // var_dump($final_data);
        $id =  $this->_db->InsertData($this->_table,$final_data);
        return $id;
    }
    /**
     * 
     */
    public function update(array $data,$id)
    {
        $this->_data = $data;
        $final_data = $this->process_schema();
        $where = "ID=" . $id;
        $this->_db->UpdateData($this->_table,$final_data,$where);
        return $id;
    }
    /**
     * 
     */
     public function deleteDbId($id)
    {
        $data = ["ID"=>$id];     
        $sql = "ID=:ID";
        $this->_db->DeleteData($this->_table,$sql,$data);
        return $id;
    }

    public function deleteDbSql($sql,$data)    {        
        $this->_db->DeleteData($this->_table,$sql,$data);
       // return $id;
    }

    /**
     * 
     */
    private function process_schema()
    {
        $final_data = [];
       // var_dump($this->_columns);
        foreach ($this->_columns as $column) {
            $final_data[$column] = $this->process_single_column($column);
        }
      //  var_dump($final_data);
        return $final_data;
    }
    /**
     * 
     */
    private function get_schema_type($param)
    {
        return isset($this->_schema[$param]) ?  $this->_schema[$param] : "";
    }
    /**
     * 
     */
    private function process_single_column($column)
    {
        $schema_type = $this->get_schema_type($column);
        $value = $this->get_single_value($column);        
        switch ($schema_type) {
            case SmartConst::SCHEMA_VARCHAR : return is_string($value) ? trim($value) :$value;
            case SmartConst::SCHEMA_INTEGER : return intval($value);
            case SmartConst::SCHEMA_FLOAT : return floatval($value);
            case SmartConst::SCHEMA_DATE : return $this->get_date_value($value);
            case SmartConst::SCHEMA_CDATE : return SmartGeneral::getCurrentDbDate();
            case SmartConst::SCHEMA_CDATETIME : return SmartGeneral::getCurrentDbDateTime();
            case SmartConst::SCHEMA_CUSER_ID : return SmartAuthHelper::getLoggedInId();
            case SmartConst::SCHEMA_CUSER_USERID : return SmartAuthHelper::getLoggedInUserId();
            case SmartConst::SCHEMA_CUSER_USERNAME : return SmartAuthHelper::getLoggedInUserName();
            default : return is_string($value) ? trim($value) :$value;        
        }
    }
    /**
     * 
     */
    private function get_single_value($column){       
        return isset($this->_data[$column])  ? ($this->_data[$column]) :null;
    }

    private function get_date_value($value){
        if(is_array($value)){
            // it is array format set to date format 
            $date_arr = $value;                           
            return $date_arr["year"] ."-" . $date_arr["month"] . "-" . $date_arr["day"];
        }else{
            // date format has to be changed to set to db
            return $value;
        }
    }
}
