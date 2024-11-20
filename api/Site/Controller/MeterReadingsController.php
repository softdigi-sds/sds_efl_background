<?php

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartData as Data;
use Core\Helpers\SmartData;
use Core\Helpers\SmartExcellHelper;
use Core\Helpers\SmartFileHelper;
use Site\Helpers\MeterReadingsHelper;
use Site\Helpers\HubsHelper;
use Site\Helpers\EflConsumptionHelper;
use Site\Helpers\ImportHelper;

class MeterReadingsController extends BaseController
{

    private MeterReadingsHelper $_helper;
    private HubsHelper $_hubs_helper;
    private EflConsumptionHelper $_consumption_helper;
    private ImportHelper $_import_helper;

    function __construct($params)
    {
        parent::__construct($params);

        $this->_helper = new MeterReadingsHelper($this->db);

        $this->_hubs_helper = new HubsHelper($this->db);
        //
        $this->_consumption_helper = new EflConsumptionHelper($this->db);
        $this->_import_helper = new ImportHelper($this->db);

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
        $columns[] = "meter_cost";
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
        $columns[] = "meter_cost";
        // insert and get id
        $this->_helper->update($columns, $this->post, $id);
        //
        $this->responseMsg("updated Successfully");
    }
    /**
     * 
     */

     public function getOne()
     {
         $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
         if ($id < 1) {
             \CustomErrorHandler::triggerInvalid("Invalid ID");
         }
         // insert and get id
         $data = $this->_helper->getOneData($id);
         $this->response($data);
     }


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
           // var_dump($obj->meter_data);
            foreach ($obj->meter_data as $_obj) {
               // var_dump($_obj);
               // echo "<br/><br/>";
                $dates[$_obj->month] = $_obj->month;
                $_obj->meter_reading = intval($_obj->meter_end) - intval($_obj->meter_start);
               // echo "start_date " . $obj->meter_start_date . " end_date " . $_obj->meter_end_date . " <br/>";
              // echo  $_obj->meter_end_date;
                $_sub_data =  $this->_consumption_helper->getCountByHubAndStartEndDate($obj->ID,  $_obj->meter_start_date,  $_obj->meter_end_date);
               // var_dump($_sub_data);
                // $obj->total = 
                $_obj->cms_reading =  round($this->_consumption_helper->hubTotal($_sub_data));
                $_obj->deviation =  $_obj->meter_reading > 0 ?  (($_obj->cms_reading - $_obj->meter_reading) / $_obj->meter_reading)  * 100 : 0;
                $_obj->deviation = round( $_obj->deviation,2);

            }
        }
        $out->data = $hubs;
        $out->dates = array_keys($dates);
        $this->response($out);
    }


    public function importExcel()
    {
        $excel_import = Data::post_array_data("excel");
        if (!is_array($excel_import) || count($excel_import) < 1) {
            \CustomErrorHandler::triggerInvalid("Please upload Excel to Import");
        }
        // get the excel content
        $content = isset($excel_import["content"]) ? $excel_import["content"] : "";
        if (strlen($content) < 10) {
            \CustomErrorHandler::triggerInvalid("Please upload Excel to Import");
        }
        //
        $insert_id = $this->_import_helper->insertData("VEHICLES");
        // excel path 
        $store_path = "excel_import" . DS . $insert_id . DS . "import.xlsx";
        // 
        $dest_path = SmartFileHelper::storeFile($content, $store_path);
        // 
        $this->_import_helper->updatePath($insert_id, $store_path);
        // read the excel and process      
        $excel = new SmartExcellHelper($dest_path, 0);
        $excel->init_excel();
        $out = [];
        for ($i = 2; $i <= $excel->get_last_row(); $i++) {
            $obj = [
                "hub_name" => $excel->get_cell_value("B", $i),
                "start_date" => $excel->getDate($excel->get_cell_value("C", $i)),
                "end_date" => $excel->getDate($excel->get_cell_value("D", $i)),
                "meter_start" => $excel->get_cell_value("E", $i),
                "meter_end" => $excel->get_cell_value("F", $i),
                "meter_cost" => $excel->get_cell_value("G", $i),
            ];
            if ($obj["hub_name"] == "" || $obj["start_date"] == "" || $obj["end_date"] == "") {
                $obj["status"] = 10;
                $obj["msg"] = "Improper Data";
            } else {
                $_data = $this->_hubs_helper->checkHubExist($obj["hub_name"]);
                if (isset($_data->ID)) {
                    // vendor existed insert or update the data                    
                    $_m_data = [
                        "sd_hub_id" =>  $_data->ID,
                        "meter_start_date" =>  $obj["start_date"],
                        "meter_end_date" => $obj["end_date"],
                        "meter_start" => $obj["meter_start"],
                        "meter_end" => $obj["meter_end"],
                        "meter_cost" => $obj["meter_cost"],
                    ];
                    $this->_helper->insertUpdate($_m_data);
                    $obj["status"] = 5;
                } else {
                    $obj["status"] = 10;
                    $obj["msg"] = "Hub Is Not Existed ";
                    $out[$obj["hub_name"]] = $obj;
                }
            }
        }
        $this->response(array_values($out));
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
