<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Site\View;

use Core\Helpers\SmartPdfHelper;
use Core\Helpers\SmartGeneral;
use Site\Helpers\InvoiceHelper;




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

  private function pget($index)
  {
    $dummy = $this->get($index);
    echo $dummy;
  }



  public function get_html()
  {
    ob_start();
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />

      <title>Document</title>

      <style>
        .flex-container>div {
          background-color: #f1f1f1;
          margin: 10px;
          border-radius: 2px;
          padding: 20px;
          font-size: 30px;
        }

        .div {

          align-content: center;
        }

        table {
          font-family: arial, sans-serif;
          border-collapse: collapse;
          width: 80%;
        }

        td,
        th {
          border: 1px solid #dddddd;
          text-align: left;
          padding: 10px;
        }

        table {
          border-collapse: collapse;
          width: 100%;
        }

        th,
        td {
          border-left: 1px solid black;
          /* Left border */
          border-right: 1px solid black;
          /* Right border */
          padding: 8px;
          text-align: left;
        }

        th,
        td {
          border: 1px solid black;
          padding: 8px;
          text-align: left;
        }


        tr:nth-child(2),
        tr:nth-child(3),
        tr:nth-child(4),
        tr:nth-child(5),
        tr:nth-child(6) {
          border: none;
        }

        tr:nth-child(2) th,
        tr:nth-child(3) th,
        tr:nth-child(4) th,
        tr:nth-child(5) th,
        tr:nth-child(6) th {
          border: none;
        }
      </style>

    </head>
    <center>
      <h1>TAX INVOICE</h1>
      <h3 style="text-align: right;">ORIGINAL FOR RECIPIENT</h3>

      <h2>(As per Rule 46 of CGST Rules, 2017)</h2>



      <h2>TTL ELECTRIC FUEL PRIVATE LIMITED</h2>

      <h2>8-2-601,Street No : 10, Banjara hills,HYDERABAD,Delhi-500034</h2>


      <div>




        <div class="flex-container">

          <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=YourDataHere" alt="QR Code" style="width:150px; height:150px; float:right;">


          <img src="c:\Users\Admin\Downloads\QR_code.htm" alt="Image Description" style="float: right; width: 200px; height: auto;">

          <div>
            <p style="text-align: left;">ACK No:112421792792326</p>
            <p style="text-align: left;">ACK Date: 10-09-2024 13:22:00</p>
            <p style="text-align: left;"> IRN No: f0996c5de697a29f3eb0499fa045bd1df0f</p>
            <p style="text-align: left;">d23e558b015db32854e196ecfb62c</p>



          </div>


        </div>

      </div>


      <body>



        <table>
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

          <!-- <tr>
        <th><P>REINVENT AGROCHAIN PRIVATE LIMITED,</P>
            Lower Ground Floor, C-10, South Extension Part 2, , Delhi
            110049,
           <P> State/State Code:Delhi/07</P>
            GSTIN :07AALCR6444B1ZE</th>
        <th><P>REINVENT AGROCHAIN PRIVATE LIMITED,</P>
            Lower Ground Floor, C-10, South Extension Part 2, , Delhi
            110049,
           <P> State/State Code:Delhi/07</P>
           GSTIN :07AALCR6444B1ZE
        </th>
       
      </tr> -->

        </table>

        <div>

          <h2>Goods Details</h2>


          <table style="width:100%">
            <tr>
              <th>Sl.No</th>
              <th>Description of
                Goods/Services</th>
              <th>HSN/SAC
                Code</th>
              <th>Quantity</th>
              <th>Unit</th>
              <th>Unit
                Price
                (RS)</th>
              <th>Unit
                Price
                (RS)</th>
              <th>Freight</th>
              <th>Taxable
                Amount(RS)</th>
              <th>Total Rate
                (GST+Cess|State
                Cess+Cess
                Non.Advol)</th>
              <th>TCS
                (Rs)</th>
              <th>Total</th>
            </tr>
            <tr>
              <td>1</td>
              <td>ELECTRIC
                VEHICLE
                PARKING FEE -
                3WL (from 21-
                07-2024 to 20-
                08-2024)</td>
              <td>996743 </td>
              <td>7.610 </td>
              <td>NOS </td>
              <td>4500.000</td>
              <td>0.00</td>
              <td>0.0 </td>
              <td>34245.00</td>
              <td>18.00 + 0 | 0 + 0</td>
              <td>0.0 </td>
              <td>40409.10</td>



            </tr>
            <tr>
              <td>2</td>
              <td>ADDITIONAL
                CHARGING
                UNITS BILLED
                AS PER SUB
                METER-3WL</td>
              <td>998714</td>
              <td>181.300</td>
              <td>UNT</td>
              <td>15.000</td>
              <td>0.00 </td>
              <td>0.0 </td>
              <td>2719.50</td>
              <td>18.00 + 0 | 0 + 0</td>
              <td>0.0 </td>
              <td>3209.01</td>

            </tr>
            <tr>
              <td>3</td>
              <td>ELECTRIC
                VEHICLE
                PARKING FEE-
                (from 21-07-
                2024 to 20-08-
                2024)-4WL</td>
              <td>996743</td>
              <td>4.000</td>
              <td>NOS</td>
              <td>3000.000</td>
              <td>0.00</td>
              <td>0.0 </td>
              <td>12000.00</td>
              <td>18.00 + 0 | 0 + 0</td>
              <td>0.0 </td>
              <td>14160.00</td>


            </tr>
            <tr>
              <td>4</td>
              <td>ADDITIONAL
                CHARGING
                UNITS BILLED
                AS PER SUB
                METER-4WL</td>
              <td>998714</td>
              <td>2225.060</td>
              <td>UNT</td>
              <td>15.000 </td>
              <td>0.00 </td>
              <td>0.0 </td>
              <td>33375.85</td>
              <td>18.00 + 0 | 0 + 0 </td>
              <td>0.0 </td>
              <td>39383.50</td>


            </tr>

          </table>

        </div>
        <br>
        <br>


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
        </div>



        <br>
        <br>
        <br>
        <hr>
        <center>
          <h2>Corporate Office Address:1-8-303/48/9, TIRUMALA CHAMBERS, PENDERGHAST</h2>
          <h1>ROAD, SINDHI COLONY, BEGUMPET, HYDERABAD, TELANGANA - 500016</h1>
          <h2>PAN No: AAICT7241B CIN No: U74999TG2021PTC153003</h2>
        </center>


        <div>


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


          </table>



        </div>



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
