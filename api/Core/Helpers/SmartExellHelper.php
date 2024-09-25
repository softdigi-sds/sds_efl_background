<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Core\Helpers;

require 'vendor/excel/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Description of SmartExcellNew
 *
 * @author SUBBA RAJU
 */
class SmartExcellHelper {

    //put your code here
    //put your code here
    private $_excel_path;
    private $_last_row;
    private $_sheet_number;

    function __construct($excel_path, $sheetno) {
        $this->_excel_path   = $excel_path;
        $this->_sheet_number = $sheetno;
        if (!file_exists($this->_excel_path)) {
            \CustomErrorHandler::triger_error("Invalid Excel File Path " . $this->_excel_path);
        }
        //
        $this->init_excel();
    }

    /**
     * 
     */
    private function init_excel() {
        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($this->_excel_path);

        /**  Create a new Reader of the type that has been identified  * */
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

        /**  Load $inputFileName to a Spreadsheet Object  * */
         $excelObj      = $reader->load($this->_excel_path);
        // include_once("../../common/excel/PHPExcel.php");
       // $excelReader      = \PHPExcel_IOFactory::CreateReaderForFile($this->_excel_path);
        //$excelObj         = $excelReader->load($this->_excel_path);
        $this->_worksheet = $excelObj->getSheet($this->_sheet_number);
        $this->_last_row  = $this->_worksheet->getHighestRow();
    }

    /**
     * 
     * @param type $column_number
     * @param type $row_number
     * @return type
     */
    public function get_cell_value($column_number, $row_number) {
        $out = $this->_worksheet->getCell($column_number . $row_number)->getValue();
        return $out;
    }

    public function get_last_row() {
        return $this->_last_row;
    }

}
