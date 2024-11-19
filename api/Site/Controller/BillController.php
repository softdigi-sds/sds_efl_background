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
use Site\Helpers\TaxillaExcelHelper;



class BillController extends BaseController
{

    private BillHelper $_helper;
    private InvoiceHelper $_invoice_helper;
    private ImportHelper $_import_helper;
    private TaxillaExcelHelper $_taxilla_helper;
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new BillHelper($this->db);
        //
        $this->_invoice_helper = new InvoiceHelper($this->db);
        //
        $this->_import_helper = new ImportHelper($this->db);
        //
        $this->_taxilla_helper = new TaxillaExcelHelper($this->db);
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

    public function getAllSelect()
    {
        $select = ["t1.ID as value", "t1.bill_start_date as label"];
        $data = $this->_helper->getAllData("", [], $select);
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
        $invoice_data =  $this->_invoice_helper->getInvoiceByBillIdForExport($id);
        // var_dump($invoice_data);
        if (!isset($invoice_data[0])) {
            \CustomErrorHandler::triggerInvalid("Invalid data");
        }
        $out = [];
        foreach ($invoice_data as $obj) {
            $out[] = $this->_taxilla_helper->getData($obj);
        }
        //
        $path = SmartFileHelper::getDataPath() . "bills" . DS . $id . DS . "bill.xlsx";
        SmartFileHelper::createDirectoryRecursive($path);
        $excel = new SmartExcellHelper($path, 0);
        $excel->createExcelFromData($out);
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
                    "signed_qr_code" => isset($invoice_data["signed_qr_code"]) ? $invoice_data["signed_qr_code"] : "",
                    "ack_no" => $invoice_data["ack_no"],
                    "ack_date" => $invoice_data["ack_date"],
                    "signed_invoice" => $invoice_data["signed_invoice"],
                    "status" => 5
                ];
                $this->_invoice_helper->updateInvoiceData($invoiceId, $_in_data);
                // $this->_invoice_helper->generateInvoicePdf($invoiceId);
            }
        }
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
        for ($i = 2; $i <= $excel->get_last_row(); $i++) {
            $obj = [
                "invoice_number" => $excel->get_cell_value("A", $i),
                "ack_no" => $excel->get_cell_value("FS", $i),
                "signed_qr_code" => $excel->get_cell_value("FR", $i),
                "ack_date" => $excel->getDate($excel->get_cell_value("FT", $i)),
                "irn_number" => $excel->get_cell_value("C", $i),
            ];
            // var_dump($obj);
            if ($obj["invoice_number"] == "" || $obj["ack_no"] == "") {
                $obj["status"] = 10;
                $obj["msg"] = "Improper Data";
                $out[$obj["invoice_number"]] = $obj;
            } else {
                $rate_data = $this->_invoice_helper->getOneWithInvoiceNumber($obj["invoice_number"]);
                // var_dump($rate_data);
                if (isset($rate_data->ID)) {
                    if ($rate_data->status != 10) {
                        $in_data = $obj;
                        $in_data["status"] = 5;
                        $this->_invoice_helper->updateInvoiceData($rate_data->ID, $in_data);
                        //
                        $obj["status"] = 5;
                    } else {
                        $obj["status"] = 10;
                        $obj["msg"] = "Invoice Already Signed";
                        $out[$obj["invoice_number"]]  = $obj;
                    }
                } else {
                    $obj["status"] = 10;
                    $obj["msg"] = "Invoice Number Not Existed";
                    $out[$obj["invoice_number"]]  = $obj;
                }
            }
        }
        //var_dump($out);
        $this->response(array_values($out));
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
