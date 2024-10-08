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
  
         $data = $this->_helper->getAllData();
    
         $this->response($data);
     }
     
   
  
    }

