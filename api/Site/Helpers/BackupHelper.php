<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Helpers;

use Core\BaseHelper;
use Core\Helpers\SmartConst;

//
use Site\Helpers\TableHelper as Table;

/**
 * Description of Data
 * 
 *  class helps to get the data from post with specified type 
 *
 * @author kms
 */
class BackupHelper extends BaseHelper
{
    private function getTables()
    {
        // $tables = array();
        $this->db->_db->query("SHOW TABLES");
        $result = $this->db->_db->resultset();
        $tables = (array)$result;
        //var_dump($tables);
        $out = [];
        foreach ($tables as $arr) {
            //var_dump($arr);
            $out[] = $arr->Tables_in_igcdoc;
        }
        return $out;
    }

    public function doBackUp($backup_file)
    {
        $tables = $this->getTables();
        $handle = fopen($backup_file, 'w');
        // Check if file opened successfully
        if (!$handle) {
             \CustomErrorHandler::triggerInternalError("error taking backup");
        }
       
        
        // Close the file and database connection
        fclose($handle);
        //$this->db->_db->close
    }
}
