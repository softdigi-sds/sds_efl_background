<?php

namespace Site\Controller;


use Core\BaseController;

use Site\Helpers\InvoiceHelper;
use Site\Helpers\BillHelper;
use Site\View\InvoicePdf;
use Core\Helpers\SmartFileHelper;


class InvoiceController extends BaseController
{

    private InvoiceHelper $_helper;
    private BillHelper $_bill_helper;

    private InvoicePdf $_invoice_pdf_helper;
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new InvoiceHelper($this->db);
        $this->_bill_helper = new BillHelper($this->db);
        $this->_invoice_pdf_helper = new InvoicePdf($this->db);
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
        // insert and get id
        $this->_helper->deleteOneId($id);
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
        $path = "invoice" . DS . $id . DS . "invoice.pdf";
        //
        $path = SmartFileHelper::getDataPath() .  $path;
        $this->responseFileBase64($path);
    }


    public function getPdf()
    {

    
        $id = 3;
        // $html = InvoicePdf::getHtml([]);
        // $path = "invoice" . DS . $id . DS . "invoice.pdf";
        // SmartPdfHelper::genPdf($html,$path);
        $this->_helper->generateInvoicePdf($id);
        exit();
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
