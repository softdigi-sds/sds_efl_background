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
class InvoiceHelper extends BaseHelper
{
    const schema = [
        "sd_bill_id" => SmartConst::SCHEMA_INTEGER,
        "sd_hub_id" => SmartConst::SCHEMA_INTEGER,
        "sd_vendor_id" => SmartConst::SCHEMA_INTEGER,
        "total_units"=> SmartConst::SCHEMA_FLOAT,
        "total_vehicles"=> SmartConst::SCHEMA_INTEGER,
        "unit_amount"=> SmartConst::SCHEMA_FLOAT,
        "vechicle_amount"=> SmartConst::SCHEMA_FLOAT,
        "gst_percentage"=> SmartConst::SCHEMA_FLOAT,
        "gst_amount" => SmartConst::SCHEMA_FLOAT,
        "total_amount" => SmartConst::SCHEMA_FLOAT,
        "status" => SmartConst::SCHEMA_INTEGER,
    ];
    /**
     * 
     */
    const validations = [
        "sd_bill_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter sd bill id"
            ]
            ],
        "sd_hub_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter sd hub id"
            ]
            ],
        "sd_vendor_id" => [    
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter sd vendors id"
            ]],
            "total_units" => [    
                [
                    "type" => SmartConst::VALID_REQUIRED,
                    "msg" => "Please Enter total_units"
                ]],
                "total_vehicles" => [    
                    [
                        "type" => SmartConst::VALID_REQUIRED,
                        "msg" => "Please Enter total vehicals"
                    ]],
                "unit_amount" => [    
                        [
                            "type" => SmartConst::VALID_REQUIRED,
                            "msg" => "Please Enter unit amount"
                        ]],
                "vechicle_amount" => [    
                        [
                            "type" => SmartConst::VALID_REQUIRED,
                            "msg" => "Please Enter vechical amount"
                        ]], 
                "gst_percentage" => [    
                            [
                                "type" => SmartConst::VALID_REQUIRED,
                                "msg" => "Please Enter gst percentage"
                            ]],       
                            "gst_amount" => [    
                            [
                                "type" => SmartConst::VALID_REQUIRED,
                                "msg" => "Please Enter gst amount"
                            ]],             
                "total_amount"=> [    
                    [
                        "type" => SmartConst::VALID_REQUIRED,
                        "msg" => "Please Enter total amount"
                    ]],    
      
    ];


    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::INVOICE, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::INVOICE, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [],$select=[],$group_by = "", $count = false,$single=false)
    {
        $from = Table::INVOICE ;
        $select = !empty($select) ? $select : ["*"];
       // $order_by="last_modified_time DESC";
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }


    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::INVOICE;
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
        $from = Table::INVOICE;
        $this->deleteId($from,$id);
    }
  
    public function checkInvoiceExist($vend_id)
    {
        $from = Table::INVOICE;
        $select = ["ID"];
        $sql = "sd_vendor_id=:vend_id";
        $data_in = ["vend_id" => $vend_id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }
}