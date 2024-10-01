<?php

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartData as Data;
use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartExcellHelper;
use Core\Helpers\SmartFileHelper;
use Site\Helpers\EflVehiclesHelper;
use Site\Helpers\ImportHelper;
use Site\Helpers\VendorsHelper;



class EflVehiclesController extends BaseController
{

    private EflVehiclesHelper $_helper;
    private ImportHelper $_import_helper;
    private VendorsHelper $_vendor_helper;
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new EflVehiclesHelper($this->db);
        //
        $this->_import_helper = new ImportHelper($this->db);
        //
        $this->_vendor_helper = new VendorsHelper($this->db);
    }

    /**
     * 
     */
    public function insert()
    {

        $consump_data = Data::post_array_data("input_data");
        $hub_id = Data::post_select_value("hub_id");
        // echo "id = " .   $hub_id;
        //exit();
        $date = Data::post_data("date", "STRING");
        if ($hub_id < 1) {
            \CustomErrorHandler::triggerInvalid("Please Select Hub ID");
        }
        if (empty($consump_data)) {
            \CustomErrorHandler::triggerInvalid("Provided data dosen't contain any information !!");
        }
        $insert_columns = ["sd_hub_id", "sd_vendors_id", "sd_date", "vehicle_count", "created_by", "created_time"];
        $update_columns = ["vehicle_count", "last_modified_by", "last_modified_time"];
        foreach ($consump_data as $data) {
            $data["sd_hub_id"] = $hub_id;
            $data["sd_date"] =  $date;
            $this->_helper->insertUpdate($data, $insert_columns, $update_columns);
        }
        $this->responseMsg(msg: "Parking Report has been appended successfully");
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
        $columns = ["sd_hub_id", "sd_vendors_id", "sd_date", "vehicle_count"];
        // do validations
        $this->_helper->validate(EflVehiclesHelper::validations, $columns, $this->post);
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
    public function getOneParkingData()
    {

        // $id = isset($this->post["hub_id"]) ? $this->post["hub_id"] : 0;
        $hub_id = Data::post_select_value("hub_id");
        $date = isset($this->post["date"]) ? trim($this->post["date"]) : "";
        if ($hub_id  < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid Hub ID");
        }
        if (strlen($date) < 0) {
            \CustomErrorHandler::triggerInvalid("Invalid date ");
        }
        $data = $this->_helper->getVendorsByHubId($hub_id, $date);
        $this->response($data);
    }


    /**
     * 
     */
    public function getAllParkingData()
    {

        $hub_id =  Data::post_select_value("hub_id");
        //isset($this->post["hub_id"]) ? $this->post["hub_id"] : 0;
        $month = isset($this->post["month"]) ? intval($this->post["month"]) : "";
        $year = isset($this->post["year"]) ? intval($this->post["year"]) : "";
        if ($hub_id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid Hub ID");
        }
        if ($month < 0 || $year < 0) {
            \CustomErrorHandler::triggerInvalid("Invalid month or date ");
        }
        // $hub_id = Data::post_select_value($id);
        $data = $this->_helper->getCountByHubAndDate($hub_id, $month, $year);
        $this->response($data);
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
        $_data = $excel->getData($this->_import_helper->importColumnsVehicleCount(), 2);
        $out = [];
        foreach ($_data as $obj) {
            $vendor_data = $this->_vendor_helper->checkVendorByCodeCompany($obj["vendor"], "##");
            if ($obj["vendor"] == "" || $obj["date"] == "") {
                $obj["status"] = 10;
                $obj["msg"] = "Improper Data";
            } else {
                if (isset($vendor_data->ID)) {
                    // vendor existed insert or update the data
                    $_vehicle_data = [
                        "sd_hub_id" => $vendor_data->sd_hub_id,
                        "sd_vendors_id" => $vendor_data->ID,
                        "sd_date" => $obj["date"],
                        "vehicle_count" => $obj["count"]
                    ];
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
