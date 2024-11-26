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
class TaxillaExcelHelper extends BaseHelper
{

    public function getData($_dt)
    {
        $cgst = $sgst =  $_dt->total * (9 / 100);
        $igst =  $_dt->total * (18 / 100);
        $quantity = $_dt->count;
        if ($_dt->type == 1 || $_dt->type == 2) {
            $quantity = $_dt->month_avg;
        }
        $units = "NOS";
        if ($_dt->type == 3 || $_dt->type == 5 || $_dt->type == 7) {
            $units = "UNITS";
        }

        $out = [
            "Transaction Reference Number" => $_dt->invoice_number,
            "Invoice Number" => $_dt->invoice_number,
            "Invoice Date" => date("d-m-Y"),
            "Hub Name" => $_dt->hub_id,
            "Supply Type" => "Outward",
            "Invoice Type" => " Regular",
            "Supply Category" => "Taxable",
            "Transaction Type" => " INV",
            "Transaction Subtype" => " Regular",
            "Place of Supply" => $_dt->of_state,
            "Supplier PAN" => $_dt->of_pan,
            "Reverse Charge" => "No",
            "Supplier Legal Name" => "TTL ELECTRIC FUEL PRIVATE LIMITED",
            "Org Code" => $_dt->of_gst,
            "Supplier Address 1" => $_dt->of_add,
            "Supplier Address 2" => "",
            "Supplier City" => $_dt->of_city,
            "Supplier State" => $_dt->of_state,
            "Supplier PIN Code" => $_dt->of_pin,
            "Goods/Services" => " Services",
            "HSN Code" => "996743",
            "HSN Description" => $_dt->type_desc,
            "PO Number" => "",
            "PO Date" => "",
            "Line item ID" => "1",
            "Quantity" => $quantity,
            "UQC" => $units,
            "Rate per Quantity" => $_dt->price,
            "Gross Value" => $_dt->total,
            "Discount Before GST" => "",
            "Freight" => "",
            "Pre Taxable Value" => $_dt->total,
            "Taxable Value" => $_dt->total,
            "GST Rate" => "18",
            "IGST Amount" => $_dt->of_state != $_dt->customer_state ? $igst : "",
            "CGST Amount" => $_dt->of_state == $_dt->customer_state ? $cgst : "",
            "SGST/ UTGST Amount" => $_dt->of_state == $_dt->customer_state ? $sgst : "",
            "Total Taxable Value" => $igst,
            "Total IGST Amount" => "",
            "Total CGST Amount" => "",
            "Total SGST/UTGST Amount" => "",
            "TCS" => "-",
            "Invoice Value" => round($_dt->total + $igst, 2),
            "Payment Terms" => "",
            "Buyer GSTIN/UIN" => $_dt->gst_no,
            "Buyer Legal Name" => $_dt->billing_to,
            "Buyer Address 1" => $_dt->address_one,
            "Buyer Address 2" => $_dt->address_two,
            "Buyer City" => "",
            "Buyer State" => $_dt->customer_state,
            "Buyer PIN Code" => $_dt->pin_code,
            "Ship to Legal Name" => $_dt->billing_to,
            "Ship to Address 1" => $_dt->address_one,
            "Ship to Address 2" => $_dt->address_two,
            "Ship to City" => "",
            "Ship to State" => $_dt->customer_state,
            "Ship to PIN Code" => $_dt->pin_code,
            "Receiver E mail ID" => "",
            "LR No" => "",
            "Transporter Name" => "",
            "Vehicle No" => "",
            "Remarks" => "",
            "Auto Generate IRN" => "Yes",
            "Auto Generate EWB" => "NO",
            "EWB Sub Supply Type" => "",
            "EWB Sub Supply Type Description" => "",
            "EWB Transaction Type" => "",
            "Transport Mode" => "",
            "Type of Cargo" => "",
            "Distance level(km)" => "",
            "Transporter ID" => "",
            "Transport Document No" => "",
            "Transport Document Date" => "",
            "Transporter e-mail" => "",
            "Entity-1" => "As per Rule 46 of CGST Rules, 2017",
            "Entity-2" => "",
            "Dispatch Name" => "",
            "Dispatch Address 1" => "   ",
            "Dispatch Address 2" => "",
            "Dispatch City" => "",
            "Dispatch State" => "",
            "Dispatch PIN Code" => "",
            "Dispatcher E mail ID" =>  "",
            "Dispatcher Phone Number" =>  "",
            "Ship Code" => "",
            "Ship to Trade Name" => "",
            "Ship to GSTIN" => "",
            "Receiver Phone Number" => "",
            "E Commerce GSTIN" => "",
            "Payee Name" => "",
            "Payment Mode" => "",
            "Bank Account Number" => "",
            "Bank IFSC code" => "",
            "Payment Instructions" => "",
            "Credit Transfer Terms" => "",
            "Direct Debit Terms" => "",
            "Credit Days" => "",
            "Amount Paid" => "",
            "Amount Due" => "",
            "Due Date" => "",
            "Shipping Bill Number" => "",
            "Shipping Bill Date" => "",
            "Export Duty Amount" => "",
            "Port Code" => "",
            "Tax Scheme" => "",
            "Invoice Currency Code" => "",
            "Invoice Period Start Date" => "",
            "Invoice Period End Date" => "",
            "Preceding Invoice Number" => "",
            "Preceding Invoice Date" => "",
            "Other Reference Number" => "",
            "Receipt Advice Number" => "",
            "Receipt Advice Date" => "",
            "Lot/Batch Reference Number" => "",
            "Contract Reference Number" => "",
            "External Reference Number" => "",
            "Project Reference Number" => "",
            "Supporting Document URL" => "",
            "Other Supporting Document" => "",
            "Any Other Additional Information" => "",
            "Total Invoice Value in Foreign Currency" => "",
            "Option for Supplier for Refund" => "",
            "Foreign Currency" => "",
            "Customer Country Code" => "",
            "Total Cess Amount" => "",
            "Total State Cess Amount" => "",
            "Rounding Off Amount" => "",
            "Discount at Invoice level" => "",
            "Charges at Invoice level" => "",
            "Flag for Supply covered under sec 7 of IGST Act" => "",
            "Original Invoice Value" => "",
            "Original POS" => "",
            "Original Transaction Type" => "",
            "Original Transaction ID" => "",
            "Differential Percentage" => "",
            "Schedule Description Code" => "",
            "Exemption Notification No" => "",
            "Compensation Cess Description Code" => "",
            "RCM Description Code" => "",
            "Bar code of Product" => "",
            "Batch Name" => "",
            "Warranty Date" => "",
            "Expiry Date" => "",
            "Cess Rate" => "",
            "State Cess Rate" => "",
            "Cess Non Advol Value" => "",
            "Compensation Cess Amount" => "",
            "State Cess Amount" => "",
            "State Cess Non Advol Amount" => "",
            "Other Charges" => "",
            "PO Line Reference Number" => "",
            "Product Serial Number" => "",
            "Attribute Details" => "",
            "Attribute Value" => "",
            "Entity-3" => "",
            "Entity-4" => "",
            "Validate INV" => "",
            "Generate Proforma" => "",
            "Auto Generate Invoice No" => "",
            "Invoice Reference Number" => "",
            "Free Quantity" => "",
            "Department Name" => "",
            "Department ID" => "",
            "Origin Country" => "",
            "Supplier Code" => "",
            "Supplier Location Code" => "",
            "Supplier GSTIN/UIN" => "",
            "Buyer Code" => "",
            "Buyer Location Code" => "",
            "Supplier Trade Name" => "",
            "Supplier E mail ID" => "",
            "Supplier Phone Number" => "",
            "Buyer Trade Name" => "",
            "Buyer E mail ID" => "",
            "Buyer Phone Number" => "",
            "Dispatch Code" => "",
            "Dispatch GSTIN" => "",
            "Invoice Remarks" => ""
        ];
        return $out;
    }
}
