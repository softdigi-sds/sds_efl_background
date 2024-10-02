<?php

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartExcellHelper;
use Core\Helpers\SmartFileHelper;
use Site\Helpers\BillHelper;
use Site\Helpers\InvoiceHelper;
use Site\Helpers\ImportHelper;
use Core\Helpers\SmartData as Data;


class BillController extends BaseController
{

    private BillHelper $_helper;
    private InvoiceHelper $_invoice_helper;
    private ImportHelper $_import_helper;
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new BillHelper($this->db);
        //
        $this->_invoice_helper = new InvoiceHelper($this->db);
        //
        $this->_import_helper = new ImportHelper($this->db);
    }

    /**
     * 
     */
    public function insert()
    {
        $columns = ["bill_start_date", "bill_end_date"];
        // do validations
        $this->_helper->validate(BillHelper::validations, $columns, $this->post);
        $columns[] = "created_by";
        $columns[] = "created_time";
        // insert and get id
        $id = $this->_helper->insert($columns, $this->post);

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
        $columns = ["bill_start_date", "bill_end_date"];
        // do validations
        $this->_helper->validate(BillHelper::validations, $columns, $this->post);
        // extra columns
        $columns[] = "created_by";
        $columns[] = "created_time";
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
        if (isset($data->ID)) {
            $data->invoice_data =  $this->_invoice_helper->getInvoiceByBillId($data->ID);
        }
        $this->response($data);
    }

    public function exportExcel()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        $invoice_data =  $this->_invoice_helper->getInvoiceByBillId($id);
        //
        $path = SmartFileHelper::getDataPath() . "bills" . DS . $id . DS . "bill.xlsx";
        SmartFileHelper::createDirectoryRecursive($path);
        $excel = new SmartExcellHelper($path, 0);
        $excel->createExcelFromData($invoice_data);
        $this->responseFileBase64($path);
        // $this->responseMsg("created success");
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

    public function getExcelContent($path)
    {
        $excel = new SmartExcellHelper($path, 0);
        $_data = $excel->getData($this->_import_helper->importAckColumns(), 2);
        return $_data;
        // foreach ($_data as $obj) {
        //     //var_dump($obj);
        //     // echo $obj["invoice_number"] . "<br/>";
        // }
        //var_dump($data);
    }

    private function checkUpdateInvoiceData($id, $obj)
    {
        $invoice_number = $obj["nameonly"];
        $invoice_id = str_replace("-", "/", $invoice_number);
        $invoice_id = str_replace("24/25", "24-25", $invoice_id);
        //echo " invoice id " . $invoice_id;
        $invoiceId = $this->_invoice_helper->getInvoiceId($id, $invoice_id);
        //echo "invoice ID " . $invoiceId;
        if ($invoiceId > 0) {
            $_data =  $this->getExcelContent($obj["path"]);
            $invoice_data = isset($_data[0]) ? $_data[0] : [];
            if (count($invoice_data) > 0) {
                $_in_data = [
                    "irn_number" => $invoice_data["irn_no"],
                    "signed_qr_code" => $invoice_data["signed_qr_code"],
                    "ack_no" => $invoice_data["ack_no"],
                    "ack_date" => $invoice_data["ack_date"],
                    "signed_invoice" => $invoice_data["signed_invoice"],
                    "status" => 10
                ];
                $this->_invoice_helper->updateInvoiceData($invoiceId, $_in_data);
            }
        }
    }

    /**
     * 
     */
    public function importZip()
    {
        $id = Data::post_data("id", "INTEGER");
        $excel_import = Data::post_array_data("excel");
        if (!is_array($excel_import) || count($excel_import) < 1) {
            \CustomErrorHandler::triggerInvalid("Please upload Zip to Import");
        }
        // get the excel content
        $content = isset($excel_import["content"]) ? $excel_import["content"] : "";
        if (strlen($content) < 10) {
            \CustomErrorHandler::triggerInvalid("Please upload Zip to Import");
        }
        //
        $insert_id = $this->_import_helper->insertData("IMPORT");
        // excel path 
        $store_path = "excel_import" . DS . $insert_id . DS . "import.zip";
        //
        $zip_dir = "excel_import" . DS . $insert_id . DS;
        // 
        $dest_path = SmartFileHelper::storeFile($content, $store_path);
        // 
        $this->_import_helper->updatePath($insert_id, $store_path);
        //
        SmartFileHelper::extractZip($dest_path, "");
        // $this->response($out);
        $xlsx_files = SmartFileHelper::getFilesDirectory($zip_dir, 'xlsx');
        //var_dump($xlsx_files);
        if (count($xlsx_files) < 1) {
            \CustomErrorHandler::triggerInvalid("Please upload a valid zip file");
        }
        foreach ($xlsx_files as $obj) {
            //$invoice_number = $obj["nameonly"];
            $this->checkUpdateInvoiceData($id, $obj);
        }
        // 
        $this->responseMsg("Imported Successfully");
    }
}
