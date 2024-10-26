<?php

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartData as Data;
use Core\Helpers\SmartData;
use Site\Helpers\MeterReadingsHelper;
use Site\Helpers\HubsHelper;


class MeterReadingsController extends BaseController
{

    private MeterReadingsHelper $_helper;
    private HubsHelper $_hubs_helper;

    function __construct($params)
    {
        parent::__construct($params);

        $this->_helper = new MeterReadingsHelper($this->db);

        $this->_hubs_helper = new HubsHelper($this->db);
    }

    /**
     * 
     */
    public function insert()
    {
        $columns = [
            "sd_hub_id",
            "meter_start_date",
            "meter_end_date",
            "meter_start",
            "meter_end"
        ];
        $this->post["sd_hub_id"] = SmartData::post_select_value("sd_hub_id");
        // do validations
        $this->_helper->validate(MeterReadingsHelper::validations, $columns, $this->post);
        $columns[] = "created_by";
        $columns[] = "created_time";
        // insert and get id
        $id = $this->_helper->insert($columns, $this->post);

        //
        $this->response($id);
    }

    public function update()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        $columns = [
            "sd_hub_id",
            "meter_start_date",
            "meter_end_date",
            "meter_start",
            "meter_end"
        ];
        $this->post["sd_hub_id"] = SmartData::post_select_value("sd_hub_id");
        // do validations
        $this->_helper->validate(MeterReadingsHelper::validations, $columns, $this->post);

        // insert and get id
        $this->_helper->update($columns, $this->post, $id);
        //
        $this->responseMsg("updated Successfully");
    }
    /**
     * 
     */



    public function getAll()
    {
        $year = isset($this->post["year"]) ? intval($this->post["year"]) : "";
        if ($year < 0) {
            \CustomErrorHandler::triggerInvalid("Invalid month or date ");
        }
        $out = new \stdClass();
        $hubs = $this->_hubs_helper->getAllData("t1.status=5");
        //$data = $this->_helper->GetAllMeterData($year, $month);
        //  $out = [];
        $dates = [];
        foreach ($hubs as $obj) {
            $obj->meter_data = $this->_helper->getHubData($obj->ID, $year);
            //
            foreach ($obj->meter_data as $_obj) {
                $dates[$_obj->month] = $_obj->month;
                $_obj->meter_reading = intval($_obj->meter_end) - intval($_obj->meter_start);
                $_obj->cms_reading = 0;
                $_obj->deviation =  $_obj->meter_reading > 0 ?  (($_obj->cms_reading - $_obj->meter_reading) / $_obj->meter_reading)  * 100 : 0;
            }
        }
        $out->data = $hubs;
        $out->dates = array_keys($dates);
        $this->response($out);
    }

    public function getAllOld()
    {
        $month = isset($this->post["month"]) ? intval($this->post["month"]) : "";
        $year = isset($this->post["year"]) ? intval($this->post["year"]) : "";
        if ($month < 0 || $year < 0) {
            \CustomErrorHandler::triggerInvalid("Invalid month or date ");
        }
        $data = $this->_helper->GetAllMeterData($year, $month);
        //  $out = [];
        foreach ($data as $obj) {
            $obj->meter_reading = intval($obj->meter_end) - intval($obj->meter_start);
            $obj->cms_reading = 0;
            $obj->deviation =  $obj->meter_reading > 0 ?  (($obj->cms_reading - $obj->meter_reading) / $obj->meter_reading)  * 100 : 0;
        }
        $this->response($data);
    }
}
