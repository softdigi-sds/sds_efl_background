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
class EflOfficeHelper extends BaseHelper
{
    const schema = [
        "office_city" => SmartConst::SCHEMA_VARCHAR,
        "address_one" => SmartConst::SCHEMA_TEXT,
        "address_two"=> SmartConst::SCHEMA_TEXT,
        "gst_no" => SmartConst::SCHEMA_VARCHAR,
        "pan_no" => SmartConst::SCHEMA_VARCHAR,
        "cin_no"  => SmartConst::SCHEMA_VARCHAR,
        "state"  => SmartConst::SCHEMA_VARCHAR,
        "pin_code"  => SmartConst::SCHEMA_VARCHAR,
        "cgst"=> SmartConst::SCHEMA_FLOAT,
        "igst"=> SmartConst::SCHEMA_FLOAT,
        "sgst"=> SmartConst::SCHEMA_FLOAT,
        "status"    => SmartConst::SCHEMA_INTEGER,
        "created_by"  => SmartConst::SCHEMA_CUSER_ID,
        "created_time"  => SmartConst::SCHEMA_CDATETIME,
        "last_modified_by"  => SmartConst::SCHEMA_CUSER_ID,
        "last_modified_time"  => SmartConst::SCHEMA_CDATETIME,
    ];
    /**
     * 
     */
    const validations = [
        "office_city" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter office city"
            ]
            ],
            
        
        
        "address_one" => [    
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter address one"
            ]],
    
        
        "address_two" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter address two"
            ]],
    
    
    "gst_no"=> [
        [
            "type"=> SmartConst::VALID_REQUIRED,
            "msg"=> "Please Enter gst no"
        ]],

        "pan_no"=> [
            [
                "type"=> SmartConst::VALID_REQUIRED,
                "msg"=> "Please Enter pan no"
            ]
        
            ],
        "cin_no"=> [
        [
            "type"=> SmartConst::VALID_REQUIRED,
            "msg"=> "Please Enter cin no"
        ]
    
        ],
        "state"=> [
            [
                "type"=> SmartConst::VALID_REQUIRED,
                "msg"=> "Please Specify state"
            ]
        
            ],
        "pin_code"=> [
            [
                "type"=> SmartConst::VALID_REQUIRED,
                "msg"=> "Please Enter pincode"
            ]
        
            ],
        "status" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter status"
            ]
    
            ],

            "cgst" => [
                [
                    "type" => SmartConst::VALID_REQUIRED,
                    "msg" => "Please specify cgst"
                ]
        
                ],
                "igst" => [
                    [
                        "type" => SmartConst::VALID_REQUIRED,
                        "msg" => "Please specify igst"
                    ]
            
                    ],
                    "sgst" => [
                        [
                            "type" => SmartConst::VALID_REQUIRED,
                            "msg" => "Please specify sgst"
                        ]
                
                        ],
    ];


    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table:: EFLOFFICE, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::EFLOFFICE, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [],$select=[],$group_by = "", $count = false,$single=false)
    {
        $from = Table::EFLOFFICE." t1 INNER JOIN ".Table::STATEDB." t2 ON t1.state=t2.ID ";
        $select = !empty($select) ? $select : ["t1.*, t2.state_name"];
       // $order_by="last_modified_time DESC";
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }


    /**
     * 
     */
    public function getOneData($id)
    { 
        $from = Table::EFLOFFICE." t1 INNER JOIN ".Table::STATEDB." t2 ON t1.state=t2.ID ";
        $select = ["t1.*, t2.state_name"];
        $sql = "t1.ID=:ID";
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
        $from = Table::EFLOFFICE;
        $this->deleteId($from,$id);
    }
    /**
     * 
     */
    public function checkOfficeExist($office_city)
    {
        $from = Table::EFLOFFICE;
        $select = ["ID"];
        $sql = "office_city=:city";
        $data_in = ["city" => $office_city];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }
  
}