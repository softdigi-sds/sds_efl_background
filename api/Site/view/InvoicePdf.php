<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Site\view;
// use Site\images\qr_code;




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
    return isset($this->data[$index]) ? $this->data[$index] : "";
  }

  private function getIndex($dt, $index)
  {
    return isset($dt[$index]) ? $dt[$index] : "";
  }

  private function pget($index)
  {
    $dummy = $this->get($index);
    echo $dummy;
  }

  public function gettable()
  {
    $_data = [];
    ob_start();
    ?>
    <div>

      <h2>Goods Details</h2>
      <table style="width:100%;" border="1">
        <tr>
          <th>Sl.No</th>
          <th>Description of Goods/Services</th>
          <th>HSN/SAC Code</th>
          <th>Quantity</th>
          <th>Unit</th>
          <th>Unit Price (RS)</th>
          <th>Unit Price (RS)</th>
          <th>Freight</th>
          <th>Taxable Amount(RS)</th>
          <th>Total Rate (GST+Cess|State Cess+Cess Non.Advol)</th>
          <th>TCS (Rs)</th>
          <th>Total</th>
        </tr>
        <?php foreach ($_data as $key => $obj) { ?>
          <tr>
            <td><?php echo $key + 1; ?> </td>
            <td><?php echo $this->getIndex($obj, "hsn_info") ?>
              (from <?php echo $this->getIndex($obj, "start_date") ?> to
              <?php echo $this->getIndex($obj, "end_date") ?>)
            </td>
            <td><?php echo $this->getIndex($obj, "quantity") ?> </td>
            <td><?php echo $this->getIndex($obj, "quantity") ?></td>
            <td><?php echo $this->getIndex($obj, "quantity") ?> </td>
            <td><?php echo $this->getIndex($obj, "quantity") ?></td>
            <td><?php echo $this->getIndex($obj, "quantity") ?></td>
            <td><?php echo $this->getIndex($obj, "quantity") ?> </td>
            <td><?php echo $this->getIndex($obj, "quantity") ?></td>
            <td><?php echo $this->getIndex($obj, "quantity") ?></td>
            <td><?php echo $this->getIndex($obj, "quantity") ?> </td>
            <td><?php echo $this->getIndex($obj, "quantity") ?></td>
          </tr>
        <?php } ?>
      </table>
    </div>
    <?php
    $html = ob_get_clean();
    return $html;
  }



  public function get_html()
  {
    ob_start();
    ?>

    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Document</title>
      <style>
        .head{
          display: flex;
        }
      </style>

    </head>

    <body>
      <div>
        <img style="width:170px;" src="Site/view/images/logo.jpeg" alt="logo code">
      </div>
      <div style="text-align:center">
        <table style="width:100%;">
          <tr>
            <td style="width:230px;"></td>
            <td style="text-align: center;"><h3>TAX INVOICE</h3></td>
            <td style="text-align: right; "><h3>ORIGINAL FOR RECIPIENT</h3></td>
          </tr>
        </table>
        <!-- <h3>TAX INVOICE</h3>
        <h3 style="text-align: right;">ORIGINAL FOR RECIPIENT</h3> -->
        
        <h3>(As per Rule 46 of CGST Rules, 2017)</h3>
        <h3>TTL ELECTRIC FUEL PRIVATE LIMITED</h3>
        <h3><?php echo $this->get("address") ?></h>
        <div style="border:1px solid black; margin-bottom: 0;">
          <table style="width:100%;">
            <tr>
              <td style="text-align: left;">
                <p>ACK No:<?php echo $this->get("ack_no") ?></p>
                <p>ACK Date: <?php echo $this->get("ack_date") ?></p>
                <p> IRN No:<?php echo $this->get("irn_no") ?></p>
                <p><?php echo $this->get("additional_info") ?></p>
              </td>
              <td style="text-align: right;">
                <img style="width:150px; text-align: right;" src="Site/view/images/qr_code.jpg" alt="qr code">
              </td>
            </tr>
          </table>
       
      </div>


      <table style="width:100%; margin-top: 0;" border="1">
        <tr>

          <td>
            <p><b>GSTIN:36AAICT7241B1ZH</b></p> <br />
            <p><b>Tax is payable under reverse charge: No</b></p> <br />
            <p><b>Invoice No: <?php echo $this->get("invoice_number") ?></b></p> <br />
            <p><b>Invoice Date: <?php echo $this->get("invoice_date") ?></b></p> <br />
            <p><b>Place of Supply: Delhi</b></p> <br />
            <p><b>Due Date: 09/05/0024</b></p> <br />
          </td>
          <td>

            <p><b>Vehicle No:</b></p> <br />
            <p><b>LR No:</b></p><br />
            <p><b>Transporter:</b></p><br />
            <p><b>Date of Supply: <?php echo $this->get("date_of_supply") ?></b></p><br />
            <p><b>Shipped From: DL</b> </p><br />
            <p><b>Transporter ID:</b></p><br />

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
            <p>REINVENT AGROCHAIN PRIVATE LIMITED,<br />
              Lower Ground Floor, C-10, South Extension Part 2, Delhi 110049,</p><br />
            <p><b>State/State Code: Delhi/07</b></p><br />
            <p><b>GSTIN: 07AALCR6444B1ZE</b></p>
          </td>
          <td>
            <p>REINVENT AGROCHAIN PRIVATE LIMITED,</p><br />
            Lower Ground Floor, C-10, South Extension Part 2, , Delhi110049,</p><br />
            <p><b>State/State Code: Delhi/07</b></p><br />
            <p><b>GSTIN: 07AALCR6444B1ZE</b></p>
          </td>
        </tr>


      </table>
      </div>
      <?php echo $this->gettable() ?>




      <div style="padding-top:10px">
        <br />
        <br />
        <table style="width:100%; " border="1">
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
            <td><?php echo $this->get("tax_amt") ?></td>
            <td><?php echo $this->get("cgst_amt") ?> </td>
            <td><?php echo $this->get("sgst_amt") ?></td>
            <td><?php echo $this->get("igst_amt") ?> </td>
            <td><?php echo $this->get("cees_amt") ?></td>
            <td><?php echo $this->get("state_cees") ?></td>
            <td><?php echo $this->get("roundoff_amt") ?></td>
            <td><?php echo $this->get("other_charge") ?></td>
            <td><?php echo $this->get("total_inv_amt") ?></td>
          </tr>

        </table>
      </div>

  <hr>
      <div style="text-align:center">
        <h2>Corporate Office Address:1-8-303/48/9, TIRUMALA CHAMBERS, PENDERGHAST</h2>
        <h2>ROAD, SINDHI COLONY, BEGUMPET, HYDERABAD, TELANGANA - 500016</h2>
        <h2>PAN No: AAICT7241B CIN No: U74999TG2021PTC153003</h2>
      </div>

      
      <div>
        <table style="width:100% ; border-collapse:collapse" border="1" >
          <tr>
            <th colspan="2"  style="text-align: left;">Net Invoice Value: Ninety Seven Thousand One Hundred Sixty One Rupees and Sixty One Paisa</th>
          </tr>
          <tr>
            <th colspan="2" style="text-align: left;"> Hub Name :SAROOR NAGAR</th>
          </tr>
          <tr>
            <th colspan="2" style="text-align: left;">Remarks :</th>
          </tr>
          <tr>
            <th colspan="2" style="text-align: left;">Banker Details: ICICIBANK,HYDERABAD,ACCOUNTNO:777705120721,IFSCCODE:ICIC0000008.</th>
          </tr>
          <tr>
            <th>Certified that the particulars are true and correct</th>
            <th style=" border-bottom: 0;">For TTL ELECTRIC FUEL PRIVATE LIMITED</th>
          </tr>
          <tr>
            <th>Subject to Hyderabad Jurisdiction only </th>
            <th style=" border-top: 0;">Authorized Signatory</th>
          </tr>
          
        
          </table>
          </div>

          <!-- <tr>
            <td>
              <table>
                <tr>
                  <th>Certified that the particulars are true and correct</th>
                </tr>
                <tr>
                  <th>For TTL ELECTRIC FUEL PRIVATE LIMITED</th>
                </tr>
              </table>
            </td>
            <td>
              <th>Subject to Hyderabad Jurisdiction only </th>
              <th>Authorized Signatory</th>
            </td>
          </tr> -->
        

    </body>

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
