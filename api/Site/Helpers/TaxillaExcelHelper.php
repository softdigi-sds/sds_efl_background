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
        $out = [
            "Transaction Reference Number" => $_dt->invoice_number,
            "Invoice Number" => $_dt->invoice_number,
            "Invoice Date" => $_dt->ack_date,
            "Hub Name" => $_dt->hub_name,
            "Supply Type" => "Outward",
            "Invoice Type" => $_dt->type,
            "Supply Category" => "Taxable",
            "Transaction Type" => $_dt->invoice_number,
            "Transaction Subtype" => $_dt->invoice_number,
            "Place of Supply" => $_dt->invoice_number,
            "Supplier PAN" => $_dt->invoice_number,
            "Reverse Charge" => $_dt->invoice_number,
            "Supplier Legal Name" => $_dt->invoice_number,
            "Org Code" => $_dt->invoice_number,
            "Supplier Address 1" => $_dt->invoice_number,
            "Supplier Address 2" => $_dt->invoice_number,
            "Supplier City" => $_dt->invoice_number,
            "Supplier State" => $_dt->invoice_number,
            "Supplier PIN Code" => $_dt->invoice_number,
            "Goods/Services" => $_dt->invoice_number,
            "HSN Code" => $_dt->invoice_number,
            "HSN Description" => $_dt->invoice_number,
            "PO Number" => $_dt->invoice_number,
            "PO Date" => $_dt->invoice_number,
            "Line item ID" => $_dt->invoice_number,
            "Quantity" => $_dt->invoice_number,
            "UQC" => $_dt->invoice_number,
            "Rate per Quantity" => $_dt->invoice_number,
            "Gross Value" => $_dt->invoice_number,
            "Discount Before GST" => $_dt->invoice_number,
            "Freight" => $_dt->invoice_number,
            "Pre Taxable Value" => $_dt->invoice_number,
            "Taxable Value" => $_dt->invoice_number,
            "GST Rate" => $_dt->invoice_number,
            "IGST Amount" => $_dt->invoice_number,
            "CGST Amount" => $_dt->invoice_number,
            "SGST/ UTGST Amount" => $_dt->invoice_number,
            "Total Taxable Value" => $_dt->invoice_number,
            "Total IGST Amount" => $_dt->invoice_number,
            "Total CGST Amount" => $_dt->invoice_number,
            "Total SGST/UTGST Amount" => $_dt->invoice_number,
            "TCS" => $_dt->invoice_number,
            "Invoice Value" => $_dt->invoice_number,
            "Payment Terms" => $_dt->invoice_number,
            "Buyer GSTIN/UIN" => $_dt->invoice_number,
            "Buyer Legal Name" => $_dt->invoice_number,
            "Buyer Address 1" => $_dt->invoice_number,
            "Buyer Address 2" => $_dt->invoice_number,
            "Buyer City" => $_dt->invoice_number,
            "Buyer State" => $_dt->invoice_number,
            "Buyer PIN Code" => $_dt->invoice_number,
            "Ship to Legal Name" => $_dt->invoice_number,
            "Ship to Address 1" => $_dt->invoice_number,
            "Ship to Address 2" => $_dt->invoice_number,
            "Ship to City" => $_dt->invoice_number,
            "Ship to State" => $_dt->invoice_number,
            "Ship to PIN Code" => $_dt->invoice_number,
            "Receiver E mail ID" => $_dt->invoice_number,
            "LR No" => $_dt->invoice_number,
            "Transporter Name" => $_dt->invoice_number,
            "Vehicle No" => $_dt->invoice_number,
            "Remarks" => $_dt->invoice_number,
            "Auto Generate IRN" => $_dt->invoice_number,
            "Auto Generate EWB" => $_dt->invoice_number,
            "EWB Sub Supply Type" => $_dt->invoice_number,
            "EWB Sub Supply Type Description" => $_dt->invoice_number,
            "EWB Transaction Type" => $_dt->invoice_number,
            "Transport Mode" => $_dt->invoice_number,
            "Type of Cargo" => $_dt->invoice_number,
            "Distance level(km)" => $_dt->invoice_number,
            "Transporter ID" => $_dt->invoice_number,
            "Transport Document No" => $_dt->invoice_number,
            "Transport Document Date" => $_dt->invoice_number,
            "Transporter e-mail" => $_dt->invoice_number,
            "Entity-1" => $_dt->invoice_number,
            "Entity-2" => $_dt->invoice_number,
            "Dispatch Name" => $_dt->invoice_number,
            "Dispatch Address 1" => $_dt->invoice_number,
            "Dispatch Address 2" => $_dt->invoice_number,
            "Dispatch City" => $_dt->invoice_number,
            "Dispatch State" => $_dt->invoice_number,
            "Dispatch PIN Code" => $_dt->invoice_number,
            "Dispatcher E mail ID" => $_dt->invoice_number,
            "Dispatcher Phone Number" => $_dt->invoice_number,
            "Ship Code" => $_dt->invoice_number,
            "Ship to Trade Name" => $_dt->invoice_number,
            "Ship to GSTIN" => $_dt->invoice_number,
            "Receiver Phone Number" => $_dt->invoice_number,
            "E Commerce GSTIN" => $_dt->invoice_number,
            "Payee Name" => $_dt->invoice_number,
            "Payment Mode" => $_dt->invoice_number,
            "Bank Account Number" => $_dt->invoice_number,
            "Bank IFSC code" => $_dt->invoice_number,
            "Payment Instructions" => $_dt->invoice_number,
            "Credit Transfer Terms" => $_dt->invoice_number,
            "Direct Debit Terms" => $_dt->invoice_number,
            "Credit Days" => $_dt->invoice_number,
            "Amount Paid" => $_dt->invoice_number,
            "Amount Due" => $_dt->invoice_number,
            "Due Date" => $_dt->invoice_number,
            "Shipping Bill Number" => $_dt->invoice_number,
            "Shipping Bill Date" => $_dt->invoice_number,
            "Export Duty Amount" => $_dt->invoice_number,
            "Port Code" => $_dt->invoice_number,
            "Tax Scheme" => $_dt->invoice_number,
            "Invoice Currency Code" => $_dt->invoice_number,
            "Invoice Period Start Date" => $_dt->invoice_number,
            "Invoice Period End Date" => $_dt->invoice_number,
            "Preceding Invoice Number" => $_dt->invoice_number,
            "Preceding Invoice Date" => $_dt->invoice_number,
            "Other Reference Number" => $_dt->invoice_number,
            "Receipt Advice Number" => $_dt->invoice_number,
            "Receipt Advice Date" => $_dt->invoice_number,
            "Lot/Batch Reference Number" => $_dt->invoice_number,
            "Contract Reference Number" => $_dt->invoice_number,
            "External Reference Number" => $_dt->invoice_number,
            "Project Reference Number" => $_dt->invoice_number,
            "Supporting Document URL" => $_dt->invoice_number,
            "Other Supporting Document" => $_dt->invoice_number,
            "Any Other Additional Information" => $_dt->invoice_number,
            "Total Invoice Value in Foreign Currency" => $_dt->invoice_number,
            "Option for Supplier for Refund" => $_dt->invoice_number,
            "Foreign Currency" => $_dt->invoice_number,
            "Customer Country Code" => $_dt->invoice_number,
            "Total Cess Amount" => $_dt->invoice_number,
            "Total State Cess Amount" => $_dt->invoice_number,
            "Rounding Off Amount" => $_dt->invoice_number,
            "Discount at Invoice level" => $_dt->invoice_number,
            "Charges at Invoice level" => $_dt->invoice_number,
            "Flag for Supply covered under sec 7 of IGST Act" => $_dt->invoice_number,
            "Original Invoice Value" => $_dt->invoice_number,
            "Original POS" => $_dt->invoice_number,
            "Original Transaction Type" => $_dt->invoice_number,
            "Original Transaction ID" => $_dt->invoice_number,
            "Differential Percentage" => $_dt->invoice_number,
            "Schedule Description Code" => $_dt->invoice_number,
            "Exemption Notification No" => $_dt->invoice_number,
            "Compensation Cess Description Code" => $_dt->invoice_number,
            "RCM Description Code" => $_dt->invoice_number,
            "Bar code of Product" => $_dt->invoice_number,
            "Batch Name" => $_dt->invoice_number,
            "Warranty Date" => $_dt->invoice_number,
            "Expiry Date" => $_dt->invoice_number,
            "Cess Rate" => $_dt->invoice_number,
            "State Cess Rate" => $_dt->invoice_number,
            "Cess Non Advol Value" => $_dt->invoice_number,
            "Compensation Cess Amount" => $_dt->invoice_number,
            "State Cess Amount" => $_dt->invoice_number,
            "State Cess Non Advol Amount" => $_dt->invoice_number,
            "Other Charges" => $_dt->invoice_number,
            "PO Line Reference Number" => $_dt->invoice_number,
            "Product Serial Number" => $_dt->invoice_number,
            "Attribute Details" => $_dt->invoice_number,
            "Attribute Value" => $_dt->invoice_number,
            "Entity-3" => $_dt->invoice_number,
            "Entity-4" => $_dt->invoice_number,
            "Validate INV" => $_dt->invoice_number,
            "Generate Proforma" => $_dt->invoice_number,
            "Auto Generate Invoice No" => $_dt->invoice_number,
            "Invoice Reference Number" => $_dt->invoice_number,
            "Free Quantity" => $_dt->invoice_number,
            "Department Name" => $_dt->invoice_number,
            "Department ID" => $_dt->invoice_number,
            "Origin Country" => $_dt->invoice_number,
            "Supplier Code" => $_dt->invoice_number,
            "Supplier Location Code" => $_dt->invoice_number,
            "Supplier GSTIN/UIN" => $_dt->invoice_number,
            "Buyer Code" => $_dt->invoice_number,
            "Buyer Location Code" => $_dt->invoice_number,
            "Supplier Trade Name" => $_dt->invoice_number,
            "Supplier E mail ID" => $_dt->invoice_number,
            "Supplier Phone Number" => $_dt->invoice_number,
            "Buyer Trade Name" => $_dt->invoice_number,
            "Buyer E mail ID" => $_dt->invoice_number,
            "Buyer Phone Number" => $_dt->invoice_number,
            "Dispatch Code" => $_dt->invoice_number,
            "Dispatch GSTIN" => $_dt->invoice_number,
            "Invoice Remarks" => $_dt->invoice_number
        ];
        return $out;
    }
}
