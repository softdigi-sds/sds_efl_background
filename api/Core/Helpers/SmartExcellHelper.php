<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Core\Helpers;

//require 'vendor/excel/autoload.php';
require 'vendor/PhpSpreadsheet/IOFactory.php';

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\IOFactory;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Description of SmartExcellNew
 *
 * @author SUBBA RAJU
 */
class SmartExcellHelper
{

    //put your code here
    //put your code here
    private $_excel_path;
    private $_last_row;
    private $_sheet_number;
    private $_worksheet;

    function __construct($excel_path, $sheetno)
    {
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
    private function init_excel()
    {
        try {
            $inputFileType = IOFactory::identify($this->_excel_path);
            /**  Create a new Reader of the type that has been identified  * */
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            /**  Load $inputFileName to a Spreadsheet Object  * */
            $excelObj      = $reader->load($this->_excel_path);
            $this->_worksheet = $excelObj->getSheet($this->_sheet_number);
            //var_dump( $this->_worksheet);
            //exit();
            $this->_last_row  = $this->_worksheet->getHighestRow();
        } catch (\Exception $ex) {
            var_dump($ex);
        }
    }

    /**
     * 
     * @param type $column_number
     * @param type $row_number
     * @return type
     */
    public function get_cell_value($column_number, $row_number)
    {
        $out = $this->_worksheet->getCell($column_number . $row_number)->getValue();
        return $out;
    }

    public function get_last_row()
    {
        return $this->_last_row;
    }

    public function getDate($value)
    {

        // Check if the value is numeric (Excel date number)
        if (is_numeric($value)) {
            // Convert Excel date number to DateTime object
            $dateTime =\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            return $dateTime->format('Y-m-d'); // Return the formatted date
        } elseif (is_string($value)) {
            // Try to create a DateTime object from a string
            $dateTime = \DateTime::createFromFormat('Y-m-d', $value);

            // If that fails, try a different format (you can add more formats as needed)
            if (!$dateTime) {
                $dateTime = \DateTime::createFromFormat('d/m/Y', $value);
            }

            // Check if a DateTime object was created
            if ($dateTime) {
                return $dateTime->format('Y-m-d'); // Return the formatted date
            }
        }
        // Return 'Invalid Date' or the original value if not convertible
        return '';
    }


    public function getData($columns,$startRow=1)
    {
        $out = []; 
        for ($i = $startRow; $i < $this->_last_row; $i++) {
            $obj = [];
            $empty = false;
            foreach ($columns as $single_column) {
                $letter = $single_column["letter"];
                $prp_name = $single_column["index"];
                $value = "";
                if(isset($single_column["type"]) && $single_column["type"]=="date"){
                    $value =  $this->getDate($this->get_cell_value($letter, $i));
                }else{
                    $value =  $this->get_cell_value($letter, $i);
                }              
                if (isset($single_column["empty"]) && ($value == null || $value == "")) {
                    $empty = true;
                }
                $obj[$prp_name] = $value;
            }
            if ($empty === false) {
                $out[] = $obj;
            }
        }
        return $out;
    }

    // public function getExcelData()
    // {
    //     try {
    //         $spreadsheet = IOFactory::load($this->_excel_path);
    //         $sheet = $spreadsheet->getSheet($this->_sheet_number);
    //         $data = [];
    //         $headers = [];
    //         $headerRow = $sheet->getRowIterator()->current();
    //         $cellIterator = $headerRow->getCellIterator();
    //         $cellIterator->setIterateOnlyExistingCells(false);
    //         foreach ($cellIterator as $cell) {
    //             $headers[] = $cell->getValue();
    //         }

    //         foreach ($sheet->getRowIterator() as $row) {
    //             if ($row->getRowIndex() === 1) continue;
    //             $cellIterator = $row->getCellIterator();
    //             $cellIterator->setIterateOnlyExistingCells(false);
    //             $rowData = [];
    //             foreach ($cellIterator as $cell) {
    //                 $columnIndex = $cell->getColumn();
    //                 $index = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnIndex) - 1;
    //                 if (isset($headers[$index])) {
    //                     $rowData[$headers[$index]] = $cell->getValue();
    //                 }
    //             }
    //             $data[] = $rowData;
    //         }

    //         return $data;
    //     } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    //         die('Error loading file "' . pathinfo($this->_excel_path, PATHINFO_BASENAME) . '": ' . $e->getMessage());
    //     }
    // }
    // public function createExcelFromData($data)
    // {
    //     if (empty($data)) {
    //         \CustomErrorHandler::exceptionHandler("No data provided! " . $this->_excel_path);
    //     }
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();
    //     $headers = array_keys((array)$data[0]);
    //     // var_dump($headers);exit();
    //     $sheet->fromArray($headers, NULL, 'A1');
    //     $rowNumber = 2;
    //     foreach ($data as $object) {
    //         $rowData = (array)$object;
    //         // var_dump(array_values($rowData));exit();
    //         $sheet->fromArray(array_values($rowData), 77, 'A' . $rowNumber);
    //         $rowNumber++;
    //     }
    //     $writer = new Xlsx($spreadsheet);
    //     $outputFileName = 'E:/output.xlsx';
    //     $writer->save($outputFileName);
    //     echo "Excel file created successfully: $outputFileName";
    // }
}
