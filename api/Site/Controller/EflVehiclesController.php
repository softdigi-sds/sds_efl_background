<?php

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartData as Data;
use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartData;
use Core\Helpers\SmartExcellHelper;
use Core\Helpers\SmartFileHelper;
use Core\Helpers\SmartDateHelper;
use Site\Helpers\EflVehiclesHelper;
use Site\Helpers\ImportHelper;
use Site\Helpers\VendorsHelper;
use Site\Helpers\VendorRateHelper;
//use Site\view\VehiclesPdf;
use Core\Helpers\SmartPdfHelper;
use Site\Helpers\HubsHelper;
use Site\Helpers\VehiclesTypesHelper;


class EflVehiclesController extends BaseController
{

    private EflVehiclesHelper $_helper;
    private ImportHelper $_import_helper;
    private VendorsHelper $_vendor_helper;
    private VendorRateHelper $_vendor_rate_helper;
    //private VehiclesPdf $_vehicles_pdf_helper;
    private VehiclesTypesHelper $_vehiclesTypesHelper;
    private HubsHelper $_hubs_helper;

    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new EflVehiclesHelper($this->db);
        //
        $this->_import_helper = new ImportHelper($this->db);
        //
        $this->_vendor_helper = new VendorsHelper($this->db);
        //
        $this->_vendor_rate_helper = new VendorRateHelper($this->db);

        // $this->_vehicles_pdf_helper = new VehiclesPdf($this->db);
        //
        $this->_vehiclesTypesHelper = new VehiclesTypesHelper($this->db);
        //
        $this->_hubs_helper = new HubsHelper($this->db);
    }

    /**
     * 
     */
    public function insert()
    {

        $_data = Data::post_array_data("input_data");
        $hub_id = Data::post_select_value("hub_id");
        // echo "id = " .   $hub_id;
        //exit();
        $date = Data::post_data("date", "STRING");
        if ($hub_id < 1) {
            \CustomErrorHandler::triggerInvalid("Please Select Hub ID");
        }
        if (empty($_data)) {
            \CustomErrorHandler::triggerInvalid("Provided data dosen't contain any information !!");
        }
        foreach ($_data as $data) {
            $data["sd_hub_id"] = $hub_id;
            $data["sd_date"] =  $date;
            $this->_helper->insertUpdateNew($data);
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

    private function get_one_vehicle_type_count($_data, $type_id)
    {
        foreach ($_data as $obj) {
            if ($obj->sd_vehicle_types_id == $type_id) {
                return $obj->count;
            }
        }
        return 0;
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
        // $out = [];
        $types = $this->_vehiclesTypesHelper->getAllData();
        foreach ($data as $obj) {
            $_db_out = $this->_helper->vehicleTypeCount($obj->ID);
            $obj->sub_data =  is_array($_db_out) ? $_db_out : [];
            //$out[] = $obj;
        }
        $out = new \stdClass();
        $out->data = $data;
        $out->types = $types;
        // var_dump($out);
        $this->response($out);
    }
    /**
     *  function to get parking count with vendor and start and end data with hub id
     * 
     */
    public function getOneParkingDataHub()
    {
        // $id = isset($this->post["hub_id"]) ? $this->post["hub_id"] : 0;
        $hub_id = Data::post_select_value("hub_id");     
        $start_date = SmartData::post_data("date", "DATE");
        $end_date = SmartData::post_data("end_date", "DATE");
        if ($hub_id  < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid Hub ID");
        }      
        $vendor_data = $this->_vendor_rate_helper->getAllWithHubId($hub_id);
        $types = $this->_vehiclesTypesHelper->getAllData();        
        foreach ( $vendor_data as $obj) {
            $_db_out = $this->_helper->getVehicleInvoiceByDateVendor($hub_id,$obj->sd_customer_id,$start_date,$end_date,true);
            $obj->sub_data =  is_array($_db_out) ? $_db_out : [];
            //$out[] = $obj;
        }
       $out = new \stdClass();
       $out->data =$vendor_data;
       $out->types = $types;
       // var_dump($out);
        $this->response($out);
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

    public function getAllParkingHubWise()
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
    /**
     *  function gets the count of vehicle report for hubs whether the data is updated or not
     * 
     * 
     */
    public function getDashBoardHubWise()
    {
        $start_date = SmartData::post_data("start_date", "DATE");
        $end_date = SmartData::post_data("end_date", "DATE");
        $dates = SmartDateHelper::getDatesBetween($start_date, $end_date);
        $hubs = $this->_hubs_helper->getAllData("t1.status=5");
        $out = [];
        foreach ($dates as $date_single) {
            $hub_data = $this->_helper->getHubViseVehicleWithDate($date_single);
            $obj = new \stdClass();
            $obj->date = $date_single;
            $hub_count = is_array($hub_data) ? count($hub_data) : 0;
            if ($hub_count === $hubs) {
                $obj->status = 3; //  nothing is updated
            } else if ($hub_count == 0) {
                $obj->status = 1; //  all is updated
            } else {
                $obj->status = 2; // partial updated
            }
            $out[] = $obj;
        }
        $this->response($out);
    }

    /**
     * given a date which hubs not updated vehicle report is coming here
     */
    public function getDashBoardHubWiseDate()
    {
        $start_date = SmartData::post_data("start_date", "DATE");
        $hub_data = $this->_helper->getHubViseVehicleWithDate($start_date);
        $this->response($hub_data);
    }


    private function prepare_sub_object($count, $type_id)
    {
        $arr = [
            "sd_vehicle_types_id" => $type_id,
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
                "vendor" => $excel->get_cell_value("C", $i),
                "two_count" => $excel->get_cell_value("E", $i),
                "three_count" => $excel->get_cell_value("F", $i),
                "four_count" => $excel->get_cell_value("G", $i),
                "ace_count" => $excel->get_cell_value("H", $i),
                "date" => $excel->getDate($excel->get_cell_value("D", $i)),              
            ];
            if ($obj["vendor"] == "" || $obj["date"] == "" || $obj["hub_name"] == "") {
                $obj["status"] = 10;
                $obj["msg"] = "Improper Data";
            }else{
                $rate_data = $this->_vendor_rate_helper->getOneWithHubNamAndCustomerName($obj["hub_name"], $obj["vendor"]);
                if (isset($rate_data->ID)) {
                    // vendor existed insert or update the data
                    $sub_data = [
                        $this->prepare_sub_object($obj["two_count"], 1),
                        $this->prepare_sub_object($obj["three_count"], 2),
                        $this->prepare_sub_object($obj["four_count"], 3),
                        $this->prepare_sub_object($obj["ace_count"], 4),
                    ];
                    $_vehicle_data = [
                        "sd_hub_id" =>  $rate_data->sd_hubs_id,
                        "sd_customer_id" => $rate_data->sd_customer_id,
                        "sd_date" => $obj["date"],
                        "vehicle_count" => 0,
                        "sub_data" => $sub_data
                    ];
                    $this->_helper->insertUpdateNew($_vehicle_data);
                    $obj["status"] = 5;
                } else {
                    $obj["status"] = 10;
                    $obj["msg"] = "Customer With Hub Is Not Existed or Not Linked";
                    $out[$obj["vendor"]] = $obj;
                }
            }
        }
        $this->response(array_values($out));
    }
    public function VehicleReport()
    {


        $id = 3;

        $this->_helper->generateVehiclesPdf($id);
        exit();
    }
}
