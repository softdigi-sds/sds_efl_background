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
        "total_units" => SmartConst::SCHEMA_FLOAT,
        "total_vehicles" => SmartConst::SCHEMA_INTEGER,
        "unit_amount" => SmartConst::SCHEMA_FLOAT,
        "vehicle_amount" => SmartConst::SCHEMA_FLOAT,
        "rent_amount" => SmartConst::SCHEMA_FLOAT,
        "other_one_amount" => SmartConst::SCHEMA_FLOAT,
        "other_two_amount" => SmartConst::SCHEMA_FLOAT,
        "total_others" => SmartConst::SCHEMA_FLOAT,
        "total_taxable" => SmartConst::SCHEMA_FLOAT,
        "sgst" => SmartConst::SCHEMA_FLOAT,
        "cgst" => SmartConst::SCHEMA_FLOAT,
        "igst" => SmartConst::SCHEMA_FLOAT,
        "gst_percentage" => SmartConst::SCHEMA_FLOAT,
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
    const FILE_FOLDER = "downloads";
    const FILE_NAME = "invoice.pdf";
    public static function getFullFile($id)
    {
        return self::FILE_FOLDER . DS . $id . DS . self::FILE_NAME;
    }


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
    public function getAllData($sql = "", $data_in = [], $select = [], $group_by = "", $count = false, $single = false)
    {
        $from = Table::INVOICE;
        $select = !empty($select) ? $select : ["*"];
        // $order_by="last_modified_time DESC";
        return $this->getAll($select, $from, $sql, $group_by, "", $data_in, $single, [], $count);
    }


    /**
     * 
     */
    public function getInvoiceByBillId($bill_id)
    {
        $from = Table::INVOICE . " t1 
        LEFT JOIN " . Table::VENDORS . " t2 ON t1.sd_vendor_id=t2.ID 
        LEFT JOIN " . Table::HUBS . " t3 ON t1.sd_hub_id=t3.ID ";
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
        $this->deleteId($from, $id);
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


    private function getConsumptionWithVendor($ven_id, $start_date, $end_date)
    {
        $consumption = new EflConsumptionHelper($this->db);
        return $consumption->getConsumptionInvoiceByDateVendor($ven_id, $start_date, $end_date);
    }

    private function getVehicleCountWithVendor($ven_id, $start_date, $end_date)
    {
        $consumption = new EflVehiclesHelper($this->db);
        return $consumption->getVehicleInvoiceByDateVendor($ven_id, $start_date, $end_date);
    }


    /**
     *  function get the start and end date details and update teh invoice table
     */
    public function insertInvoice($bill_id,$bill_data)
    {
       
        // get the vendors data 
        $vendorHelper = new VendorsHelper($this->db);
        $vendors = $vendorHelper->getAllData("status=5", [], ["t1.ID,t2.ID as hub_id"]);
        $start_date = $bill_data->bill_start_date;
        $end_date = $bill_data->bill_end_date;      
        // loop over the venodrs 
        $dt = [
            "total_invoices"=>0,
            "unit_amount"=>0,
            "vehicle_amount"=>0,
            "others"=>0,
            "gst_amount"=>0,
            "total_amount"=>0,
        ];      
       
        foreach ($vendors as $ven_data) {
            $_data = $this->prepareSingleVendorData($bill_id,$ven_data,$start_date,$end_date);
            if($_data["total_taxable"] > 0){
                $this->insertUpdateSingle($_data);
                $dt["unit_amount"] += $_data["unit_amount"];
                $dt["vehicle_amount"]  += $_data["vehicle_amount"];
                $dt["others"]  += $_data["total_others"];
                $dt["gst_amount"]  += $_data["gst_amount"];
                $dt["total_amount"]  += $_data["total_amount"];                             
                $dt["total_invoices"] ++;
            }
        }
        return $dt;
        // 


        // $invoice_cols = [
        //     "sd_bill_id",
        //     "sd_hub_id",
        //     "sd_vendor_id",
        //     "total_units",
        //     "total_vehicles",
        //     "unit_amount",
        //     "vehicle_amount",
        //     "status",
        //     "gst_percentage",
        //     "gst_amount",
        //     "total_amount"
        // ];
        // $consumption = new EflConsumptionHelper($this->db);
        // $vehicle = new EflVehiclesHelper($this->db);

        // $consump_data = $consumption->getConsumptionInvoiceByDate($start_date, $end_date);
        // $vehicle_data = $vehicle->getVehicleInvoiceByDate($start_date, $end_date);
        // foreach ($consump_data as $c_data) {
        //     if (isset($c_data->ID)) {
        //         $vend_rate = new VendorRateHelper($this->db);
        //         $rate = $vend_rate->getVendorRateByHubVenID($c_data->sd_hub_id, $c_data->sd_vendors_id);
        //         $units_amt = !empty($rate) ? ($c_data->count * $rate->unit_rate) : $c_data->count;
        //         $c_invoice_data = [
        //             "sd_bill_id" => $bill_id,
        //             "sd_hub_id" => $c_data->sd_hub_id,
        //             "sd_vendor_id" => $c_data->sd_vendors_id,
        //             "total_units" => ($c_data->count),
        //             "total_vehicles" => 0,
        //             "unit_amount" => $units_amt,
        //             "vehicle_amount" => 0,
        //             "status" => 5,
        //             "gst_percentage" => 0,
        //             "gst_amount" => 0,
        //             "total_amount" => $units_amt
        //         ];
        //         $this->insert($invoice_cols, $c_invoice_data);
        //     }
        // }
        // foreach ($vehicle_data as $v_data) {
        //     if (isset($v_data->ID)) {
        //         $vend_rate = new VendorRateHelper($this->db);
        //         $rate = $vend_rate->getVendorRateByHubVenID($v_data->sd_hub_id, $v_data->sd_vendors_id);
        //         $vehicle_amt = !empty($rate) ? ($v_data->count * $rate->parking_rate_vehicle) : $v_data->count;
        //         $exist_data = $this->checkInvoiceExistByBillHubAndVenID($bill_id, $v_data->sd_hub_id, $v_data->sd_vendors_id);
        //         if (isset($exist_data->ID)) {
        //             $vi_invoice_data = ["total_vehicles" => $v_data->count, "vehicle_amount" => $vehicle_amt, "total_amount" => ($vehicle_amt + $exist_data->unit_amount)];
        //             $this->update(["total_vehicles", "vehicle_amount", "total_amount"], $vi_invoice_data, $exist_data->ID);
        //         } else {
        //             $vu_invoice_data = [
        //                 "sd_bill_id" => $bill_id,
        //                 "sd_hub_id" => $v_data->sd_hub_id,
        //                 "sd_vendor_id" => $v_data->sd_vendors_id,
        //                 "total_units" => 0,
        //                 "total_vehicles" => $v_data->count,
        //                 "unit_amount" => 0,
        //                 "vehicle_amount" => $vehicle_amt,
        //                 "status" => 5,
        //                 "gst_percentage" => 0,
        //                 "gst_amount" => 0,
        //                 "total_amount" => $vehicle_amt
        //             ];
        //             $this->insert($invoice_cols, $vu_invoice_data);
        //         }
        //     }
        // }
    }

    public function prepareSingleVendorData($bill_id, $ven_data, $start_date, $end_date)
    {
        $_data = [];
        $_data["sd_bill_id"] = $bill_id;
        $_data["sd_hub_id"] = $ven_data->hub_id;
        $_data["sd_vendor_id"] = $ven_data->ID;
        // get consumption with dates 
        $_data["total_units"] = $this->getConsumptionWithVendor($ven_data->ID, $start_date, $end_date);
        // get vehicle count with dates
        $_data["total_vehicles"] = $this->getVehicleCountWithVendor($ven_data->ID, $start_date, $end_date);
        // calculate amount now 
        $_data["unit_amount"] = $_data["total_units"]  * 10;
        // calculate vehicle amount now 
        $_data["vehicle_amount"] = $_data["total_vehicles"]  * 100;
        // rent amount
        $_data["rent_amount"] = 100;
        //
        $_data["other_one_amount"] = 100;
        //
        $_data["other_two_amount"] = 100;
        //
        $_data["total_others"] =   $_data["rent_amount"]  +   $_data["other_one_amount"] +  $_data["other_two_amount"];
        //
        $_data["total_taxable"] = $_data["unit_amount"] +  $_data["vehicle_amount"] +  $_data["total_others"];
        //
        $_data["sgst"] = 100;
        $_data["cgst"] = 100;
        $_data["igst"] = 100;
        $_data["gst_percentage"] = 100;
        $_data["gst_amount"] = 100;
        $_data["total_amount"] =  $_data["total_taxable"]  +   $_data["gst_amount"];
        return $_data;
    }


    /**
     * 
     */
    public function checkInvoiceExists($bill_id, $vendor_id)
    {
        $sql = " sd_bill_id=:bill_id AND sd_vendor_id=:vend_id";
        $data_in = ["bill_id" => $bill_id, "vend_id" => $vendor_id];
        $data = $this->getAll(["*"], TABLE::INVOICE, $sql, "", "", $data_in, true, []);
        return $data;
    }
    /**
     * 
     */
    public function insertUpdateSingle($data)
    {
        $exist_data = $this->checkInvoiceExists($data["sd_bill_id"], $data["sd_vendor_id"]);
        if (isset($exist_data->ID)) {
            $update_columns = [
                "total_units",
                "total_vehicles",
                "unit_amount",
                "vehicle_amount",
                "status",
                "gst_percentage",
                "gst_amount",
                "total_amount"
            ];
            $this->update($update_columns, $data, $exist_data->ID);
            return $exist_data->ID;
        } else {
            $insert_columns = [
                "sd_bill_id",
                "sd_hub_id",
                "sd_vendor_id",
                "total_units",
                "total_vehicles",
                "unit_amount",
                "vehicle_amount",
                "status",
                "gst_percentage",
                "gst_amount",
                "total_amount"
            ];
            return $this->insert($insert_columns, $data);
        }
    }
    
}
