<?php

namespace Site\Controller;

use Core\BaseController;
use Core\Helpers\SmartData;
use Site\Helpers\VendorsHelper as VendorsHelper;
use Core\Helpers\SmartData as Data;
use Site\Helpers\InvoiceHelper as InvoiceHelper;
use Site\Helpers\HubsHelper;
use Site\Helpers\StateDbHelper;
use Core\Helpers\SmartFileHelper;
use Core\Helpers\SmartExcellHelper;
use Site\Helpers\ImportHelper;

class VendorsController extends BaseController
{

    private VendorsHelper $_helper;
    private InvoiceHelper $_invoice_helper;
    private HubsHelper $_hubs_helper;
    private StateDbHelper $_state_helper;
    private ImportHelper $_import_helper;

    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new VendorsHelper($this->db);
        $this->_invoice_helper = new InvoiceHelper($this->db);
        $this->_state_helper = new StateDbHelper($this->db);
        $this->_hubs_helper = new HubsHelper($this->db);
        $this->_import_helper = new ImportHelper($this->db);
    }

    /**
     * 
     */
    public function insert()
    {
        $validate_columns = ["sd_hub_id", "vendor_code", "vendor_company", "gst_no", "pin_code"];
        // do validations
        $this->_helper->validate(VendorsHelper::validations, $validate_columns, $this->post);
        $columns = ["sd_hub_id", "vendor_code", "vendor_company", "vendor_name", "billing_to", "gst_no", "pan_no", "address_one", "address_two", "state_name", "pin_code", "status", "created_by", "created_time"];
        $this->post["status"] = 5;
        $this->post["sd_hub_id"] = Data::post_select_value("sd_hub_id");
        $this->post["state_name"] = Data::post_select_value("state_name");
        $data = $this->_helper->checkVendorByCodeCompany($this->post["vendor_code"], $this->post["vendor_company"]);
        if (!empty($data)) {
            \CustomErrorHandler::triggerInvalid("Vendor code and company already available ");
        }
        $this->db->_db->Begin();
        // insert and get id
        $id = $this->_helper->insert($columns, $this->post);
        $this->db->_db->commit();
        //
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
        $columns = ["gst_no", "pin_code", "status", "billing_to"];
        // do validations
        $this->_helper->validate(VendorsHelper::validations, $columns, $this->post);
        // insert and get id
        $columns[] = "last_modified_by";
        $columns[] =  "last_modified_time";
        $this->_helper->update($columns, $this->post, $id);
        $this->response($id);
    }

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


    // public function getAll()
    // {
    //     // insert and get id
    //     $data = $this->_helper->getAllData();
    //     $this->response($data);
    // }
    public function getAll()
    {
        // Get hub_id from POST request
        $hub_id = SmartData::post_data("hub_id","INTEGER");
        // Prepare SQL query and data inputs
        $sql = "";
        $data_in = [];    
        if (intval($hub_id) > 0) {         
            $sql = "t1.sd_hub_id=:id";
            $data_in["id"] = $hub_id;
        }    
        // Fetch data using the helper function
        $data = $this->_helper->getAllData($sql, $data_in);    
        // Send response
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
        $data = $this->_invoice_helper->checkInvoiceExist($id);
        if (!empty($data)) {
            \CustomErrorHandler::triggerInvalid("Cannot remove Vendor, invoice is registered for for this vendor");
        }
        // insert and get id
        $this->_helper->deleteOneId($id);
        $out = new \stdClass();
        $out->msg = "Deleted Successfully";
        $this->response($out);
    }

    public function getAllSelect()
    {
        $id = isset($this->post["hub_id"]) ? intval($this->post["hub_id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid Hub ID");
        }
        // insert and get id
        $sql = "t1.sd_hub_id=:hub_id";
        $data_in = ["hub_id" => $id];
        $select = ["t1.ID AS value, t1.vendor_company AS  label"];
        $data = $this->_helper->getAllData($sql, $data_in, $select);
        $this->response($data);
    }
    public function getVendorCompany()
    {

        $vendor_company = isset($this->post["vendor_company"]) ? trim($this->post["vendor_company"]) : "";


        if (strlen($vendor_company) < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid vendor company");
        }

        $data = $this->_helper->getVendorCompany($vendor_company);

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
        $insert_id = $this->_import_helper->insertData("VENDORS");
        // excel path 
        $store_path = "excel_import" . DS . $insert_id . DS . "import.xlsx";
        // 
        $dest_path = SmartFileHelper::storeFile($content, $store_path);
        // 
        $this->_import_helper->updatePath($insert_id, $store_path);
        // read the excel and process
        // $dest_path = "E:\Book1.xlsx";
        $excel = new SmartExcellHelper($dest_path, 2);
        $_data = $excel->getData($this->_import_helper->importVendorColumns(), 2);
        $out = [];
        // var_dump($_data);exit();
        foreach ($_data as $obj) {
            $hub_data = $this->_hubs_helper->checkHubExist($obj["HUB_ID"]);
            $state_data = $this->_state_helper->checkStateExist($obj["State"]);
            // var_dump($hub_data);exit();
            if ($obj["Customer Code"] == "" || $obj["HUB_ID"] == "" ) {
                $obj["status"] = 10;
                $obj["msg"] = "Improper Data";
            } else {
                if (isset($hub_data->ID) && isset($state_data->ID)) {
                    $_vendor_data = [
                        "sd_hub_id" => $hub_data->ID,
                        "vendor_code" => $obj["Customer Code"],
                        "vendor_company" =>$obj["Vendor"], 
                        "vendor_name" =>$obj["Name of the Customer"], 
                        "billing_to" => $obj["BILLING TO"], 
                        "gst_no" => $obj["GST NO."], 
                        "pan_no" => $obj["PAN"], 
                        "address_one" => $obj["Address 1"], 
                        "address_two" => $obj["Address 2"], 
                        "state_name" => $state_data->ID, 
                        "pin_code" => $obj["Pin Code"], 
                        "status" => 5

                      
                    ];
                    // var_dump($_vendor_data);exit();
                    $this->_helper->insertUpdateNew($_vendor_data);
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
