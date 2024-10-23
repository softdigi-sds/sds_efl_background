<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Site\view;
// use Site\images\qr_code;




class VehiclesPdf
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

  <?php
    $html = ob_get_clean();
    return $html;
  }



  public function get_html()
  {
    $vehicle_data = isset($this->data["sub_data"]) ? $this->data["sub_data"] : [];
    ob_start();
  ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>August Month Invoice WORKBOOK (Autosaved)_UPDATED.xlsm</title>
    </head>

    <body>


      <table style="width:100%;  border-collapse: collapse;" border="1">
        <tr>
        <tr>
          <td style="background-color: chartreuse;">BILLING FOR EV CHARGING</td>
          <td><?php $this->pget("vendor_company")?></td>
          <td colspan="3">1) Parking Fee per vehicle 2200/-, 2) Unit per rate 14/- 3) Min Units
            billing 50 per vehicle</td>

        </tr>
        <tr>
          <td style="background-color: chartreuse;">Aug-24</td>
          <td>SAPE</td>
          <td rowspan="2">Charges Per Vehicle Per Month Rate</td>
          <td rowspan="2">Charges  PER VEHICLE PER DAY</td>
          <td rowspan="2">TOTAL CHARGES PER DAY</td>
        </tr>
        <tr>
          <td>Date</td>
          <td>TOTAL COUNT</td>
        </tr>
        </tr>
        <tbody>
          <?php foreach ($vehicle_data as $obj) { ?>
            <tr>
              <td><?php echo $obj->date?></td>
              <td><?php echo $obj->count?></td>
              <td><?php echo $obj->charge_month?></td>
              <td><?php echo $obj->charge_per_day?></td>
              <td><?php echo $obj->total ?></td>
            </tr>
          <?php } ?>
          <tr style="background-color: yellow;">
            <td>Total</td>
            <td><?php  $this->pget("total_vehicles")?></td>
            <td></td>
            <td></td>
            <td><?php $this->pget("total_vehicles_charge")?></td>
          </tr>
          <tr>
            <td>Max units allowed per Vehicle</td>
            <td><?php echo $this->pget("min_units_vehicle")?></td>
            <td>Total units allowed</td>
            <td><?php  $this->pget("units_allowed")?></td>
            <td style="background-color: yellow;"></td>
          </tr>
          <tr>
            <td>Avg. no. of vehicles</td>
            <td><?php  $this->pget("avg_vehicles")?></td>
            <td>Total Units consumed</td>
            <td></td>
            <td><?php $this->pget("total_units")?></td>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <td style="background-color: yellow;">Extra Units consumed</td>
            <td style="background-color: yellow;"><?php $this->pget("extra_units")?></td>
            <td style="background-color: yellow;">-</td>
          </tr>
        </tbody>
      </table>

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
