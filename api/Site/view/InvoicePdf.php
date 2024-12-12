<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Site\view;
// use Site\images\qr_code;
use Core\Helpers\SmartGeneral;



class InvoicePdf
{

  private $data = [];

  function __construct($data)
  {
    $this->data = is_object($data) ? (array) $data : $data;
    // var_dump($this->data);

  }
  //
  private function get($index)
  {
    return isset($this->data[$index]) ? str_replace("&", " ", $this->data[$index]) : "";
  }

  private function getIndex($dta, $index)
  {
    $dt = (array)$dta;
    return isset($dt[$index]) ? $dt[$index] : "";
  }

  private function pget($index)
  {
    $dummy = $this->get($index);
    echo $dummy;
  }

  public function gettable()
  {
    $_data = isset($this->data["sub_data"]) ? $this->data["sub_data"] : [];
    ob_start();
?>
    <div style="font-size:11px">
      <p>Goods Details</p>
      <table style="width:100%;border-collapse:collapse" border="1">
        <tr>
          <th>Sl.No</th>
          <th>Description of Goods/Services</th>
          <th>HSN/SAC Code</th>
          <th>Quantity</th>
          <th>Unit</th>
          <th>Unit Price (RS)</th>
          <th>Discount (RS)</th>
          <th>Freight</th>
          <th>Taxable Amount(RS)</th>
          <th>Total Rate (GST+Cess|State Cess+Cess Non.Advol)</th>
          <th>TCS (Rs)</th>
          <th>Total</th>
        </tr>
        <?php foreach ($_data as $key => $obj) { ?>
          <tr>
            <td><?php echo $key + 1; ?> </td>
            <td><?php echo $this->getIndex($obj, "type_desc") ?>
              (from <?php echo $this->get("start_date") ?> to
              <?php echo $this->get("end_date") ?>)
            </td>
            <td><?php echo $this->getIndex($obj, "type_hsn") ?> </td>
            <td><?php echo $this->getIndex($obj, "count") ?></td>
            <td><?php echo $this->getIndex($obj, "unit") ?> </td>
            <td><?php echo $this->getIndex($obj, "price") ?></td>
            <td>0.00</td>
            <td>0</td>
            <td><?php echo $this->getIndex($obj, "total") ?></td>
            <td>18.00 + 0 | 0 + 0</td>
            <td>0.0 </td>
            <td><?php echo $this->getIndex($obj, "total_with_gst") ?></td>
          </tr>
        <?php } ?>
      </table>
    </div>
  <?php
    $html = ob_get_clean();
    return $html;
  }


  public function getPartPdf()
  {
    $sub_vh_data = isset($this->data["sub_data_vehicle"]) ? $this->data["sub_data_vehicle"] : [];
    $html = "";
    foreach ($sub_vh_data as $single_vh_data) {
      $html = VehiclesPdf::getHtml($single_vh_data);
    }
    return $html;
  }



