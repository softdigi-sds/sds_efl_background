<?php

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartData as Data;
use Core\Helpers\SmartData;
use Core\Helpers\SmartDateHelper;
use Core\Helpers\SmartExcellHelper;
use Core\Helpers\SmartFileHelper;
use Site\Helpers\EflConsumptionHelper;
use Site\Helpers\ImportHelper;
use Site\Helpers\VendorsHelper;
use Site\Helpers\MeterTypesHelper;
use Site\Helpers\HubsHelper;
use Site\Helpers\VendorRateHelper;

class EflConsumptionController extends BaseController
{

    private EflConsumptionHelper $_helper;
    private ImportHelper $_import_helper;
    private VendorsHelper $_vendor_helper;
    private MeterTypesHelper $_meterTypesHelper;
    private HubsHelper $_hubs_helper;
    private VendorRateHelper $_vendor_rate_helper;
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new EflConsumptionHelper($this->db);
        $this->_import_helper = new ImportHelper($this->db);
        //
        $this->_vendor_helper = new VendorsHelper($this->db);
        //
        $this->_meterTypesHelper = new MeterTypesHelper($this->db);

        $this->_hubs_helper = new HubsHelper($this->db);
        //
        $this->_vendor_rate_helper = new VendorRateHelper($this->db);
    }

    /**
     * 
     */
    public function insert()
    {

        $consump_data = Data::post_array_data("data");
        $hub_id = Data::post_select_value("hub_id");
        $date = Data::post_data("date", "STRING");
        foreach ($consump_data as $data) {
            $data["sd_hub_id"] = $hub_id;
            $data["sd_date"] =  $date;
            // if (isset($data["sd_hub_id"])) {
            $this->_helper->insertUpdateNew($data);
            // }
        }
        $this->responseMsg(msg: "Consumption Report has been appended successfully");
    }
    /**
     * 
     */
    public function update()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        $columns = ["sd_hub_id", "sd_vendors_id", "sd_date", "unit_count"];
        // do validations
        $this->_helper->validate(EflConsumptionHelper::validations, $columns, $this->post);
        // extra columns
        $columns[] = "last_modified_by";
        $columns[] = "last_modified_time";
        // begin transition
        $this->db->_db->Begin();
        // insert and get id
        $id = $this->_helper->update($columns, $this->post, $id);
        $this->db->_db->commit();
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
    /**
     * 
     */
    public function deleteOne()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        // insert and get id
        $this->_helper->deleteOneId($id);
        $out = new \stdClass();
        $out->msg = "Removed Successfully";
        $this->response($out);
    }
    /**
     * 
     */
    public function getOneConsumptionData()
    {
        $hub_id = Data::post_select_value("hub_id");
        $date = isset($this->post["date"]) ? trim($this->post["date"]) : "";
        if ($hub_id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid Hub ID");
        }
        if (strlen($date) < 0) {
            \CustomErrorHandler::triggerInvalid("Invalid date ");
        }
        $types = $this->_meterTypesHelper->getAllData();
        // $hub_id = Data::post_select_value($hub_id);
        $data = $this->_helper->getVendorsByHubId($hub_id, $date);
        foreach ($data as $obj) {
            $_db_out = $this->_helper->ConsumptionTypeCount($obj->ID);
            $obj->sub_data =  is_array($_db_out) ? $_db_out : [];
            //$out[] = $obj;
        }
        $out = new \stdClass();
        $out->data = $data;
        $out->types = $types;
        $this->response($out);
    }

    public function getOneConsumptionDataHub()
    {
        $hub_id = Data::post_select_value("hub_id");
        $start_date = SmartData::post_data("date", "DATE");
        $end_date = SmartData::post_data("end_date", "DATE");      
        if ($hub_id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid Hub ID");
        }      
        $types = $this->_meterTypesHelper->getAllData();
        $vendor_data = $this->_vendor_rate_helper->getAllWithHubId($hub_id);          
        foreach ( $vendor_data as $obj) {
            $_db_out = $this->_helper->getConsumptionInvoiceByDateVendor($hub_id,$obj->sd_customer_id,$start_date,$end_date);
            //var_dump($_db_out);
            $obj->sub_data =  is_array($_db_out) ? $_db_out : [];
            //$out[] = $obj;
        }
        //exit();
        $out = new \stdClass();
        $out->data =  $vendor_data ;
        $out->types = $types;
        $this->response($out);
    }
    /**
     * 
     */
    public function getAllConsumptionData()
    {
        $hub_id = Data::post_select_value("hub_id");
        // $id = isset($this->post["hub_id"]) ? $this->post["hub_id"] : 0;
        $month = isset($this->post["month"]) ? intval($this->post["month"]) : "";
        $year = isset($this->post["year"]) ? intval($this->post["year"]) : "";
        if ($hub_id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid Hub ID");
        }
        if ($month < 0 || $year < 0) {
            \CustomErrorHandler::triggerInvalid("Invalid month or date ");
        }

        // echo $hub_id;exit();    
        $data = $this->_helper->getCountByHubAndDate($hub_id, $month, $year);
        $this->response($data);
    }


    public function getAllConsumptionHubWise()
    {
        $start_date = SmartData::post_data("start_date", "DATE");
        $end_date = SmartData::post_data("end_date", "DATE");
        // get hub details first
        $hubs = $this->_hubs_helper->getAllData("t1.status=5");
        $dates = SmartDateHelper::getDatesBetween($start_date, $end_date);
        // loop over and get sub data   
        foreach ($hubs as $obj) {
            $obj->sub_data = $this->_helper->getCountByHubAndStartEndDate($obj->ID,  $start_date,  $end_date);
            $obj->total = $this->_helper->hubTotal($obj->sub_data);
            $obj->average = count($dates) > 0 ? round($obj->total / count($dates), 2) : 0;
            // $hubs[$key] = $obj;
        }
        $out = new \stdClass();
        $out->dates =  $dates;
        $out->data = $hubs;
        //return $hubs;
        $this->response($out);
    }

    private function prepare_sub_object($count, $type_id)
    {
        $arr = [
            "sd_meter_types_id" => $type_id,
            "count" => $count
        ];
        return $arr;
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
        $insert_id = $this->_import_helper->insertData("CONSUMPTION");
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
        $dates = [];
        for ($i = 2; $i <= $excel->get_last_row(); $i++) {
            $obj = [
                "hub_name" => $excel->get_cell_value("F", $i),
                "vendor" => $excel->get_cell_value("AG", $i),
                "point_type" => $excel->get_cell_value("L", $i),
                "date" => $excel->getDate($excel->get_cell_value("N", $i)),
                "count" => $excel->get_cell_value("V", $i),
            ];
            // var_dump($obj);
            if ($obj["vendor"] == "" || $obj["date"] == "") {
                $obj["status"] = 10;
                $obj["msg"] = "Improper Data";
                $out[$obj["vendor"]] = $obj;
            } else {
                $rate_data = $this->_vendor_rate_helper->getOneWithHubNamAndCustomerName($obj["hub_name"], $obj["vendor"]);
                // var_dump($rate_data);
                if (isset($rate_data->ID)) {
                    // vendor existed insert or update the data
                    $index = $rate_data->ID . "_" . $obj["date"];
                    $prev_count = isset($dates[$index]) ? $dates[$index] : 0;
                    $new_count =  $prev_count  + floatval($obj["count"]);
                    $type = isset($obj["point_type"]) && $obj["point_type"] == "DC" ? 2 : 1;
                    $sub_data = [
                        $this->prepare_sub_object($new_count, 1, $type),
                    ];
                    $_vehicle_data = [
                        "sd_hub_id" => $rate_data->sd_hubs_id,
                        "sd_customer_id" => $rate_data->sd_customer_id,
                        "sd_date" => $obj["date"],
                        "unit_count" =>  $new_count,
                        "sub_data" => $sub_data
                    ];
                    $dates[$index] =  $new_count;
                    $this->_helper->insertUpdateNew($_vehicle_data);
                    $obj["status"] = 5;
                } else {
                    $obj["status"] = 10;
                    $obj["msg"] = "Customer With Hub Is Not Existed or Not Linked";
                    $out[$obj["vendor"]]  = $obj;
                }
            }
        }
        //var_dump($out);
        $this->response(array_values($out));
    }

    public function importCmsExcel()
    {
        // path
        $dest_path = "C:/Users/KMS/Downloads/dummy.xlsx";
        // read the excel and process
        $excel = new SmartExcellHelper($dest_path, 0);
        $_data = $excel->getData($this->_import_helper->importCmsColumns(), 2);
        foreach ($_data as $obj) {
            $this->_helper->insertCmsData($obj);
        }
        $this->response("done");
    }
}
