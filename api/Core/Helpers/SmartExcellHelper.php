<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Core\Helpers;

require 'vendor/excel/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
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
            \CustomErrorHandler::exceptionHandler("Invalid Excel File Path " . $this->_excel_path);
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

    public function getExcelData() {
        try {
            $spreadsheet = IOFactory::load($this->_excel_path);
            $sheet = $spreadsheet->getSheet($this->_sheet_number);
            $data = []; 
            $headers = []; 
            $headerRow = $sheet->getRowIterator()->current();
            $cellIterator = $headerRow->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); 
            foreach ($cellIterator as $cell) {
                $headers[] = $cell->getValue(); 
            }

            foreach ($sheet->getRowIterator() as $row) {
                if ($row->getRowIndex() === 1) continue;  
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); 
                $rowData = []; 
                foreach ($cellIterator as $cell) {
                    $columnIndex = $cell->getColumn(); 
                    $index = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnIndex) - 1; 
                    if (isset($headers[$index])) {
                        $rowData[$headers[$index]] = $cell->getValue(); 
                    }
                }
                $data[] = $rowData; 
            }
    
            return $data; 
    
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            die('Error loading file "' . pathinfo($this->_excel_path, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
    }
    public function createExcelFromData($data) {
        if (empty($data)) {
            \CustomErrorHandler::exceptionHandler("No data provided! " . $this->_excel_path);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = array_keys((array)$data[0]); 
        // var_dump($headers);exit();
        $sheet->fromArray($headers, NULL, 'A1');
        $rowNumber = 2; 
        foreach ($data as $object) {
            $rowData = (array)$object;
        // var_dump(array_values($rowData));exit();
            $sheet->fromArray(array_values($rowData), 77, 'A' . $rowNumber);
            $rowNumber++;
        }
        $writer = new Xlsx($spreadsheet);
        $outputFileName = 'E:/output.xlsx';
        $writer->save($outputFileName);
        echo "Excel file created successfully: $outputFileName";
    }
    
}
