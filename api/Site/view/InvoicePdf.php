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
            <td><?php echo $this->getIndex($obj, "type_desc") ?>
              (from <?php echo $this->get("start_date") ?> to
              <?php echo $this->get("end_date") ?>)
            </td>
            <td><?php echo $this->getIndex($obj, "type_hsn") ?> </td>
            <td><?php echo $this->getIndex($obj, "count") ?></td>
            <td>NOS </td>
            <td><?php echo $this->getIndex($obj, "price") ?></td>
            <td>0.00</td>
            <td>0</td>
            <td><?php echo $this->getIndex($obj, "total") ?></td>
            <td>18.00 + 0 | 0 + 0</td>
            <td>0.0 </td>
            <td><?php echo $this->getIndex($obj, "total") ?></td>
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
    foreach( $sub_vh_data as $single_vh_data){
      $html = VehiclesPdf::getHtml($single_vh_data);
  }
  return $html;
  }



  public function get_html()
  {
    ob_start();
  ?>   
    <html lang="en">

    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>INVOICE PDF</title>
    </head>

    <body>
     

    <?php echo $this->getPartPdf();?>
    
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
