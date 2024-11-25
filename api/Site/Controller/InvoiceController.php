<?php

namespace Site\Controller;


use Core\BaseController;
use Core\Helpers\SmartData;
use Site\Helpers\InvoiceHelper;
use Site\Helpers\BillHelper;
//use Site\View\InvoicePdf;
use Core\Helpers\SmartFileHelper;
use Site\Helpers\InvoiceSubHelper;
use Site\Helpers\HubsHelper;



class InvoiceController extends BaseController
{

    private InvoiceHelper $_helper;
    private BillHelper $_bill_helper;
    // private InvoicePdf $_invoice_pdf_helper;
    private InvoiceSubHelper $_invoice_sub_helper;
    private HubsHelper $_hub_helper;
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new InvoiceHelper($this->db);
        $this->_bill_helper = new BillHelper($this->db);
        //   $this->_invoice_pdf_helper = new InvoicePdf($this->db);
        $this->_invoice_sub_helper = new InvoiceSubHelper($this->db);
        $this->_hub_helper= new HubsHelper($this->db);
    }

    /**
     * 
     */
    public function insert()
    {
        $columns = [
            "sd_bill_id",
            "sd_hub_id",
            "sd_vendor_id",
            "total_units",
            "total_vehicles",
            "unit_amount",
            "vehicle_amount",
            "gst_percentage",
            "gst_amount",
            "total_amount"
        ];
        // do validations
        $this->_helper->validate(InvoiceHelper::validations, $columns, $this->post);
        $columns[] = "status";
        $this->post["status"] = 5;
        // insert and get id
        $id = $this->_helper->insert($columns, $this->post);

        //
        $this->response($id);
    }


    public function generate()
    {
        $columns = ["bill_start_date", "bill_end_date"];
        // do validations
        $this->_helper->validate(BillHelper::validations, $columns, $this->post);
        $columns[] = "created_by";
        $columns[] = "created_time";
        $this->db->_db->Begin();
        $bill_id = $this->_bill_helper->insert($columns, $this->post);
        $bill_data = $this->_bill_helper->getOneData($bill_id);
        //var_dump($bill_data);
        // generate and insert invoice
        $dt = $this->_helper->insertInvoiceNew($bill_id, $bill_data);
        // update the bill table with the update details
        $this->_bill_helper->updateBillData($bill_id, $dt);
        // \CustomErrorHandler::triggerInvalid("tesing gg");
        $this->db->_db->commit();
        $this->response($bill_id);
    }


    public function update()
    {
        $bill_id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($bill_id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid Bill ID");
        }
        $bill_data = $this->_bill_helper->getOneData($bill_id);
        // generate and insert invoice
        $dt = $this->_helper->insertInvoiceNew($bill_id, $bill_data);
        // update the bill table with the update details
        $this->_bill_helper->updateBillData($bill_id, $dt);
        // \CustomErrorHandler::triggerInvalid("tesing gg");
        $this->db->_db->commit();
        $this->response($bill_id);
    }

    /**
     * 
     */

    /**
     * 
     */
    public function getAll()
    {
        $data = $this->_helper->getAllData();
        $this->response($data);
    }


    public function getAllSelect()
    {
        $id = SmartData::post_data("sd_customer_id","INTEGER"); 
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid Customer ID");
        }

        $sql = "sd_customer_id=:sd_customer_id";
        $_data = ["sd_customer_id"=>$id];
        $data = $this->_helper->getAllData($sql,$_data,["ID as value, invoice_number as label"]);
        $this->response($data);
    }

    /**
     * 
     */
    public function getInvoiceByBillID()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid Bill ID");
        }
        // insert and get id
        $data = $this->_helper->getInvoiceByBillId($id);
        $this->response($data);
    }

    public function getInvoiceByCustomerID()
    {
        $id = isset($this->post["sd_customer_id"]) ? intval($this->post["sd_customer_id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid Customer ID");
        }
        // insert and get id
        $data = $this->_helper->getInvoiceByCustomerId($id);
        $this->response($data);
    }


    public function getOne()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        // insert and get id
        $data = $this->_helper->getOneData($id);
        if (isset($data->ID)) {
            $data->sub_data = $this->_invoice_sub_helper->getAllByInvoiceId($data->ID);
        }
        $this->response($data);
    }

    public function getOneDetails()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        // insert and get id
        $data = $this->_helper->getOneDetails($id);
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
        $invoicd_data = $this->_helper->getOneData($id);
        $this->_invoice_sub_helper->deleteWithInvoiceId($id);
        // insert and get id
        $this->_helper->deleteOneId($id);
        
        $this->_bill_helper->updateBillDetails($invoicd_data->sd_bill_id);
        $out = new \stdClass();
        $out->msg = "Removed Successfully";
        $this->response($out);
    }
    /**
     * 
     */
    public function downloadInvoice()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        // generate the invoide 
        $data = $this->_helper->getOneData($id);
        $path = "invoice" . DS . $id . DS . "invoice.pdf";
        if (isset($data->ID) && $data->status!==10) {
            $this->_helper->prepareGenerateInvoice($data,$this->_invoice_sub_helper);
        }else{
            $path = "invoice" . DS . $id . DS . "invoicesign.pdf";
        }
       
        $path = SmartFileHelper::getDataPath() .  $path;
        $this->responseFileBase64($path);
    }


    public function getSingInfo()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        $data = $this->_helper->getOneData($id);
        $this->_helper->prepareGenerateInvoice($data,$this->_invoice_sub_helper);        
        $output = $this->_helper->initiate_curl_sign($data);
        $token = isset($output->data) ? $output->data : "";
        $this->_helper->update(["sign_token"],["sign_token"=>$token],$id);
        $this->response($output);
    }

    public function checkSingInfo()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        $token = SmartData::post_data("token","STRING");
        $output = $this->_helper->verify_sign_info($token);
        $status = isset($output->data) && isset($output->data->task_status) ? $output->data->task_status : "FAILED";
        if($status==="COMPLETED"){
            $content = $output->data->content;
            $this->_helper->storeSignedFile($id,$content);
            $update_data = [
                "status"=>10,
                "signed_invoice"=>$content
            ];
            $this->_helper->update(["status","signed_time","signed_by","signed_invoice"],$update_data,$id);
            $this->responseMsg("Signature Verified");
        }else{
           // \CustomErrorHandler::triggerInvalid("Signature Filed");
        }
    }


    public function insertManual(){
        $id = SmartData::post_data("bill_id","INTEGER");
        if($id < 1){
            \CustomErrorHandler::triggerInvalid("Bill_id is required");
        }
        $data = [];
        $sub_data = SmartData::post_array_data("rate_data");
        if(count($sub_data)  < 1 ){
            \CustomErrorHandler::triggerInvalid("Atleast one invoice item is required");
        }
        $total_taxable = 0;
        $tax = "18";
        foreach($sub_data as $key=>$_sub_arr){
            $total_taxable += floatval($_sub_arr["price"]);
            $tax = floatval($_sub_arr["tax"]);
            $tax_value = floatval($_sub_arr["price"]) * (floatval($_sub_arr["price"]) / 100 );
            $sub_arr["total"] = floatval($_sub_arr["price"]) +  $tax_value;
            $sub_data[$key] = $_sub_arr;
        }
        $data["sd_hub_id"]  = SmartData::post_select_value("sd_hubs_id");
        $hub_data = $this->_hub_helper->getOneData($data["sd_hub_id"]);
        
        $data["sd_customer_id"]  = SmartData::post_select_value("sd_customer_id");
        $data["sd_customer_address_id"]  = SmartData::post_select_value("sd_customer_address_id");
        $data["sd_vendor_rate_id"]   = 0;
        $data["total_taxable"] = $total_taxable;
        $data["status"] = 0;
        $data["invoice_type"] = 2;
        $data["gst_percentage"] = $tax;
        $data["gst_amount"] = $total_taxable * ($tax / 100);
        $data["total_amount"] =  $data["total_taxable"] +   $data["gst_amount"];
        $data["short_name"] = $hub_data->short_name;
        $data["sub_data"] = $sub_data;
        $data["sd_bill_id"] = $id;
        $data["bill_type"] = "CMS";
        $this->db->_db->Begin();
        $this->_helper->insertUpdateSingle($data);
        $this->_bill_helper->updateBillDetails($id);
        $this->db->_db->commit();
        $this->responseMsg("Invoice Added/Updated");

    }


    public function getPdf()
    {



        $id = 55;
        $_dt = $data = $this->_helper->getOneData($id);
        if (isset($data->ID)) {
            $data->sub_data = $this->_invoice_sub_helper->getAllByInvoiceId($data->ID);
            $_sub_data_vehicle=[];
            foreach(  $data->sub_data  as $_obj){
               // var_dump($_obj);
                if($_obj->vehicle_id > 0 && $_obj->count > 0 && ($_obj->type==1 || $_obj->type==2)){
                    $_item_data = $this->_helper->getVehicleCount($_dt ,$_obj->vehicle_id,$_obj->price);
                    $_sub_data_vehicle[] = $_item_data;
                }
            }
            $data->sub_data_vehicle = $_sub_data_vehicle;           
        }
       // var_dump($data);
       // exit();
        $this->_helper->generateInvoicePdf($id,$data);
        // $html = InvoicePdf::getHtml([]);
        // $path = "invoice" . DS . $id . DS . "invoice.pdf";
        // SmartPdfHelper::genPdf($html,$path);
      //  $this->_helper->generateInvoicePdf($id);
      //  exit();
        // if ($data < 1) {
        //     \CustomErrorHandler::triggerInvalid("Provide Data ");
        // }
        // $id = $data["ID"];
        // $location = $this->_helper->getFullFile($id);
        // $html = $this->_invoice_pdf_helper->getHtml($data);
        // try {
        //     PdfHelper::genPdf($html, $location);
        //     $this->response("pdf created successfully");
        // } catch (\Exception $e) {
        //     $this->response($e);
        // }

    }
}
