<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Site\View;





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

  private function getIndex($dt,$index)
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
      <table style="width:100%">
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
        <?php foreach($_data as $key=>$obj) { ?>
        <tr>
          <td><?php echo $key+1; ?> </td>
          <td><?php echo $this->getIndex($obj,"hsn_info") ?>
           (from <?php echo $this->getIndex($obj,"start_date") ?> to
            <?php echo $this->getIndex($obj,"end_date") ?>)</td>
          <td><?php echo $this->getIndex($obj,"quantity")?> </td>
          <td><?php echo $this->getIndex($obj,"quantity")?></td>
          <td><?php echo $this->getIndex($obj,"quantity")?> </td>
          <td><?php echo $this->getIndex($obj,"quantity")?></td>
          <td><?php echo $this->getIndex($obj,"quantity")?></td>
          <td><?php echo $this->getIndex($obj,"quantity")?> </td>
          <td><?php echo $this->getIndex($obj,"quantity")?></td>
          <td><?php echo $this->getIndex($obj,"quantity")?></td>
          <td><?php echo $this->getIndex($obj,"quantity")?> </td>
          <td><?php echo $this->getIndex($obj,"quantity")?></td>
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

      </style>

    </head>

    <body>
      <div style="text-align:center">
        <h1>TAX INVOICE</h1>
        <h3 style="text-align: right;">ORIGINAL FOR RECIPIENT</h3>
        <h2>(As per Rule 46 of CGST Rules, 2017)</h2>
        <h2>TTL ELECTRIC FUEL PRIVATE LIMITED</h2>
        <h2>8-2-601,Street No : 10, Banjara hills,HYDERABAD,Delhi-500034</h2>
        <div>
          <div class="flex-container">
            
            <div>
              <p style="text-align: left;">ACK No:112421792792326</p>
              <p style="text-align: left;">ACK Date: 10-09-2024 13:22:00</p>
              <p style="text-align: left;"> IRN No: f0996c5de697a29f3eb0499fa045bd1df0f</p>
              <p style="text-align: left;">d23e558b015db32854e196ecfb62c</p>
            </div>
          </div>
        </div>
      </div>

      <table style="width:100%">
        <tr>
          <th>GSTIN:36AAICT7241B1ZH</th>
          <th>Vehicle No:</th>
        </tr>
        <tr>
          <th>Tax is payable under reverse charge: No</th>
          <th>LR No:</th>
        </tr>
        <tr>
          <th>Invoice No: EFL/TS/428/24-25</th>
          <th>Transporter:</th>
        </tr>
        <tr>
          <th>Invoice Date: 10/09/2024</th>
          <th>Date of Supply: 10/09/2024</th>
        </tr>
        <tr>
          <th>Place of Supply: Delhi</th>
          <th>Shipped From: DL</th>
        </tr>
        <tr>
          <th>Due Date: 09/05/0024</th>
          <th>Transporter ID:</th>
        </tr>
        <tr>
          <th>Receiver (Billed to)</th>
          <th>Consignee (Shipped to)</th>
        </tr>
        <tr>
          <th>
            <p>REINVENT AGROCHAIN PRIVATE LIMITED,</p>
            Lower Ground Floor, C-10, South Extension Part 2, Delhi 110049,
            <p>State/State Code: Delhi/07</p>
            GSTIN: 07AALCR6444B1ZE
          </th>
          <th>
            <p>REINVENT AGROCHAIN PRIVATE LIMITED,</p>
            Lower Ground Floor, C-10, South Extension Part 2, Delhi 110049,
            <p>State/State Code: Delhi/07</p>
            GSTIN: 07AALCR6444B1ZE
          </th>
        </tr>
      </table>

      <table>
        <tr>
          <!-- <th>Receiver (Billed to) </th> -->
          <th>Consignee (Shipped to)</th>
        </tr>
      </table>
      <?php echo $this->gettable() ?>

      <div>

        <table style="width:100%">
          <tr>
            <th>Tax'ble Amt</th>
            <th>CGST Amt</th>
            <th>SGST Amt </th>
            <th>IGST Amt</th>
            <th>CESS Amt</th>
            <th>State CESS
              Amt</th>
            <th>Round off
              Amt</th>
            <th>Other
              Charges</th>
            <th>Total Inv.
              Amt</th>


          </tr>
          <tr>
            <td>82340.35 </td>
            <td>0.00 </td>
            <td>0.00 </td>
            <td>14821.26 </td>
            <td>0.00</td>
            <td>0.00</td>
            <td>0</td>
            <td>0.00</td>
            <td>97161.61</td>
          </tr>

        </table>


        <br>
        <br>
        <br>
        <hr>
        <div style="text-align:center">
          <h2>Corporate Office Address:1-8-303/48/9, TIRUMALA CHAMBERS, PENDERGHAST</h2>
          <h1>ROAD, SINDHI COLONY, BEGUMPET, HYDERABAD, TELANGANA - 500016</h1>
          <h2>PAN No: AAICT7241B CIN No: U74999TG2021PTC153003</h2>
        </div>
      </div>

      <table style="width:100%">
        <tr>
          <th colspan="2">Net Invoice Value: Ninety Seven Thousand One Hundred Sixty One Rupees and Sixty One Paisa</th>
        </tr>
        <tr>
          <td colspan="2"> Hub Name :SAROOR NAGAR</td>
        </tr>
        <tr>
          <td colspan="2">Remarks :</td>
        </tr>
        <tr>
          <td>Banker Details: ICICIBANK,HYDERABAD,ACCOUNTNO:777705120721,IFSCCODE:ICIC0000008.</td>
        </tr>
        <table>
          <tr>
            <th>Certified that the particulars are true and correct </th>
            <th>For TTL ELECTRIC FUEL PRIVATE LIMITED
              <p>Authorized Signatory</p>
            </th>
          </tr>
          <tr>
            <th>Subject to Hyderabad Jurisdiction only </th>
          </tr>
        </table>


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