  public function get_html()
  {
    $sub_vh_data = isset($this->data["sub_data_vehicle"]) ? $this->data["sub_data_vehicle"] : [];
    ob_start();
  ?>
    <html lang="en">

    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>INVOICE PDF</title>
      <style>
        .page-break {
          page-break-before: always;
        }

        body {
          font-family: Arial, sans-serif;
        }
      </style>
    </head>

    <body>
      <div>
        <img style="width:170px;" src="[LOGO]" alt="logo code" />
      </div>
      <div style="font-size:11px">
        <table style="width:100%;">
          <tr>
            <td style="width:20%"></td>
            <td style="text-align: center;width:60%;font-weight:bold">
              <p>TAX INVOICE<br />(As per Rule 46 of CGST Rules, 2017)</p>
            </td>
            <td style="text-align: right; width:20%;font-weight:bold ">
              <p>ORIGINAL FOR RECIPIENT</p>
            </td>
          </tr>
          <tr>
            <td colspan="3" style="text-align: center;font-weight:bold">
              TTL ELECTRIC FUEL PRIVATE LIMITED <br /><?php echo $this->get("of_add") ?>
            </td>
          </tr>
        </table>
      </div>
      <div style="border:1px solid black; margin-bottom: 0;font-size:11px">
        <table style="width:100%;">
          <tr>
            <td style="text-align: left;">
              <p>ACK No:<?php echo $this->get("ack_no") ?></p>
              <p>ACK Date: <?php echo $this->get("ack_date") ?></p>
              <p> IRN No:<?php echo $this->get("irn_number") ?></p>
            </td>
            <td style="text-align: right;">
              <img style="width:150px; text-align: right;" src="[QR_CODE]" alt="qr code" />
            </td>
          </tr>
        </table>
      </div>


      <table style="width:100%; margin-top: 0;font-size:11px;border-collapse:collapse" border="1">
        <tr>
          <td>
            <b>GSTIN:<?php $this->pget("of_gst") ?> </b><br />
            <b>Tax is payable under reverse charge: No</b><br />
            <b>Invoice No: <?php echo $this->get("invoice_number") ?></b><br />
            <b>Invoice Date: <?php echo $this->get("invoice_date") ?></b><br />
            <b>Place of Supply: <?php $this->pget("of_city") ?></b><br />
            <b>Due Date: <?php $this->pget("due_date") ?></b><br />
          </td>
          <td>
            <b>Vehicle No:</b> <br />
            <b>LR No:</b><br />
            <b>Transporter:</b><br />
            <b>Date of Supply: <?php echo $this->get("date_of_supply") ?></b><br />
            <b>Shipped From: <?php $this->pget("of_city") ?></b><br />
            <b>Transporter ID:</b><br />
          </td>
        </tr>
        <tr>
          <td>
            <p><b>Receiver (Billed to)</b></p>
          </td>
          <td>
            <p><b>Consignee (Shipped to)</b></p>
          </td>
        </tr>
        <tr>
          <td>
            <p><?php $this->pget("billing_to") ?><br />
              <?php $this->pget("address_one") ?> <br />
              <b>State/State Code: <?php $this->pget("customer_state") ?></b> <br />
              <b>GSTIN: <?php $this->pget("gst_no") ?></b>
            </p>
          </td>
          <td>
            <p><?php $this->pget("billing_to") ?><br />
              <?php $this->pget("address_one") ?> <br />
              <b>State/State Code: <?php $this->pget("customer_state") ?></b> <br />
              <b>GSTIN: <?php $this->pget("gst_no") ?></b>
            </p>
          </td>
        </tr>
      </table>
      <?php echo $this->gettable() ?>

      <div style="padding-top:10px;font-size:11px">
        <table style="width:100%;border-collapse:collapse " border="1">
          <tr>
            <th>Tax'ble Amt</th>
            <th>CGST Amt</th>
            <th>SGST Amt </th>
            <th>IGST Amt</th>
            <th>CESS Amt</th>
            <th>State CESS Amt</th>
            <th>Round off Amt</th>
            <th>Other Charges</th>
            <th>Total Inv. Amt</th>
          </tr>
          <tr>
            <td><?php echo $this->get("total_taxable") ?></td>
            <td><?php echo $this->get("cust_cgst_amt") ?> </td>
            <td><?php echo $this->get("cust_sgst_amt") ?></td>
            <td><?php echo $this->get("cust_igst_amt") ?> </td>
            <td><?php echo $this->get("cees_amt") ?></td>
            <td><?php echo $this->get("state_cees") ?></td>
            <td><?php echo $this->get("roundoff_amt") ?></td>
            <td><?php echo $this->get("other_charge") ?></td>
            <td><?php echo $this->get("total_amount") ?></td>
          </tr>
        </table>
      </div>
      <div style="font-size:11px">
        <table style="width:100% ; border-collapse:collapse" border="1">
          <tr>
            <th colspan="2" style="text-align: left;">Net Invoice Value: <?php echo strtoupper(SmartGeneral::convertToIndianCurrency($this->get("total_amount"))) ?>
            </th>
          </tr>
          <tr>
            <th colspan="2" style="text-align: left;">
              Hub Name :<?php echo $this->get("hub_id") ?></th>
          </tr>
          <tr>
            <th colspan="2" style="text-align: left;">Remarks : <?php echo $this->get("remarks") ?></th>
          </tr>
          <tr>
            <th colspan="2" style="text-align: left;">Banker Details: ICICIBANK,HYDERABAD,ACCOUNTNO:777705120721,IFSCCODE:ICIC0000008.</th>
          </tr>
          <tr>
            <th>Certified that the particulars are true and correct</th>
            <th style=" border-bottom: 0;text-align:center">For TTL ELECTRIC FUEL PRIVATE LIMITED</th>
          </tr>
          <tr>
            <th>Subject to Hyderabad Jurisdiction only </th>
            <th style="border-top: 0;padding-left:40px">
              <br />
              <br />
              <br />
              <br />
              <span style="color:white;">[_APPSIG]</span>
              <p style="text-align:center">Authorized Signatory</p>
            </th>
          </tr>
        </table>
      </div>


      <hr />
      <div style="text-align:center">
        <p>
          Corporate Office Address: <?php echo $this->get("of_add") ?> <br />
          PAN No:<?php echo $this->get("of_pan") ?> CIN No: <?php echo $this->get("off_cin") ?>
        </p>
      </div>

      <?php foreach ($sub_vh_data as $single_vh_data) { ?>
        <div class="page-break"></div>
        <?php echo  VehiclesPdf::getHtml($single_vh_data); ?>
      <?php } ?>

    </body>

    </html>

<?php
    $html = ob_get_contents();
    ob_clean();
    return $html;
  }




  public static function getHtml($data)
  {
    $obj = new self($data);
    return $obj->get_html();
  }
}
