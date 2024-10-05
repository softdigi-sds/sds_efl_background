<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Helpers;

use Core\BaseHelper;
use Core\Helpers\SmartConst;
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
    public function insertInvoice($bill_id, $bill_data)
    {

        // get the vendors data 
        $vendorHelper = new VendorsHelper($this->db);
        $vendors = $vendorHelper->getAllData(
            "status=5",
            [],
            ["t1.ID,t2.ID as hub_id,t3.short_name"]
        );
        //var_dump($vendors);
        //exit();
        $start_date = $bill_data->bill_start_date;
        $end_date = $bill_data->bill_end_date;
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
            $_data = $this->prepareSingleVendorData($bill_id, $ven_data, $start_date, $end_date);
            // var_dump($_data);
            // exit();
            if ($_data["total_taxable"] > 0) {
                $this->insertUpdateSingle($_data);
                $dt["unit_amount"] += $_data["unit_amount"];
                $dt["vehicle_amount"]  += $_data["vehicle_amount"];
                $dt["others"]  += $_data["total_others"];
                $dt["gst_amount"]  += $_data["gst_amount"];
                $dt["total_amount"]  += $_data["total_amount"];
                $dt["total_invoices"]++;
            }
        }
        return $dt;
    }
    public function getVehicleParkingCharge($rates, $vehicle_count)
    {
        $charge = 0;
        foreach ($rates as $obj) {
            // var_dump($obj->sd_hsn_id["value"]);
            // only parking and charging this condition is
            if ($obj->sd_hsn_id["value"] == 1) {
                // this is only for praking so check vehicle count range and apply
                if ($obj->rate_type["value"] == 1) {
                    $charge = $charge + ($vehicle_count * $obj->price);
                } else if ($obj->rate_type["value"] == 2) {
                    // minimum type
                    if ($vehicle_count >= $obj->min_start && $vehicle_count < $obj->min_end) {
                        $total_count = ($vehicle_count - $obj->min_start + 1);
                        // echo " v " . $vehicle_count . " s =" . $obj->min_start . " p " . $obj->price . " t" .  $total_count;
                        $charge = $charge + ($total_count  * $obj->price);
                        //echo "charge " . $charge;
                    }
                }
            } else if ($obj->sd_hsn_id["value"] == 2) {
                // this is only for parking so check vehicle count range and apply
                // echo " value " . $obj->rate_type["value"] . " <br/>";
                if ($obj->rate_type["value"] == 1) {
                    $charge = $charge + ($vehicle_count * $obj->price);
                } else if ($obj->rate_type["value"] == 2) {
                    // minimum type
                    if ($vehicle_count >= $obj->min_start && $vehicle_count < $obj->min_end) {
                        $total_count = ($vehicle_count - $obj->min_start + 1);
                        $charge = $charge + ($total_count  * $obj->price);
                    }
                }
            }
        }
        // echo "charge = " . $charge . "<br/>";
        return $charge;
    }

    public function getVehicleChargingCharge($rates, $vehicle_count, $units)
    {
        $charge = 0;
        foreach ($rates as $obj) {
            if ($obj->sd_hsn_id["value"] == 1) {
                // this is only for praking so check vehicle count range and apply
                $minimum_units = ($obj->min_units_vehicle * 30) * $vehicle_count;
                $total_units = 0;
                if ($units > $minimum_units) {
                    $total_units = $units - $minimum_units;
                }
                if ($obj->rate_type["value"] == 2) {
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
        return $charge;
    }


    public function getVendorRateValues($hub_id, $vendor_id,  $vehicle_count, $unit_count, $end_date)
    {
        $rateHelper = new VendorRateHelper($this->db);
        $rateSubHelper = new VendorRateSubHelper($this->db);
        $date = SmartGeneral::getCurrentDbDate();
        $rateHelper = $rateHelper->getVendorHubDetails($hub_id, $vendor_id, $end_date);
        // var_dump($rateHelper);
        $parking_charges = 0;
        $units_charge = 0;
        if (isset($rateHelper->ID)) {
            $vendor_rates = $rateSubHelper->getAllByVendorRateId($rateHelper->ID);
            // var_dump($vendor_rates);
            $parking_charges = $this->getVehicleParkingCharge(
                $vendor_rates,
                $vehicle_count
            );
            $units_charge = $this->getVehicleChargingCharge($vendor_rates, $vehicle_count, $unit_count);
        }
        return [$parking_charges, $units_charge];
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
        list($parking_amount, $unit_amount) = $this->getVendorRateValues(
            $ven_data->hub_id,
            $ven_data->ID,
            $_data["total_vehicles"],
            $_data["total_units"],
            $end_date
        );
        $_data["unit_amount"] = $unit_amount;
        // calculate vehicle amount now 
        $_data["vehicle_amount"] = $parking_amount;
        // rent amount
        $_data["rent_amount"] = 0;
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
                "total_amount",
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
            "ack_no" => "GST NUMBER",
            "ack_date" => "2023-04-01"
        ];
        $html = InvoicePdf::getHtml($data);
        // echo $html;
        $path = "invoice" . DS . $id . DS . "invoice.pdf";
        SmartPdfHelper::genPdf($html, $path);
    }
}
