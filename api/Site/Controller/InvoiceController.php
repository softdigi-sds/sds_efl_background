<?php

namespace Site\Controller;


use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Site\Helpers\InvoiceHelper;
use Site\Helpers\BillHelper;
use site\View\InvoicePdf;
use Core\Helpers\PdfHelper;
use Core\Helpers\SmartFileHelper;
use Core\Helpers\SmartPdfHelper;

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
        $dt = $this->_helper->insertInvoice($bill_id, $bill_data);
        // update the bill table with the update details
        $this->_bill_helper->updateBillData($bill_id, $dt);
        $this->db->_db->commit();
        $this->response($bill_id);
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
        $columns = ["sd_bill_id", "sd_hub_id", "sd_vendor_id", "total_units", "total_vehicles", "unit_amount", "vehicle_amount", "gst_percentage", "gst_amount", "total_amount"];
        // do validations
        $this->_helper->validate(InvoiceHelper::validations, $columns, $this->post);
        // extra columns
        $columns[] = "status";
        $this->post["status"] = 5;
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

        $data = [
            'ack_no' => '112421792792326',
            'ack_date' => '10-09-2024 13:22:00',
            'irn_no' => 'f0996c5de697a29f3eb0499fa045bd1df0f',
            'additional_info' => 'd23e558b015db32854e196ecfb62c',
            'invoice_number' => 'EFL/TS/428/24-25',
            'invoice_date' => '10/09/2024',
            'date_of_supply' => '10/09/2024',
            'items' => [
                [
                    'sl_no' => 1,
                    'description' => 'ELECTRIC VEHICLE PARKING FEE - 3WL (from 21-07-2024 to 20-08-2024)',
                    'hsn_code' => '996743',
                    'quantity' => '7.610',
                    'unit' => 'NOS',
                    'unit_price' => '4500.000',
                    'taxable_amount' => '34245.00',
                    'tax_details' => '18.00 + 0 | 0 + 0',
                    'tcs' => '0.0',
                    'total' => '40409.10'
                ],
                // Add more items as needed
            ],
            // Add more data fields as required
        ];
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
