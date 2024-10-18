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
class VendorsHelper extends BaseHelper
{

    const schema = [
        "sd_hub_id" => SmartConst::SCHEMA_INTEGER,
        "vendor_code" => SmartConst::SCHEMA_VARCHAR,
        "vendor_company" => SmartConst::SCHEMA_VARCHAR,
        "vendor_name" => SmartConst::SCHEMA_VARCHAR,
        "billing_to"=> SmartConst::SCHEMA_VARCHAR,
        "gst_no" => SmartConst::SCHEMA_VARCHAR,
        "pan_no" => SmartConst::SCHEMA_VARCHAR,
        "address_one" => SmartConst::SCHEMA_VARCHAR,        
        "address_two" => SmartConst::SCHEMA_VARCHAR,
        "state_name" => SmartConst::SCHEMA_VARCHAR,
        "pin_code" => SmartConst::SCHEMA_VARCHAR,
        "status" => SmartConst::SCHEMA_INTEGER,
        "created_by" => SmartConst::SCHEMA_CUSER_ID,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
        "last_modified_by" => SmartConst::SCHEMA_CUSER_ID,
        "last_modified_time" => SmartConst::SCHEMA_CDATETIME
    ];
    /**
     * 
     */
    const validations = [
        "sd_hub_id" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Hub id"
            ]
        ],
        "vendor_code" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Vendor code"
            ]
        ],
        "vendor_company" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Vendor Company"
            ]
    
        ],
        "gst_no" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter GST Number"
            ]
    
            ],
        "pin_code" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Pin Code"
            ]
    
            ],
        "status" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter status"
            ]
    
            ],
            "billing_to" => [
                [
                    "type" => SmartConst::VALID_REQUIRED,
                    "msg" => "Please Enter billing to"
                ]
        
            ]
    ];

    /**
     * 
     */
    public function  insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::VENDORS, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::VENDORS, $columns, $data, $id);
    }
    /**
     * 
     */
    // public function getAllData($sql = "", $data_in = [],$select=[],$group_by = "", $count = false,$single=false)
    // {
    //     $from = Table::VENDORS." t1 LEFT JOIN ".Table::HUBS." t2 ON t1.sd_hub_id=t2.ID LEFT JOIN ".Table::STATEDB." t3 ON t1.state_name=t3.ID ";
    //     $select = !empty($select) ? $select : ["t1.*, t2.hub_id, t2.hub_name, t3.state_name "];
    //    // $order_by="last_modified_time DESC";
    //     return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    // }
    public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    {
        // Define the tables and joins
        $from = Table::VENDORS . " t1 LEFT JOIN " . Table::HUBS . " t2 ON t1.sd_hub_id = t2.ID LEFT JOIN " . Table::STATEDB . " t3 ON t1.state_name = t3.ID";
    
        // Define the default selection if not provided
        $select = !empty($select) ? $select : ["t1.*, t2.hub_id, t2.hub_name, t3.state_name"];
    
        // Check if there's a condition in $sql
        if (!empty($sql)) {
            // Add WHERE clause only if a condition is present
            $from .= " WHERE " . $sql;
        }
    
        // Execute the query and return the result
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }
    

  

    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::VENDORS." t1 LEFT JOIN ".Table::HUBS." t2 ON t1.sd_hub_id=t2.ID  LEFT JOIN ".Table::STATEDB." t3 ON t1.state_name=t3.ID ";
        $select = ["t1.*, t2.ID AS hub_value, t2.hub_id, t3.ID AS state_value, t3.state_name "];
        $sql = "t1.ID=:ID";
        $data_in = ["ID" => $id];
            $data = $this->getAll($select,  $from, $sql, "", "", $data_in, true, []);
            if(isset($data->ID)){
                $sd_hub_id = $data->sd_hub_id;
                $data->sd_hub_id = ["value"=>$sd_hub_id,"label"=> $data->hub_id];
                $data->state_name = ["value"=>$data->state_value,"label"=> $data->state_name];
            }
            return $data;
    }
     /**
     * 
     */
    public function deleteOneId($id)
    {
        $from = Table::VENDORS;
        $this->deleteId($from,$id);
    }

     /**
     * 
     */
    public function checkVendorByCodeCompany($code, $company)
    {
        $from = Table::VENDORS;
        $select = ["ID,sd_hub_id"];
        $sql = "vendor_code=:code OR vendor_company=:company";
        $data_in = ["code" => $code, "company" => $company];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }

    public function checkVendorByCodeCompanyWithHub($code, $company,$hub_name)
    {
        $from = Table::VENDORS . " t1 INNER JOIN ".Table::HUBS." t2 ON t2.ID = t1.sd_hub_id";
        $select = ["t1.ID,t1.sd_hub_id"];
        $sql = "t2.hub_id=:hub_name AND (t1.vendor_code=:code OR t1.vendor_company=:company)";
        $data_in = ["code" => $code, "company" => $company,"hub_name"=>$hub_name];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }


    public function getVendorsByHubId($id)
    {
        $from = Table::VENDORS;
        $select = ["ID,sd_hub_id,vendor_code,vendor_company,vendor_name"];
        $sql = "sd_hub_id=:id";
        $data_in = ["id" => $id,];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, false, []);
        return $data;
    }


    public function getVendorCompany($vendor_company)
    {
        $from = Table::VENDORS;
        $select = ["ID"];
        $sql = "vendor_company = :vendor_company"; 
        $data_in = ["vendor_company" => $vendor_company]; 
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return !empty($data) ? intval($data->ID) : 0;
    }
    public function insertUpdateNew($_data){
        $insert_columns = ["sd_hub_id", "vendor_code", "vendor_company", "vendor_name", "billing_to", "gst_no", "pan_no", "address_one", "address_two", "state_name", "pin_code", "status", "created_by", "created_time"];
        $update_columns = [ "last_modified_by", "last_modified_time"];
        $exist_data = $this->checkExists($_data["sd_hub_id"], $_data["vendor_code"]);
        if (isset($exist_data->ID)) {
            $this->update(  $update_columns, $_data, $exist_data->ID);
        } else {
            $this->insert($insert_columns , $_data);
        }
    }


    public function checkExists($vendor_code, $hub_id)
    {
        $sql = "vendor_code=:vendor_code AND sd_hub_id=:hub_id ";
        $data_in = [ "vendor_code" =>$vendor_code, "hub_id" =>$hub_id];
        $exist_data = $this->getAllData($sql, $data_in, ["t1.ID"], "", false, true);
        return $exist_data;
    }
}
