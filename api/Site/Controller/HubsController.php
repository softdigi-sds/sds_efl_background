<?php

namespace Site\Controller;

use Core\BaseController;
use Core\Helpers\SmartData;
use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartData as Data;
use Core\Helpers\SmartFileHelper;
use Core\Helpers\SmartExcellHelper;
use Site\Helpers\HubsHelper;
use Site\Helpers\HubGroupsHelper;
use Site\Helpers\EflOfficeHelper;
use Site\Helpers\VendorRateHelper;
use Site\Helpers\ImportHelper;



class HubsController extends BaseController
{

    private HubGroupsHelper $_hub_group_helper;
    private HubsHelper $_helper;
    private EflOfficeHelper $_office_helper;
    private ImportHelper $_import_helper;

    private VendorRateHelper $_vendor_rate_helper;
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new HubsHelper($this->db);
        $this->_vendor_rate_helper = new VendorRateHelper($this->db);
        $this->_hub_group_helper = new HubGroupsHelper($this->db);
        $this->_import_helper = new ImportHelper($this->db);
        $this->_office_helper = new EflOfficeHelper($this->db);

    }

    /**
     * k
     */
    public function insert()
    {
        $columns = ["hub_id", "hub_name", "sd_efl_office_id"];
        // do validations
        $this->_helper->validate(HubsHelper::validations, $columns, $this->post);
        $columns = ["hub_id", "hub_name", "sd_efl_office_id","longitude","latitude"];
        $this->post["status"] = 5;
        $columns[] = "created_by";
        $columns[] = "created_time";
        $this->post["sd_efl_office_id"] = Data::post_select_value("sd_efl_office_id");
        $data = $this->_helper->checkHubExist($this->post["hub_id"]);
        if (!empty($data)) {
            \CustomErrorHandler::triggerInvalid("Hub ID already exist ");
        }
        $this->db->_db->Begin();
        // insert and get id
        $id = $this->_helper->insert($columns, $this->post);
        // insert roles
        if (!($this->post["role"]) == NULL) {
            $this->_hub_group_helper->insertRoles($id, $this->post["role"]);
        }
        $this->db->_db->commit();
        $this->response($id);
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
        $columns = ["hub_name","status"];
        // do validations
        $this->_helper->validate(HubsHelper::validations, $columns, $this->post);
        // extra columns
        $columns[] = "longitude";
        $columns[] = "latitude";
        $columns[] = "last_modified_by";
        $columns[] =  "last_modified_time";
        // begin transition
        $this->db->_db->Begin();
        // insert and get id
        $this->_helper->update($columns, $this->post, $id);
        // insert roles
        if (!($this->post["role"]) == NULL) {
            $this->_hub_group_helper->insertRoles($id, $this->post["role"]);
        } else {
            // delete from user role table
            $this->_hub_group_helper->deleteHubRole($id);
        }
        //
        $this->db->_db->commit();
        $this->response($id);
    }
    /**
     * 
     */
    public function updateStatus()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        $status = SmartData::post_data("status", "INTEGER");
        $columns = ["status"];
        $data_in = ["status" => $status];
        $this->_helper->update($columns, $this->post, $id);
        $this->responseMsg("Status Updated Successfully");
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
        $data = $this->_vendor_rate_helper->checkVenodrByHubId($id);
        if (!empty($data)) {
            \CustomErrorHandler::triggerInvalid("Cannot remove Hub, Hub is allocated to a vendor.");
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
    public function getAllSelect()
    {
        $select = ["t1.ID as value,t1.hub_id as label"];
        $data = $this->_helper->getAllData("", [], $select);
        $this->response($data);
    }
    public function gethubid(){  
        $id = isset($this->post["id"]) ? trim($this->post["id"]) : "";
        if(strlen($id )< 1){
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }    
        // insert and get id
        $data = $this->_helper->getHubID($id);
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
        $insert_id = $this->_import_helper->insertData("HUBS");
        // excel path 
        $store_path = "excel_import" . DS . $insert_id . DS . "import.xlsx";
        // 
        $dest_path = SmartFileHelper::storeFile($content, $store_path);
        // 
        $this->_import_helper->updatePath($insert_id, $store_path);
        // read the excel and process
        // $dest_path = "E:\Book1.xlsx";
        $excel = new SmartExcellHelper($dest_path, 1);
        $_data = $excel->getData($this->_import_helper->importHubColumns(), 2);
        $out = [];
        // var_dump($_data);exit();
        foreach ($_data as $obj) {
            $office_data = $this->_office_helper->checkOfficeExist($obj["SD_OFFICE"]);
            // var_dump($office_data);exit();
            if ($obj["HUB_ID"] == "" || $obj["SD_OFFICE"] == "") {
                $obj["status"] = 10;
                $obj["msg"] = "Improper Data";
            } else {
                if (isset($office_data->ID)) {
                    $_hub_data = [
                        "hub_id" => $obj["HUB_ID"],
                        "hub_name" => $obj["HUB_ID"],
                        "sd_efl_office_id" => $office_data->ID
                    ];
                    // var_dump($_hub_data);exit();
                    $this->_helper->insertUpdateNew($_hub_data);
                    $obj["status"] = 5;
                } else {
                    $obj["status"] = 10;
                    $obj["msg"] = "Office Does Not Existed";
                }
            }
            $out[] = $obj;
        }
        $this->response($out);
    }

}
