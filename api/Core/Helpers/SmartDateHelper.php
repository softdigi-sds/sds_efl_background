<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Helpers;
// others

/**
 * Description of Validator
 *
 * @author kms
 */
class SmartDateHelper
{

    static public function getDatesBetween($start_date, $end_date)
    {
        $dates = [];
        // Create DateTime objects for the start and end dates
        $start = new \DateTime($start_date);
        $end = new \DateTime($end_date);
        // Add one day to end date to include it in the range
        $end->modify('+1 day');
        // Create a DatePeriod object
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
        // Iterate through the period and add dates to the array
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d'); // You can format the date as needed
        }
        return $dates;
    }
}
