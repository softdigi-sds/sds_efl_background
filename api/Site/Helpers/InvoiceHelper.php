<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Helpers;

use Core\BaseHelper;
use Core\Helpers\SdDigiHelper;
use Core\Helpers\SmartConst;
use Core\Helpers\SmartCurl;
use Core\Helpers\SmartData;
use Core\Helpers\SmartDateHelper;
use Core\Helpers\SmartFileHelper;
use Core\Helpers\SmartGeneral;
use Core\Helpers\SmartPdfHelper;
use Core\Helpers\SmartQrCodeHelper;
//
use Site\Helpers\TableHelper as Table;
use Site\view\InvoicePdf;
use Site\view\VehiclesPdf;
use stdClass;

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
        "charge_per_month" => SmartConst::SCHEMA_FLOAT,
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
        "sign_token" => SmartConst::SCHEMA_TEXT,
        "invoice_date" => SmartConst::SCHEMA_CDATE,
        "signed_time" => SmartConst::SCHEMA_CDATETIME,
        "signed_by" => SmartConst::SCHEMA_CUSER_ID,
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


    public function getAllReport($sql, $data_in)
    {
        $from = Table::INVOICE . " t1 
        LEFT JOIN " . Table::SD_CUSTOMER . " t2 ON t1.sd_customer_id=t2.ID 
        LEFT JOIN " . Table::HUBS . " t3 ON t1.sd_hub_id=t3.ID ";
        $select = ["t1.*, t2.vendor_company, t3.hub_id "];
        $select[] = "(SELECT SUM(t20.payment_amount) FROM " . Table::SD_PAYMENT . " t20 WHERE t20.sd_invoice_id=t1.ID) as paid";
        // $sql = "t1.sd_customer_id=:sd_customer_id";
        // $data_in = ["sd_customer_id" => $_id];
        $group_by = "";
        $order_by = "";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, false, []);
        return $data;
    }

    public function getInvoiceByCustomerId($_id)
    {
        $from = Table::INVOICE . " t1 
        LEFT JOIN " . Table::SD_CUSTOMER . " t2 ON t1.sd_customer_id=t2.ID 
        LEFT JOIN " . Table::HUBS . " t3 ON t1.sd_hub_id=t3.ID ";
        $select = ["t1.*, t2.vendor_company, t3.hub_id "];
        $sql = "t1.sd_customer_id=:sd_customer_id";
        $data_in = ["sd_customer_id" => $_id];
        $group_by = "";
        $order_by = "";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, false, []);
        return $data;
    }

    public function getInvoiceReportByCustomer($_id)
    {
        $from = Table::INVOICE . " t1";
        $select = ["t1.invoice_date as date,t1.invoice_number as ref_no,t1.total_amount as amount,'1' as status"];
        $sql = "t1.sd_customer_id=:sd_customer_id";
        $data_in = ["sd_customer_id" => $_id];
        $group_by = "";
        $order_by = "t1.ID DESC";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, false, []);
        return $data;
    }

    /**
     * 
     */
    public function getInvoiceByBillId($bill_id)
    {
        $from = Table::INVOICE . " t1 
        LEFT JOIN " . Table::SD_CUSTOMER . " t2 ON t1.sd_customer_id=t2.ID 
        LEFT JOIN " . Table::HUBS . " t3 ON t1.sd_hub_id=t3.ID 
        LEFT JOIN " . Table::EFLOFFICE . " t12 ON t3.sd_efl_office_id=t12.ID
        ";
        $select = ["t1.*, t2.vendor_company, t3.hub_id ", "t12.office_city"];
        $sql = "t1.sd_bill_id=:bill_id";
        $data_in = ["bill_id" => $bill_id];
        $group_by = "";
        $order_by = "";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, false, []);
        return $data;
    }

    public function getOneWithInvoiceNumber($invoice_number)
    {
        $from = Table::INVOICE . " t1";
        $select = ["t1.ID,t1.status"];
        $sql = "t1.invoice_number=:ino";
        $data_in = ["ino" => $invoice_number];
        $group_by = "";
        $order_by = "";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
        return $data;
    }


    public function getInvoiceByBillIdForExport($bill_id)
    {
        $from =  Table::INVOICE  . " t1 
        INNER JOIN " . Table::SD_INVOICE_SUB . " t10 ON t1.ID = t10.sd_invoice_id
        INNER JOIN " . Table::BILL . " t22 ON t22.ID = t1.sd_bill_id
        INNER JOIN " . Table::SD_CUSTOMER . " t2 ON t1.sd_customer_id=t2.ID
        INNER JOIN " . Table::SD_CUSTOMER_ADDRESS . " t4 ON t1.sd_customer_address_id=t4.ID 
        INNER JOIN " . Table::HUBS . " t3 ON t1.sd_hub_id=t3.ID 
        INNER JOIN " . Table::EFLOFFICE . " t6 ON t3.sd_efl_office_id=t6.ID ";
        $select = [
            "t10.*,t1.invoice_number,t2.vendor_company,t3.hub_id,
            t4.billing_to,t4.address_one,t4.address_two,
        t4.gst_no,t2.pan_no,t4.pin_code, t1.ack_date,t1.sd_customer_id,t1.sd_hub_id",
         "DATE_FORMAT(t22.bill_start_date,'%d-%m-%Y') as start_date, DATE_FORMAT(t22.bill_end_date,'%d-%m-%Y') as end_date",
            "t6.address_one as of_add,t6.gst_no as of_gst,t6.pan_no as of_pan,t6.office_city as of_city,t6.pin_code as of_pin",
            "(SELECT t20.short_name FROM " . Table::STATEDB . " t20 WHERE t20.ID = t6.state LIMIT 0,1) as of_state",
            "(SELECT t21.short_name FROM " . Table::STATEDB . " t21 WHERE t21.ID = t4.state_name LIMIT 0,1) as customer_state",
        ];
        $sql = "t1.sd_bill_id=:bill_id";
        $data_in = ["bill_id" => $bill_id];
        $group_by = "";
        $order_by = "sd_invoice_id ASC";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, false, []);
        return $data;
    }


    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::INVOICE . " t1 
        INNER JOIN " . Table::BILL . " t12 ON t12.ID=t1.sd_bill_id
        INNER JOIN " . Table::SD_CUSTOMER . " t2 ON t1.sd_customer_id=t2.ID
        INNER JOIN " . Table::SD_CUSTOMER_ADDRESS . " t4 ON t1.sd_customer_address_id=t4.ID 
        INNER JOIN " . Table::HUBS . " t3 ON t1.sd_hub_id=t3.ID 
        INNER JOIN " . Table::EFLOFFICE . " t6 ON t3.sd_efl_office_id=t6.ID
        ";
        $select = [
            "t1.*,t12.bill_start_date,t12.bill_end_date,
            DATE_FORMAT(t12.bill_start_date,'%d-%m-%Y') as start_date, DATE_FORMAT(t12.bill_end_date,'%d-%m-%Y') as end_date",
            "t2.vendor_company,t3.hub_id,t4.billing_to,t4.address_one,t4.address_two,t4.gst_no,t2.pan_no,t4.pin_code",
            "t6.address_one as of_add,t6.gst_no as of_gst,t6.pan_no as of_pan,t6.cin_no as off_cin,t6.office_city as of_city,t6.pin_code as of_pin",
            "(SELECT t20.short_name FROM " . Table::STATEDB . " t20 WHERE t20.ID = t6.state LIMIT 0,1) as of_state",
            "(SELECT t21.short_name FROM " . Table::STATEDB . " t21 WHERE t21.ID = t4.state_name LIMIT 0,1) as customer_state",

        ];
        $sql = "t1.ID=:ID";
        $data_in = ["ID" => $id];
        $group_by = "";
        $order_by = "";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
        if (isset($data->ID)) {
            $data->cust_igst_amt = $data->of_state != $data->customer_state ? round($data->total_taxable * (18 / 100), 2) : 0;
            $data->cust_cgst_amt = $data->of_state == $data->customer_state ? round($data->total_taxable * (9 / 100), 2) : 0;
            $data->cust_sgst_amt = $data->of_state == $data->customer_state ? round($data->total_taxable * (9 / 100), 2) : 0;
            $data->cees_amt = "0.00";
            $data->state_cees = "0.00";
            $data->roundoff_amt = "0.00";
            $data->other_charge = "0.00";
            $data->due_date = "05/12/2024";
            $data->invoice_date = "25/11/2024";
        }
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



    private function getConsumptionWithVendor($hub_id, $ven_id, $start_date, $end_date)
    {
        $consumption = new EflConsumptionHelper($this->db);
        return $consumption->getConsumptionInvoiceByDateVendor($hub_id, $ven_id, $start_date, $end_date);
    }

    private function getVehicleCountWithVendor($hub_id, $ven_id, $start_date, $end_date)
    {
        $consumption = new EflVehiclesHelper($this->db);
        return $consumption->getVehicleInvoiceByDateVendor($hub_id, $ven_id, $start_date, $end_date);
    }


    private function getAllMappedCustomers()
    {
        $rateHelper = new VendorRateHelper($this->db);
        $rate_data = $rateHelper->getAllData();
        return $rate_data;
    }

    private function getCustomerRates($rate_id)
    {
        $rateSubHelper = new VendorRateSubHelper($this->db);
        return  $rateSubHelper->getAllByVendorRateId($rate_id);
    }


    public function insertInvoiceNew($bill_id, $bill_data)
    {
        $start_date = $bill_data->bill_start_date;
        $end_date = $bill_data->bill_end_date;
        $dates = SmartDateHelper::getDatesBetween($start_date, $end_date);
        $date_count = count($dates) > 0 ? count($dates) : 0;
        $dt = [
            "total_invoices" => 0,
            "unit_amount" => 0,
            "vehicle_amount" => 0,
            "others" => 0,
            "gst_amount" => 0,
            "total_amount" => 0,
        ];

        $_r_data = $this->getAllMappedCustomers();
        //  echo "count " .  count($_r_data);
        foreach ($_r_data  as $_obj) {
            //echo " hub id " . $_obj->sd_hubs_id . " cid " .  $_obj->sd_customer_id . " <br/>";
            $_data = $this->prepareSingleVendorData($bill_id, $_obj, $start_date, $end_date,  $date_count);
            $_data["invoice_type"] = 1;
            // var_dump($_data);
            if ($_data["total_taxable"] > 0) {
                $this->insertUpdateSingle($_data);
                $dt["gst_amount"]  += $_data["gst_amount"];
                $dt["total_amount"]  += $_data["total_amount"];
                $dt["total_invoices"]++;
            } else {
                $this->updateInvoiceDataNew($_data);
            }
        }
        //exit();
        return $dt;
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

    public function getVehicleServiceCharge($rates)
    {
        $charge = 0;
        foreach ($rates as $obj) {
            if ($obj->sd_hsn_id["value"] == 8) {
                $charge = $obj->price;
            }
        }
        return $charge;
    }

    public function getinfraCharge($rates)
    {
        $charge = 0;
        foreach ($rates as $obj) {
            if ($obj->sd_hsn_id["value"] == 6) {
                $charge = $obj->price;
            }
        }
        return $charge;
    }




    /**
     *  get the vehicle prices
     * 
     * 
     */
    public function getVehicleValues($rates, $vehicle_count, $type)
    {
        $parking_price = 0;
        $allowed_units = 0;
        $minimum_units = 0;
        $extra_price = 0;
        $hsn_id = 0;
        //  var_dump($rates);
        foreach ($rates as $obj) {
            // only parking and charging this condition is
            if ($obj->sd_hsn_id["value"] == 1 && $obj->sd_vehicle_types_id["value"] == $type) {
                // this is only for praking & Packgae so check vehicle count range and apply
                if ($obj->rate_type["value"] == 1) {
                    $parking_price = $obj->price;
                } else if ($obj->rate_type["value"] == 2) {
                    // minimum type
                    if ($vehicle_count > ($obj->min_start - 1) && $vehicle_count < $obj->min_end) {
                        $minimum_units = $obj->min_units_vehicle;
                        $allowed_units = $obj->min_units_vehicle * $vehicle_count;
                        $parking_price = $obj->price;
                        $extra_price = $obj->extra_price;
                        $hsn_id = 1;
                        //echo "entered here <br/>";
                    }
                }
            } else if ($obj->sd_hsn_id["value"] == 2 && $obj->sd_vehicle_types_id["value"] == $type) {
                // this is only for parking so check vehicle count range and apply
                // echo " value " . $obj->rate_type["value"] . " <br/>";
                if ($obj->rate_type["value"] == 1) {
                    $parking_price = $obj->price;
                } else if ($obj->rate_type["value"] == 2) {
                    // minimum type
                    if ($vehicle_count > ($obj->min_start - 1) && $vehicle_count < $obj->min_end) {
                        $parking_price = $obj->price;
                        $hsn_id = 2;
                    }
                }
            }
        }
        // echo "charge = " . $charge . "<br/>";
        return [$parking_price, $allowed_units, $minimum_units, $extra_price, $hsn_id];
    }
    /**
     * 
     */
    public function getUnitValues($rates, $meter_id)
    {

        $unit_price = 0;
        $hsn_id = 0;
        foreach ($rates as $obj) {
            if ($obj->sd_hsn_id["value"] == 3 && $meter_id == 1) {
                $hsn_id = 3;
                $unit_price = $obj->price;
            } else if ($obj->sd_hsn_id["value"] == 5 && $meter_id == 2) {
                $unit_price = $obj->price;
                $hsn_id = 5;
            } else if ($obj->sd_hsn_id["value"] == 7 && $meter_id == 3) {
                $unit_price = $obj->price;
                $hsn_id = 7;
            }
        }
        // echo " <br/><br/> unit charge " . $charge;
        return [$unit_price, $hsn_id];
    }


    private function calculateTotal($arr)
    {
        $total = 0;
        foreach ($arr as $obj) {
            if (isset($obj->count)) {
                $total = $total + $obj->count;
            }
        }
        return $total;
    }
    /**
     * 
     *  this is an heart function generate all lines items of invoice
     * 
     * Type 100 = Extrac units charging amount
     * 
     * 
     */

    public function prepareCustomerSubData($_obj, $units, $vehicles, $day_count)
    {
        $out = [];
        $rates = $this->getCustomerRates($_obj->ID);
        //
        //  var_dump($rates);
        $units_count = $this->calculateTotal($units);
        // generate invoice information for vehicles first with vehicle parking data  
        $allowed_units = 0;
        $extra_price = 0;
        foreach ($vehicles as $_v_obj) {
            list($parking_price, $allowed_units, $minimum_units, $extra_price, $hsn_id) = $this->getVehicleValues(
                $rates,
                $_v_obj->count / $day_count,
                $_v_obj->sd_vehicle_types_id
            );
            if ($parking_price > 0) {
                $_dt = [
                    "type" => $hsn_id,
                    "vehicle_id" => $_v_obj->sd_vehicle_types_id,
                    "price" => $parking_price,
                    "count" => $_v_obj->count,
                    "month_avg" => round($_v_obj->count / $day_count, 2),
                    "min_units" => $minimum_units,
                    "allowed_units" => $allowed_units,
                    "total" => round($_v_obj->count / $day_count, 2) * $parking_price,
                    "total_units" => $units_count,
                    "extra_units" => $units_count - $allowed_units > 0 ? $units_count - $allowed_units : 0
                ];
                $out[] = $_dt;
                $remaining_units = round($units_count - $allowed_units,2);
                if ($allowed_units > 0 && $units_count > 0 && $remaining_units > 0) {

                    $remaining_unit_price = $remaining_units * $extra_price;
                    // add a charging row
                    $_dt = [
                        "type" => 100,
                        "vehicle_id" => 0,
                        "price" => $extra_price,
                        "count" => $remaining_units,
                        "month_avg" => 0,
                        "min_units" => $units_count,
                        "allowed_units" => $allowed_units,
                        "total" => $remaining_unit_price
                    ];
                    $out[] = $_dt;
                }
            }
            //echo ""  . $_v_obj->sd_vehicle_types_id . " c=" . $_v_obj->count . " p " . $parking_price . " " . $allowed_units . " " . $minimum_units . "<br/>";
        }
        // if anything like allowed units is mentioned then it means extra units should be inserted as one more invoice

        // go for ac dc things
        //  var_dump($units);
        foreach ($units as $_u_obj) {
            $meter_id = $_u_obj->sd_meter_types_id;
            list($unit_price_test, $hsn_id) = $this->getUnitValues($rates, $meter_id);
            //  echo "<br/><br/>Unit price " . $unit_price_test . "   meter id " . $meter_id . " <br/><br/><br/>";
            if ($unit_price_test  > 0 &&  $_u_obj->count > 0) {
                //  echo "<br/><br/> E = Unit price " . $unit_price_test . "   meter id " . $meter_id . " <br/><br/>";
                $_u_obj->count = round($_u_obj->count,2);
                $_dt = [
                    "type" => $hsn_id,
                    "vehicle_id" => $meter_id,
                    "price" => $unit_price_test,
                    "count" => $_u_obj->count,
                    "month_avg" => 0,
                    "min_units" => $_u_obj->count,
                    "allowed_units" => $_u_obj->count,
                    "total" => round($_u_obj->count * $unit_price_test,2)
                ];
                $out[] = $_dt;
            }
        }

        $rent_charges = $this->getVehicleRentCharge($rates);
        if ($rent_charges > 0) {
            $_dt = [
                "type" => 4,
                "price" => $rent_charges,
                "count" => 1,
                "month_avg" => 0,
                "min_units" => 0,
                "allowed_units" => 0,
                "total" => $rent_charges
            ];
            $out[] = $_dt;
        }
        // service charges invoice
        $service_charges = $this->getVehicleServiceCharge($rates);
        if ($service_charges > 0) {
            $_dt = [
                "type" => 8,
                "price" => $service_charges,
                "count" => 1,
                "month_avg" => 0,
                "min_units" => 0,
                "allowed_units" => 0,
                "total" => $service_charges
            ];
            $out[] = $_dt;
        }


        // service charges invoice
        $infra_charges = $this->getinfraCharge($rates);
        if ($infra_charges > 0) {
            $_dt = [
                "type" => 6,
                "price" => $service_charges,
                "count" => 1,
                "month_avg" => 0,
                "min_units" => 0,
                "allowed_units" => 0,
                "total" => $service_charges
            ];
            $out[] = $_dt;
        }
        //var_dump($out);
        return $out;
    }

    private function calculateFinalTotal($out)
    {
        $total = 0;
        foreach ($out as $obj) {
            if (isset($obj["total"])) {
                $total = $total + floatval($obj["total"]);
            }
        }
        return $total;
    }


    public function prepareSingleVendorData($bill_id, $_obj, $start_date, $end_date,  $date_count)
    {
        $_data = [];
        //var_dump($_obj);
        // exit();
        $_data["sd_bill_id"] = $bill_id;
        $_data["sd_hub_id"] = $_obj->sd_hubs_id;
        $_data["sd_customer_id"] = $_obj->sd_customer_id;
        $_data["sd_customer_address_id"] = $_obj->sd_customer_address_id;
        $_data["sd_vendor_rate_id"] = $_obj->ID;
        $_data["bill_type"] = $_obj->bill_type;
        // get consumption with dates     
        $total_unit_types =  $this->getConsumptionWithVendor($_obj->sd_hubs_id, $_obj->sd_customer_id, $start_date, $end_date);
        // get vehicle count with dates
        $total_vehicles_types = $this->getVehicleCountWithVendor($_obj->sd_hubs_id, $_obj->sd_customer_id, $start_date, $end_date);
        //
        $_data["sub_data"] = $this->prepareCustomerSubData($_obj, $total_unit_types,   $total_vehicles_types, $date_count);
        //
        $_data["total_taxable"] = $this->calculateFinalTotal($_data["sub_data"]);
        if ($_obj->sd_customer_id == 7 && $_obj->sd_hubs_id == 98) {
            //var_dump($_data);
            // exit();
        }

        $_data["gst_percentage"] = 18;
        $_data["gst_amount"] = $_data["total_taxable"] * ($_data["gst_percentage"] / 100);
        $_data["total_amount"] =  $_data["total_taxable"]  +   $_data["gst_amount"];
        $_data["short_name"] = $_obj->short_name;
        return $_data;
    }


    /**
     * 
     */
    public function checkInvoiceExists($bill_id, $hub_id, $vendor_id)
    {
        $sql = " sd_bill_id=:bill_id AND sd_hub_id=:sd_hub_id AND sd_customer_id=:vend_id";
        $data_in = ["bill_id" => $bill_id, "sd_hub_id" => $hub_id, "vend_id" => $vendor_id];
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

    public function updateInvoiceDataNew($data)
    {
        $exist_data = $this->checkInvoiceExists($data["sd_bill_id"], $data["sd_hub_id"], $data["sd_customer_id"]);
        if (isset($exist_data->ID)) {
            $update_columns = [
                "total_taxable",
                "status",
                "gst_percentage",
                "gst_amount",
                "total_amount",
                "sd_customer_address_id",
                "invoice_date"
            ];
            $this->update($update_columns, $data, $exist_data->ID);
            return $exist_data->ID;
        }
    }

    private function insert_invoice_sub($_id, $sub_data, $_data)
    {
        $db = new InvoiceSubHelper($this->db);
        $db->insert_update_data($_id, $sub_data, $_data);
    }


    private function getNextSerialNumber($state, $fin_year)
    {
        $from = TABLE::INVOICE . " t1";
        $sql = "t1.state_name=:state_name AND invoice_fin_year=:invoice_fin_year";
        $data_in = ["state_name" => $state, "invoice_fin_year" => $fin_year];
        $select = ["(MAX(invoice_serial_number)) as in_number"];
        $group_by = "";
        $order_by = "";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
        return isset($data->in_number) ? $data->in_number + 1 : 1;
    }

    /**
     * 
     */
    public function insertUpdateSingle($data)
    {
        $exist_data = $this->checkInvoiceExists($data["sd_bill_id"], $data["sd_hub_id"], $data["sd_customer_id"]);
        $data["invoice_date"] = date("Y-m-d");
        $sub_data = $data["sub_data"];
        if (isset($exist_data->ID)) {
            if ($exist_data->status  < 5) {
                // to avoid updatation after irn number generated
                $this->updateInvoiceDataNew($data);
                $this->insert_invoice_sub($exist_data->ID, $sub_data, $data);
            }
            return $exist_data->ID;
        } else {
            $insert_columns = [
                "sd_bill_id",
                "sd_hub_id",
                "sd_customer_id",
                "sd_customer_address_id",
                "sd_vendor_rate_id",
                "total_taxable",
                "status",
                "gst_percentage",
                "gst_amount",
                "total_amount",
                "invoice_date",
                "state_name",
                "invoice_type"
            ];
            $data["state_name"] =  $data["short_name"];
            $id = $this->insert($insert_columns, $data);
            $up_columns = [
                "invoice_fin_year",
                "invoice_serial_number",
                "invoice_number",
            ];
            $fin_year = "24-25";
            $serial_number = $this->getNextSerialNumber($data["state_name"], $fin_year);
            $invoice_number = "EFL/" . $data["short_name"] . DS .  $serial_number . "/" . $fin_year;
            $up_data = [
                "invoice_fin_year" => $fin_year,
                "invoice_serial_number" => $serial_number,
                "invoice_number" => $invoice_number,
            ];
            $this->update($up_columns, $up_data, $id);
            // update the invoice sub data 
            $this->insert_invoice_sub($id, $sub_data, $data);
        }
    }

    public function getTotal($sub_data)
    {
        $total = array_reduce($sub_data, function ($carry, $item) {
            //var_dump($item);
            return $carry + $item->total;
        }, 0);
        return $total;
    }


    public function prepareGenerateInvoice($data, $sub_helper)
    {
        $_dt = $data;
        $data->sub_data = $sub_helper->getAllByInvoiceId($data->ID);
        // loop over sub_data 
        $_sub_data_vehicle = [];
        foreach ($data->sub_data  as $_key => $_obj) {
            // var_dump($_obj);
            if ($_obj->vehicle_id > 0 && $_obj->count > 0 && ($_obj->type == 1 || $_obj->type == 2)) {
                $_item_data = $this->getVehicleCount($_dt, $_obj);
                $_item_data["annexure"] = ($_key + 1);
                $_sub_data_vehicle[] = $_item_data;
            }
            $_obj->unit = "NOS";
            if ($_obj->type == 1 || $_obj->type == 2) {
                $_obj->count = $_obj->month_avg;
            }
            if ($_obj->type == 3 || $_obj->type == 5 || $_obj->type == 7 || $_obj->type == 100) {
                $_obj->unit = "UNITS";
            }
        }
        $data->sub_data_vehicle = $_sub_data_vehicle;
        // modify the subdata here to have single line item in pdf 
        $customer_id = 3;
        if($data->sd_customer_id===$customer_id){
            $s_obj =$data->sub_data[0];
            $s_obj->type_hsn = 998714;
            $s_obj->type_desc = "ELECTRIC VEHICLE PARKING AND CHARGING FEE 3WL AND 4WL (from ".$data->start_date." to ".$data->end_date.")";
            $s_obj->type =101; 
            $s_obj->count = 1;
            $s_obj->price = $s_obj->total = $this->getTotal($data->sub_data);
            $data->sub_data = [$s_obj];
        }



        $this->generateInvoicePdf($data->ID, $data);
    }


    public function generateInvoicePdf($id, $data)
    {

        $html = InvoicePdf::getHtml($data);
        $qr_path = "images/" . $id . "_qr.png";
        // $qr_text = isset($data->signed_qr_code) ? $data->signed_qr_code : "test";
        $qr_text = isset($data->irn_number) ? $data->irn_number : "test";
        SmartQrCodeHelper::generateQrImage($qr_text, $qr_path);
        $html_modified = SiteImageHelper::replaceImages($html, ["QR_CODE" => $id . "_qr.png"]);
       // echo $html_modified;
       //  exit();
        $this->initiate_curl($html_modified, $id);
    }

    public function getInvoicePath($id)
    {
        return "invoice" . DS . $id . DS . "invoice.pdf";
    }

    private function initiate_curl($html, $id)
    {
        $data = new \stdClass();
        $data->content = base64_encode($html);
        $curl = new SmartCurl();
        $_output = $curl->post("/taskapi/html_to_pdf", $data);
        $_output_obj = json_decode($_output);
        if (isset($_output_obj->data)) {
            $path = "invoice" . DS . $id . DS . "invoice.pdf";
            SmartFileHelper::storeFile($_output_obj->data, $path);
        }
    }

    public function initiate_curl_sign($data)
    {
        $invoice_path = $this->getInvoicePath($data->ID);
        $content = SmartFileHelper::encodeFileToBase64(file_path: $invoice_path);
        $public_server = SmartGeneral::getEnv("PUBLIC_URL");
        $url = $public_server . "/e-fuel/vendor-wish/" . $data->sd_bill_id . "?invoice_id=" . $data->ID . "&&";
        $data = SdDigiHelper::getDigiObjectSingleSign($content, "USER", "USER", "AUTH_SIGN", $url);
        $curl = new SmartCurl();
        $_output = $curl->post("/taskapi/insert_sign", $data);
        $_output_obj = json_decode($_output);
        return $_output_obj;
    }

    public function verify_sign_info($token)
    {
        $data = new stdClass();
        $data->id = $token;
        $curl = new SmartCurl();
        $_output = $curl->post("/taskapi/get_task", $data);
        $_output_obj = json_decode($_output);
        return $_output_obj;
    }


    public function storeSignedFile($id, $content)
    {
        $path = "invoice" . DS . $id . DS . "invoicesign.pdf";
        SmartFileHelper::storeFile($content, $path);
    }

    private function getOneDayCount($_data, $date, $vehicle_id = 0)
    {
        foreach ($_data as $obj) {
            if ($obj->date == $date && $obj->sd_vehicle_types_id == $vehicle_id) {
                return $obj->count;
                break;
            }
        }
        return 0;
    }


    public function getOneDetails($id)
    {
        $_data = $this->getOneData($id);
        //var_dump($_data);
        // with the dates and vendor id get the counts data
        $vehicle_obj = new EflVehiclesHelper($this->db);
        $_vehicle_count_data = $vehicle_obj->getVehicleInvoiceByDateVendor($_data->sd_customer_id, $_data->bill_start_date, $_data->bill_end_date, false);
        // var_dump($_vehicle_count_data);
        $dates = SmartDateHelper::getDatesBetween($_data->bill_start_date, $_data->bill_end_date);
        $days_count = count($dates) > 0 ? count($dates) : 1;
        //echo "count days " . $days_count;
        $sub_data = [];
        foreach ($dates as $date) {
            $day_count = $this->getOneDayCount($_vehicle_count_data, $date);
            $day_value = $_data->charge_per_month / $days_count;
            $_sub_data = [
                "date" => $date,
                "count" => $day_count,
                "charge_month" => $_data->charge_per_month,
                "charge_per_day" => round($day_value, 2),
                "total" => round($day_count * $day_value),
            ];
            $sub_data[] = $_sub_data;
        }
        $_data_out = (array)$_data;
        $_data_out["avg_vehicles"] = $_data_out["total_vehicles"];
        $_data_out["sub_data"] = $sub_data;
        return $_data_out;
    }


    public function getVehicleCount($_data, $_obj)
    {
        $vehicle_obj = new EflVehiclesHelper($this->db);
        $_vehicle_count_data = $vehicle_obj->getVehicleInvoiceByDateVendor($_data->sd_hub_id, $_data->sd_customer_id, $_data->bill_start_date, $_data->bill_end_date, false);
        // var_dump($_vehicle_count_data);
        $dates = SmartDateHelper::getDatesBetween($_data->bill_start_date, $_data->bill_end_date);
        $days_count = count($dates) > 0 ? count($dates) : 1;
        $sub_data = [];
        //echo "<br/><br/>";
        foreach ($dates as $date) {
            $day_count = $this->getOneDayCount($_vehicle_count_data, $date, $_obj->vehicle_id);
            $day_value = $_obj->price / $days_count;
            $_sub_data = [
                "date" => SmartDateHelper::dateFormat($date),
                "count" => $day_count,
                "charge_month" => $_obj->price,
                "charge_per_day" => round($day_value, 2),
                "total" => round($day_count * $day_value),
            ];
            $sub_data[] = $_sub_data;
        }
        //  var_dump($sub_data);
        $_data_out = [
            "invoice_number" => $_data->invoice_number,
            "type_desc" => $_obj->type_desc,
            "type" => $_obj->type,
            "vendor_company" => $_data->vendor_company,
            "total_vehicles" => $_obj->count,
            "avg_vehicles" => $_obj->month_avg,
            "min_units_vehicle" => $_obj->min_units,
            "units_allowed" => $_obj->allowed_units,
            "total_units" => $_obj->total_units,
            "extra_units" => $_obj->extra_units,
            "total_vehicles_charge" => $_obj->total
        ];

        // $_data_out["avg_vehicles"] = $_data_out["total_vehicles"];
        $_data_out["sub_data"] = $sub_data;
        return $_data_out;
    }
}
