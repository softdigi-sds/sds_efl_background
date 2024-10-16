<?php

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartData as Data;
use Core\Helpers\SmartExcellHelper;
use Core\Helpers\SmartFileHelper;
use Site\Helpers\EflConsumptionHelper;
use Site\Helpers\ImportHelper;
use Site\Helpers\VendorsHelper;
use Site\Helpers\MeterTypesHelper;

class EflConsumptionController extends BaseController
{

    private EflConsumptionHelper $_helper;
    private ImportHelper $_import_helper;
    private VendorsHelper $_vendor_helper;
    private MeterTypesHelper $_meterTypesHelper;
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

    }

    /**
     * 
     */
    public function insert()
    {

        $consump_data = Data::post_array_data("data");
        $hub_id = Data::post_select_value("hub_id");
        // echo "id = " .   $hub_id;
        //exit();
        $date = Data::post_data("date", "STRING");

        $insert_columns = ["sd_hub_id", "sd_vendors_id", "sd_date", "unit_count", "created_by", "created_time"];
        $update_columns = ["unit_count", "last_modified_by", "last_modified_time"];
        foreach ($consump_data as $data) {
            $data["sd_hub_id"] = $hub_id;
            $data["sd_date"] =  $date;
            // if (isset($data["sd_hub_id"])) {
            $this->_helper->insertUpdate($data, $insert_columns, $update_columns);
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
            $obj->sub_data =  is_array($_db_out) ? $_db_out : [] ;
            //$out[] = $obj;
        }
        $out = new \stdClass();
        $out->data = $data;
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

    private function prepare_sub_object($count,$type_id){
        $arr = [           
            "sd_meter_types_id"=>$type_id,
            "count"=>$count
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
        $_data = $excel->getData($this->_import_helper->importConsumptionColumns(), 2);
        $out = [];
        $dates = [];
        foreach ($_data as $obj) {
            $vendor_data = $this->_vendor_helper->checkVendorByCodeCompany("", $obj["vendor"]);
            if ($obj["vendor"] == "" || $obj["date"] == "") {
                $obj["status"] = 10;
                $obj["msg"] = "Improper Data";
            } else {
                if (isset($vendor_data->ID)) {
                    // vendor existed insert or update the data
                    $index = $vendor_data->ID . "_" . $obj["date"];
                    $prev_count = isset($dates[$index]) ? $dates[$index] : 0;
                    $new_count =  $prev_count  + $obj["count"];  
                    $type = isset($obj["point_type"]) && $obj["point_type"]=="DC" ? 2 : 1;
                    $sub_data = [
                        $this->prepare_sub_object($new_count,1, $type),                        
                    ]; 
                    $_vehicle_data = [
                        "sd_hub_id" => $vendor_data->sd_hub_id,
                        "sd_vendors_id" => $vendor_data->ID,
                        "sd_date" => $obj["date"],
                        "unit_count" =>  $new_count,
                        "sub_data"=>$sub_data
                    ];
                 
                    $dates[$index] =  $new_count;
                    $this->_helper->insertUpdateNew($_vehicle_data);
                    $obj["status"] = 5;
                } else {
                    $obj["status"] = 10;
                    $obj["msg"] = "Vendor Code Not Existed";
                }
            }
            $out[] = $obj;
        }
        $this->response($out);
    }
}
