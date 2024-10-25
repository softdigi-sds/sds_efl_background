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
class TaxillaExcelHelper extends BaseHelper
{

    public function getData($_dt)
    {
        $out = [
            "Transaction Reference Number" => $_dt->invoice_number,
            "Invoice Number" => $_dt->invoice_number,
            "Supply Type" => "Outward"

        ];
        return $out;
    }
}
