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
            <td>AMPLUS-3W</td>
            <td colspan="3">1) Parking Fee per vehicle 2200/-, 2) Unit per rate 14/- 3) Min Units
                billing 50 per vehicle</td>
       
        </tr>
        <tr>
            <td style="background-color: chartreuse;">Aug-24</td>
            <td>SAPE</td>
            <td rowspan="2">Charges Per Vehicle
                Per Month Rate</td>
            <td rowspan="2">Charges
                PER VEHICLE
                PER DAY</td>
            <td rowspan="2">TOTAL CHARGES
                PER DAY</td>
            
        </tr>
        <tr>
            <td>Date</td>
            <td>TOTAL COUNT</td>
        </tr>
  
    </tr>
        <tbody>
            <tr><td>21-Aug-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>22-Aug-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>23-Aug-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>24-Aug-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>25-Aug-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>26-Aug-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>27-Aug-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>28-Aug-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>29-Aug-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>30-Aug-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>31-Aug-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>1-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>2-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>3-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>4-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>5-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>6-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>7-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>8-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>9-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>10-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>11-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>13-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td> 922.58</td></tr>
            <tr><td>14-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>15-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>16-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>17-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>18-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>19-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td>20-Sep-2024</td><td>12</td><td>2200</td><td>70.97</td><td>851.61</td></tr>
            <tr><td></td><td></td><td></td><td></td><td></td></tr>
            <tr style="background-color: yellow;"><td>Total</td><td>373</td><td></td><td></td><td>26,470.97</td></tr>
            <tr><td>Max units allowed per Vehicle</td><td>0</td><td>Total units consumed</td><td></td><td style="background-color: yellow;"></td></tr>
            <tr><td>Avg. no. of vehicles</td><td></td><td>Total Units consumed</td><td></td><td>0.00</td></tr>
            <tr><td></td><td></td><td style="background-color: yellow;">Extra Units consumed</td><td style="background-color: yellow;"></td><td style="background-color: yellow;">-</td></tr>
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
