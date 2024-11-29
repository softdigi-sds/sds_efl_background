<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Site\view;

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

  public function getParkingInfo()
  {
    $rate_data =  isset($this->data["rate_data"]) ? $this->data["rate_data"] : null;
    if ($rate_data == null) {
      return "";
    }
    $hsn_id = isset($rate_data->sd_hsn_id["value"]) ? $rate_data->sd_hsn_id["value"] : 0;
    // $price = $rate_dat
    if ($hsn_id == 1) {
      return "1) Rate per vehicle " . $rate_data->price . "/-, 2) Unit price " . $rate_data->extra_price . "/- per unit 3) 
      Min units to bill " . $rate_data->min_units_vehicle . " per vehicle";
    } else if ($hsn_id == 2) {
      return "1) Rate per vehicle " . $rate_data->price . "/";
    }
    return "";
  }



  public function get_html()
  {
    $vehicle_data = isset($this->data["sub_data"]) ? $this->data["sub_data"] : [];
    $html = '   
    <table style="width:100%;font-size:11px;"><tr><td colspan="2" style="text-align:center">
    <b>Annexure-' . $this->get("annexure") . '</b></td></tr>
    <tr><td>Invoice No: ' . $this->get("invoice_number") . '</td><td>Description:' . $this->get("type_desc") . '</td></tr></table>
    <table style="width:100%;font-size:11px;  border-collapse: collapse;" border="1">       
        <tr>
          <td>BILLING FOR EV CHARGING</td>
          <td>' . $this->get("vendor_company") . '</td>
          <td colspan="3">' . $this->getParkingInfo() . '</td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td rowspan="2">Charges Per Vehicle Per Month Rate</td>
          <td rowspan="2">Charges PER VEHICLE PER DAY</td>
          <td rowspan="2">TOTAL CHARGES PER DAY</td>
        </tr>
        <tr>
          <td style="text-align:center" >Date</td>
          <td style="text-align:center" >TOTAL COUNT</td>
        </tr>';
    foreach ($vehicle_data as $obj) {
      $html .= '<tr>
            <td style="text-align:center" >' . $obj["date"] . '</td>
            <td style="text-align:center" >' . $obj["count"] . '</td>
            <td style="text-align:center" >' . $obj["charge_month"] . '</td>
            <td style="text-align:center" >' . $obj["charge_per_day"] . '</td>
            <td style="text-align:center" >' . $obj["total"] . '</td>
          </tr>';
    }

    $html .= '
        <tr style="background-color: yellow;">
          <td>Total</td>
          <td>' . $this->get("total_vehicles") . '</td>
          <td></td>
          <td></td>
          <td style="text-align:center">' . $this->get("total_vehicles_charge") . '</td>
        </tr>
         <tr style="background-color: yellow;">
          <td>Avg. no. of vehicles</td>
          <td>' . $this->get("avg_vehicles") . '</td>
          <td></td>
          <td></td>
          <td></td>
        </tr>';
    if ($this->get("type") == 1 && $this->get("extra_units") > 0) {
      $html .= '<tr>
          <td>Max units allowed per Vehicle</td>
          <td>' . $this->get("min_units_vehicle") . '</td>
          <td>Total units allowed</td>
          <td>' . $this->get("units_allowed") . '</td>
          <td style="background-color: yellow;"></td>
        </tr>
        <tr>
          <td>Total Units consumed</td>
          <td>' . $this->get("total_units") . '</td>
          <td>Extra Units consumed</td>
          <td>' . $this->get("extra_units") . '</td>
          <td></td>
        </tr>';
    }

    $html .= '</table>';
    return $html;
  }




  public static function getHtml($data)
  {
    //return "<p>Hello text</p>";
    // var_dump($data);
    //exit();
    $obj = new self($data);
    return $obj->get_html();
  }
}
