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
class BillHelper extends BaseHelper
{
    const schema = [
        "bill_start_date" => SmartConst::SCHEMA_DATE,
        "bill_end_date" => SmartConst::SCHEMA_DATE,
        "created_by"=> SmartConst::SCHEMA_CUSER_ID,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
    ];
    /**
     * 
     */
    const validations = [
        "bill_start_date" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Specify bill start date"
            ]
            ],
            
        
        
        "bill_end_date" => [    
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Specify bill end date"
            ]]
    
        
      
    ];


    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table:: BILL, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::BILL, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [],$select=[],$group_by = "", $count = false,$single=false)
    {
        $from = Table::BILL." t1 INNER JOIN ".Table::INVOICE." t2 ON t1.ID=t2.sd_bill_id ";
        $sql = " t1.ID > 0 GROUP BY t2.sd_bill_id ";
        $select = !empty($select) ? $select : ["t1.*, SUM(t2.unit_amount) AS unit_amt, SUM(t2.vehicle_amount) AS vehicle_amt "];
       return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }


    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::BILL." t1 INNER JOIN ".Table::INVOICE." t2 ON t1.ID=t2.sd_bill_id ";
        $select = ["t1.*, SUM(t2.unit_amount) AS unit_amt, SUM(t2.vehicle_amount) AS vehicle_amt "];
        $sql = " t1.ID=:ID GROUP BY t2.sd_bill_id ";
        $data_in = ["ID" => $id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }
     /**
     * 
     */
    public function deleteOneId($id)
    {
        $from = Table::BILL;
        $this->deleteId($from,$id);
    }
  
}