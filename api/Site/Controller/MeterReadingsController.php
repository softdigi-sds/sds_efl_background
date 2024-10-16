<?php

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartData as Data;


use Site\Helpers\MeterReadingsHelper;



class MeterReadingsController extends BaseController
{

    private MeterReadingsHelper $_helper;
  
    function __construct($params)
    {
        parent::__construct($params);
    
        $this->_helper = new MeterReadingsHelper($this->db);
     
    

    }

    /**
     * 
     */
    public function insert()
    {
        $columns = ["sd_hub_id","meter_year","meter_month","meter_start",
        "meter_end"];
        // do validations
        $this->_helper->validate(MeterReadingsHelper::validations, $columns, $this->post);
        $columns[] = "created_by";
        $columns[] = "created_time";
        // insert and get id
        $id = $this->_helper->insert($columns, $this->post);

        //
        $this->response($id);
    }
    /**
     * 
     */
   
    
  
     public function getAll()
     {
        $month = isset($this->post["month"]) ? intval($this->post["month"]) : "";
        $year = isset($this->post["year"]) ? intval($this->post["year"]) : "";
        if ($month < 0 || $year < 0) {
            \CustomErrorHandler::triggerInvalid("Invalid month or date ");
        }
        $data = $this->_helper->GetAllMeterData($year,$month);   
      //  $out = [];
        foreach($data as $obj){
            $obj->meter_reading = intval($obj->meter_end) - intval($obj->meter_start);  
            $obj->cms_reading = 0;
            $obj->deviation =  $obj->meter_reading > 0 ?  (($obj->cms_reading - $obj->meter_reading) / $obj->meter_reading )  * 100 : 0;             
        } 
        $this->response($data);
     }
     
   
  
    }

