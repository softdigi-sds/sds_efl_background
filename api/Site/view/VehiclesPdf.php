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



  public function get_html()
  {
    $vehicle_data = isset($this->data["sub_data"]) ? $this->data["sub_data"] : [];
    ob_start();
  ?>
  
    <p> hello </p>

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
