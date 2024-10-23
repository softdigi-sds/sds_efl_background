<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Helpers;

use Core\BaseHelper;
use Core\Helpers\SmartConst;
use Core\Helpers\SmartDateHelper;
use Core\Helpers\SmartGeneral;
use Core\Helpers\SmartPdfHelper;
//
use Site\Helpers\TableHelper as Table;
use Site\View\InvoicePdf;

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
        "invoice_fin_year" => SmartConst::SCHEMA_VARCHAR,
        "invoice_serial_number" => SmartConst::SCHEMA_VARCHAR,
        "invoice_number" => SmartConst::SCHEMA_VARCHAR,
        "total_units" => SmartConst::SCHEMA_FLOAT,
        "total_vehicles" => SmartConst::SCHEMA_FLOAT,
        "unit_amount" => SmartConst::SCHEMA_FLOAT,
        "vehicle_amount" => SmartConst::SCHEMA_FLOAT,
        "rent_amount" => SmartConst::SCHEMA_FLOAT,
        "charge_per_month"=> SmartConst::SCHEMA_FLOAT,
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
        "irn_number" => SmartConst::SCHEMA_VARCHAR,
        "signed_qr_code" => SmartConst::SCHEMA_TEXT,
        "ack_no" => SmartConst::SCHEMA_VARCHAR,
        "ack_date" => SmartConst::SCHEMA_DATE,
        "signed_invoice" => SmartConst::SCHEMA_TEXT,
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
    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::INVOICE . " t1 INNER JOIN ".Table::BILL." t2 ON t2.ID=t1.sd_bill_id";
        $select = ["t1.*,t2.bill_start_date,t2.bill_end_date"];
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
    public function insertInvoice($bill_id, $bill_data)
    {

        // get the vendors data 
        $vendorHelper = new VendorsHelper($this->db);
        $vendors = $vendorHelper->getAllData(
            "t1.status=5",
            [],
            ["t1.ID,t2.ID as hub_id,t3.short_name"]
        );
        //var_dump($vendors);
        //exit();
        $start_date = $bill_data->bill_start_date;
        $end_date = $bill_data->bill_end_date;
        $dates = SmartDateHelper::getDatesBetween($start_date,$end_date);
        $date_count = count($dates) > 0 ? count($dates) : 0;
        // loop over the venodrs 
        $dt = [
            "total_invoices" => 0,
            "unit_amount" => 0,
            "vehicle_amount" => 0,
            "others" => 0,
            "gst_amount" => 0,
            "total_amount" => 0,
        ];

        foreach ($vendors as $ven_data) {
           // if ($ven_data->ID == 578) {
                $_data = $this->prepareSingleVendorData($bill_id, $ven_data, $start_date, $end_date,  $date_count);
               // var_dump($_data);
                if ($_data["total_taxable"] > 0) {                  
                    $this->insertUpdateSingle($_data);
                    $dt["unit_amount"] += $_data["unit_amount"];
                    $dt["vehicle_amount"]  += $_data["vehicle_amount"];
                    $dt["others"]  += $_data["total_others"];
                    $dt["gst_amount"]  += $_data["gst_amount"];
                    $dt["total_amount"]  += $_data["total_amount"];
                    $dt["total_invoices"]++;
                }else{
                    $this->updateInvoiceDataNew($_data);
                }
            //}
        }
        //exit();
        return $dt;
    }
    public function getVehicleParkingCharge($rates, $vehicle_count)
    {
        $charge = 0;
        $parking_rate = 0;
        foreach ($rates as $obj) {
            // var_dump($obj->sd_hsn_id["value"]);
            // only parking and charging this condition is
            if ($obj->sd_hsn_id["value"] == 1) {
                // this is only for praking so check vehicle count range and apply
                if ($obj->rate_type["value"] == 1) {
                    $charge = $charge + ($vehicle_count * $obj->price);
                    $parking_rate =  $obj->price;
                } else if ($obj->rate_type["value"] == 2) {
                    // minimum type
                    if ($vehicle_count >= $obj->min_start && $vehicle_count < $obj->min_end) {
                        $total_count = ($vehicle_count - $obj->min_start + 1);
                        // echo " v " . $vehicle_count . " s =" . $obj->min_start . " p " . $obj->price . " t" .  $total_count;
                        $charge = $charge + ($total_count  * $obj->price);
                        //echo "charge " . $charge;
                        $parking_rate =  $obj->price;
                    }
                }
            } else if ($obj->sd_hsn_id["value"] == 2) {
                // this is only for parking so check vehicle count range and apply
                // echo " value " . $obj->rate_type["value"] . " <br/>";
                if ($obj->rate_type["value"] == 1) {
                    $charge = $charge + ($vehicle_count * $obj->price);
                    $parking_rate =  $obj->price;
                } else if ($obj->rate_type["value"] == 2) {
                    // minimum type
                    if ($vehicle_count >= $obj->min_start && $vehicle_count < $obj->min_end) {
                        $total_count = ($vehicle_count - $obj->min_start + 1);
                        $charge = $charge + ($total_count  * $obj->price);
                        $parking_rate =  $obj->price;
                    }
                }
            }
        }
        // echo "charge = " . $charge . "<br/>";
        return [$charge, $parking_rate];
    }

    public function getVehicleChargingCharge($rates, $vehicle_count, $units)
    {
        $charge = 0;
        $min_units = 0;
        $extra_units = 0;
        $extra_price = 0;
        $allowed_units = 0;
        foreach ($rates as $obj) {
            if ($obj->sd_hsn_id["value"] == 1 && $vehicle_count > 0) {
                $min_units = $obj->min_units_vehicle;
                // this is only for praking so check vehicle count range and apply
                $allowed_units  = $minimum_units = ($obj->min_units_vehicle) * $vehicle_count;
                $total_units = 0;
                if ($units > $minimum_units) {
                    $extra_units = $total_units = $units - $minimum_units;
                }
                if ($obj->rate_type["value"] == 2) {
                    $extra_price =  $obj->extra_price;
                    $charge = ($total_units * $obj->extra_price);
                }
            } else if ($obj->sd_hsn_id["value"] == 3) {
                // this is only for charging 
                //echo " value = " . $obj->sd_hsn_id["value"] . " units " . $units;
                if ($obj->rate_type["value"] == 1) {
                    $charge = ($units * $obj->price);
                }
            }
        }
       // echo " <br/><br/> unit charge " . $charge;
        return [$charge, $min_units, $extra_units,  $allowed_units , $extra_price ];
    }


    public function getVehicleRentCharge($rates)
    {
        $charge = 0;
        foreach ($rates as $obj) {          
            if ($obj->sd_hsn_id["value"] == 4) {
               $charge = $obj->price;
            } 
        }
        return $charge;
    }



    public function getVendorRateValues($hub_id, $vendor_id,  $vehicle_count, $unit_count, $end_date)
    {
        $rateHelper = new VendorRateHelper($this->db);
        $rateSubHelper = new VendorRateSubHelper($this->db);
      //  $date = SmartGeneral::getCurrentDbDate();
        $rateHelper = $rateHelper->getVendorHubDetails($hub_id, $vendor_id, $end_date);
        // var_dump($rateHelper);
        $parking_charges = 0;
        $units_charge = 0;
        $rent_charges = 0;
        $min_units = 0;
        $extra_units = 0;
        $allowed_units = 0;
        $extra_price = 0;
        $charge_per_month =0;
        if (isset($rateHelper->ID)) {
            $vendor_rates = $rateSubHelper->getAllByVendorRateId($rateHelper->ID);
           //  var_dump($vendor_rates);
            list($parking_charges,$charge_per_month) = $this->getVehicleParkingCharge(
                $vendor_rates,
                $vehicle_count
            );
            list($units_charge, $min_units, $extra_units,  $allowed_units , $extra_price) = $this->getVehicleChargingCharge($vendor_rates, $vehicle_count, $unit_count);
            //
            $rent_charges = $this->getVehicleRentCharge($vendor_rates);
        }
        return [$parking_charges, $units_charge,$rent_charges,$min_units, $extra_units,  $allowed_units , $extra_price,$charge_per_month];
    }


    public function prepareSingleVendorData($bill_id, $ven_data, $start_date, $end_date,  $date_count)
    {
        $_data = [];
        $_data["sd_bill_id"] = $bill_id;
        $_data["sd_hub_id"] = $ven_data->hub_id;
        $_data["sd_vendor_id"] = $ven_data->ID;
        // get consumption with dates     
        $_data["total_units"] =  $this->getConsumptionWithVendor($ven_data->ID, $start_date, $end_date);
        // get vehicle count with dates
        $total_vehicles = $this->getVehicleCountWithVendor($ven_data->ID, $start_date, $end_date);
        $_data["total_vehicles"] =  round(  $total_vehicles  / $date_count,3);
        // calculate amount now 
        list($parking_amount, $unit_amount,$rent_amount,$min_units, $extra_units,  $allowed_units , $extra_price,$charge_per_month) = $this->getVendorRateValues(
            $ven_data->hub_id,
            $ven_data->ID,
            $_data["total_vehicles"],
            $_data["total_units"],
            $end_date
        );
        $_data["min_units_vehicle"] = $min_units;
        $_data["units_allowed"] =  $allowed_units;
        $_data["unit_amount"] = $unit_amount;
        $_data["extra_units"] = $extra_units;
        $_data["extra_price"] = $extra_price;
        $_data["charge_per_month"] = $charge_per_month;
        // calculate vehicle amount now 
        $_data["vehicle_amount"] = $parking_amount;
        // rent amount
        $_data["rent_amount"] = $rent_amount;
        //
        $_data["other_one_amount"] = 0;
        //
        $_data["other_two_amount"] = 0;
        //
        $_data["total_others"] =   $_data["rent_amount"]  +   $_data["other_one_amount"] +  $_data["other_two_amount"];
        //
        $_data["total_taxable"] = $_data["unit_amount"] +  $_data["vehicle_amount"] +  $_data["total_others"];
        //
        $_data["sgst"] = 9;
        $_data["cgst"] = 9;
        $_data["igst"] = 18;
        $_data["gst_percentage"] = 18;
        $_data["gst_amount"] = $_data["total_taxable"] * ($_data["gst_percentage"] / 100);
        $_data["total_amount"] =  $_data["total_taxable"]  +   $_data["gst_amount"];
        $_data["short_name"] = $ven_data->short_name;
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

    public function getInvoiceId($bill_id, $invoice_number)
    {
        $sql = " sd_bill_id=:bill_id AND invoice_number=:invoice_number";
        $data_in = ["bill_id" => $bill_id, "invoice_number" => $invoice_number];
        $data = $this->getAll(["*"], TABLE::INVOICE, $sql, "", "", $data_in, true, []);
        return isset($data->ID) ? $data->ID : 0;
    }

    public function updateInvoiceData($id, $_data)
    {
        $columns = array_keys($_data);
        $this->update($columns, $_data, $id);
    }

    public function updateInvoiceDataNew($data){
        $exist_data = $this->checkInvoiceExists($data["sd_bill_id"], $data["sd_vendor_id"]);
        if (isset($exist_data->ID)) {
            $update_columns = [
                "total_units",
                "total_vehicles",
                "rent_amount",
                "unit_amount",
                "vehicle_amount",
                "status",
                "gst_percentage",
                "gst_amount",
                "total_amount",
                "min_units_vehicle",
                "units_allowed",
                "extra_units",
                "extra_price",
                "charge_per_month"
            ];
            $this->update($update_columns, $data, $exist_data->ID);
            return $exist_data->ID;
        }
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
                "rent_amount",
                "unit_amount",
                "vehicle_amount",
                "status",
                "gst_percentage",
                "gst_amount",
                "total_amount",
                "min_units_vehicle",
                "units_allowed",
                "extra_units",
                "extra_price",
                "charge_per_month"
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
                "rent_amount",
                "total_taxable",
                "status",
                "gst_percentage",
                "gst_amount",
                "total_amount",
                "min_units_vehicle",
                "units_allowed",
                "extra_units",
                "extra_price",
                "charge_per_month"
            ];
            $id = $this->insert($insert_columns, $data);
            $up_columns = [
                "invoice_fin_year",
                "invoice_serial_number",
                "invoice_number",
            ];
            $fin_year = "24-25";
            $invoice_number = "EFL/" . $data["short_name"] . DS . $id . "/24-25";
            $up_data = [
                "invoice_fin_year" => $fin_year,
                "invoice_serial_number" => $id,
                "invoice_number" => $invoice_number,
            ];
            $this->update($up_columns, $up_data, $id);
        }
    }


    public function generateInvoicePdf($id)
    {
        $data = [
            'address' => 'ADDRESS',
            "ack_no" => "GST NUMBER",
            "ack_date" => "2023-04-01",
            'irn_no' => 'IRN NUMBER',
            'additional_info' => 'IRN NUMBER',
            'invoice_number' => 'INVOICE NUMBER',
            'invoice_date' => '10/09/2024',
            'date_of_supply' => '10/09/2024',
            'items' => [
                [
                    'sl_no' => "SERAIL NUMBER",
                    'description' => 'GOODS/SERVICES',
                    'hsn_code' => 'HSN CODE',
                    'quantity' => 'QUANTITY',
                    'unit' => 'NOS',
                    'unit_price' => 'UNIT PRICE',
                    'taxable_amount' => 'TAXABLE AMT',
                    'tax_details' => 'TAX DETAILS',
                    'tcs' => '0.0',
                    'total' => 'TOTAL'
                ],


            ],
            'itemamt' => [
                [
                    'tax_amt'  => '82340.35',
                    'cgst_amt' => '0.00',
                    'sgst_amt' => '0.00',
                    'igst_amt' => '14821.26',
                    'cees_amt' => '0.00 ',
                    'state_cees' => '0.00',
                    'roundoff_amt' => '0',
                    'other_charge' => '0.00',
                    'total_inv_amt' => '97161.61',
                ],


            ],

        ];
        $html = InvoicePdf::getHtml($data);
        //    $html = '<p>hello </p>';
        // echo $html;
        $path = "invoice" . DS . $id . DS . "invoice.pdf";
        SmartPdfHelper::genPdf($html, $path);
    }

    private function getOneDayCount($_data,$date){
        foreach($_data as $obj){
            if($obj->date==$date){
                return $obj->count;
                break;
            }
        }
        return 0;
    }


    public function getOneDetails($id){
        $_data = $this->getOneData($id);
        //var_dump($_data);
        // with the dates and vendor id get the counts data
        $vehicle_obj = new EflVehiclesHelper($this->db);
        $_vehicle_count_data = $vehicle_obj->getVehicleInvoiceByDateVendor($_data->sd_vendor_id, $_data->bill_start_date,$_data->bill_end_date,false);
       // var_dump($_vehicle_count_data);
        $dates = SmartDateHelper::getDatesBetween( $_data->bill_start_date,$_data->bill_end_date);
        $days_count = count($dates) > 0 ? count($dates): 1 ;
        //echo "count days " . $days_count;
        $sub_data = [];
        foreach($dates as $date){
            $day_count = $this->getOneDayCount( $_vehicle_count_data,$date);
            $day_value = $_data->charge_per_month / $days_count;
            $_sub_data = [
                "date"=>$date,
                "count"=>$day_count,
                "charge_month"=>$_data->charge_per_month,
                "charge_per_day"=>round($day_value,2) ,
                "total"=>round($day_count * $day_value),
            ];
            $sub_data[] = $_sub_data;
        }
        $_data_out = (array)$_data;
        $_data_out["avg_vehicles"] =$_data_out["total_vehicles"];
        $_data_out["sub_data"] = $sub_data;
        return $_data_out; 
    }

}
