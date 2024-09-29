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
        "vehicle_amount"=> SmartConst::SCHEMA_FLOAT,
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
            ]
        ],
        "total_units" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter total_units"
            ]
        ],
        "total_vehicles" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter total vehicals"
            ]
        ],
        "unit_amount" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter unit amount"
            ]
        ],
        "vehicle_amount" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter vechical amount"
            ]
        ],
        "gst_percentage" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter gst percentage"
            ]
        ],
        "gst_amount" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter gst amount"
            ]
        ],
        "total_amount" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter total amount"
            ]
        ],

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
    public function getInvoiceByBillId($bill_id)
    {
        $from = Table::INVOICE." t1 LEFT JOIN ".Table::VENDORS." t2 ON t1.sd_vendor_id=t2.ID LEFT JOIN ".Table::HUBS." t3 ON t1.sd_hub_id=t3.ID " ;
        $select = ["t1.*, t2.vendor_company, t3.hub_id "];
        $sql = "t1.sd_bill_id=:bill_id";
        $data_in = ["bill_id" => $bill_id];
        $group_by = "";
        $order_by = "";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, false, []);
        return $data;
    }
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
  
    public function checkInvoiceExistByBillHubAndVenID($bill_id, $hub_id, $vend_id)
    {
        $from = Table::INVOICE;
        $select = ["ID, unit_amount"];
        $sql = " sd_bill_id=:bill_id AND sd_hub_id=:hub_id AND sd_vendor_id=:vend_id";
        $data_in = ["bill_id" => $bill_id, "hub_id" => $hub_id, "vend_id" => $vend_id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }

    public function insertInvoice($bill_id, $start_date, $end_date, $invoice_cols){
        $consumption = new EflConsumptionHelper($this->db);
        $vehicle = new EflVehiclesHelper($this->db);
        $consump_data = $consumption->getConsumptionInvoiceByDate($start_date, $end_date);
        $vehicle_data = $vehicle->getVehicleInvoiceByDate($start_date, $end_date);
        foreach($consump_data as $c_data){
            if(isset($c_data->ID)){
                $vend_rate = new VendorRateHelper($this->db);
                $rate = $vend_rate->getVendorRateByHubVenID($c_data->sd_hub_id, $c_data->sd_vendors_id);
                $units_amt = !empty($rate) ? ($c_data->count * $rate->unit_rate) : $c_data->count;
                $c_invoice_data = ["sd_bill_id" => $bill_id, "sd_hub_id" => $c_data->sd_hub_id,
                                "sd_vendor_id" => $c_data->sd_vendors_id, "total_units" => ($c_data->count),
                                "total_vehicles" => 0, "unit_amount" => $units_amt, "vehicle_amount" => 0,
                                "status" => 5, "gst_percentage" => 0, "gst_amount" => 0, "total_amount" => $units_amt];
                 $this->insert($invoice_cols, $c_invoice_data);

            }
        }
        foreach($vehicle_data as $v_data){
            if(isset($v_data->ID)){
                $vend_rate = new VendorRateHelper($this->db);
                $rate = $vend_rate->getVendorRateByHubVenID($v_data->sd_hub_id, $v_data->sd_vendors_id);
                $vehicle_amt = !empty($rate) ? ($v_data->count * $rate->parking_rate_vehicle) : $v_data->count;
                $exist_data = $this->checkInvoiceExistByBillHubAndVenID($bill_id, $v_data->sd_hub_id, $v_data->sd_vendors_id);
                if(isset($exist_data->ID)){
                    $vi_invoice_data = ["total_vehicles" => $v_data->count, "vehicle_amount" => $vehicle_amt, "total_amount" => ($vehicle_amt + $exist_data->unit_amount)];
                 $this->update(["total_vehicles","vehicle_amount", "total_amount"], $vi_invoice_data, $exist_data->ID);

                }else{
                    $vu_invoice_data = ["sd_bill_id" => $bill_id, "sd_hub_id" => $v_data->sd_hub_id,
                                    "sd_vendor_id" => $v_data->sd_vendors_id, "total_units" => 0,
                                    "total_vehicles" => $v_data->count, "unit_amount" => 0,
                                    "vehicle_amount" => $vehicle_amt, "status" => 5, "gst_percentage" => 0,
                                     "gst_amount" => 0, "total_amount" => $vehicle_amt];
                     $this->insert($invoice_cols, $vu_invoice_data);
                }
              

            }
        }

    }
}