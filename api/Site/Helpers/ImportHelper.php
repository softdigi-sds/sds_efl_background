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
class ImportHelper extends BaseHelper
{
    const schema = [
        "sd_import_type" => SmartConst::SCHEMA_VARCHAR,
        "sd_file" => SmartConst::SCHEMA_VARCHAR,
        "sd_mt_userdb_id" => SmartConst::SCHEMA_CUSER_ID,
    ];
    /**
     * 
     */
    const validations = [];
    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::SD_IMPORT, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::SD_IMPORT, $columns, $data, $id);
    }

    public function insertData($type)
    {
        // insert data in import helper 
        $columns = ["sd_import_type", "sd_mt_userdb_id"];
        $data = ["sd_import_type" => $type];
        return $this->insert($columns, $data);
    }

    public function updatePath($id, $path)
    {
        // insert data in import helper 
        $columns = ["sd_file"];
        $data = ["sd_file" => $path];
        return $this->update($columns, $data, $id);
    }

    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::SD_IMPORT . " t1";;
        $select = ["t1.*"];
        $sql = " t1.ID=:ID";
        $data_in = ["ID" => $id];
        $data = $this->getAll($select, $from, $sql, "", "", $data_in, true, []);
        return $data;
    }
    /**
     * 
     */
    public function deleteOneId($id)
    {
        $from = Table::BILL;
        $this->deleteId($from, $id);
    }

    public function importColumnsVehicleCount()
    {
        $columns = [
            [
                "letter" => "B",
                "index" => "hub_name",
                "empty" => true
            ],
            [
                "letter" => "C",
                "index" => "vendor",
                "empty" => true
            ],
            [
                "letter" => "D",
                "index" => "date",
                "type" => "date"
            ],
            [
                "letter" => "E",
                "index" => "two_count",
            ],
            [
                "letter" => "F",
                "index" => "three_count",
            ],
            [
                "letter" => "G",
                "index" => "four_count",
            ],
            [
                "letter" => "H",
                "index" => "ace_count",
            ]
        ];
        return $columns;
    }


    public function importConsumptionColumns()
    {
        $columns = [
            [
                "letter" => "G",
                "index" => "hub_id",
                "empty" => true
            ],
            [
                "letter" => "E",
                "index" => "vendor"
            ],
            [
                "letter" => "M",
                "index" => "point_type",
            ],
            [
                "letter" => "P",
                "index" => "date",
                "type" => "date"
            ],
            [
                "letter" => "W",
                "index" => "count",
            ]
        ];
        return $columns;
    }

    public function importAckColumns()
    {
        $columns = [
            [
                "letter" => "D",
                "index" => "invoice_number",
                "empty" => true
            ],
            [
                "letter" => "G",
                "index" => "irn_no"
            ],
            [
                "letter" => "H",
                "index" => "qr_code",
            ],
            [
                "letter" => "I",
                "index" => "ack_no",
            ],
            [
                "letter" => "J",
                "index" => "ack_date",
            ],
            [
                "letter" => "K",
                "index" => "signed_invoice",
            ],
        ];
        return $columns;
    }

     public function importHubColumns()
    {
        $columns = [
            [
                "letter" => "B",
                "index" => "SD_OFFICE",
                "empty" => true
            ],
            [
                "letter" => "C",
                "index" => "HUB_ID"
            ],
            [
                "letter" => "D",
                "index" => "HUB_NAME"
            ]
        ];
        return $columns;
    }
     public function importVendorColumns()
    {
        $columns = [
            [
                "letter" => "A",
                "index" => "HUB_ID",
                "empty" => true
            ],
            [
                "letter" => "B",
                "index" => "HUB Location"
            ],
            [
                "letter" => "C",
                "index" => "Vendor"
            ],
            [
                "letter" => "D",
                "index" => "Customer Code"
            ],
            [
                "letter" => "E",
                "index" => "Name of the Customer"
            ],
            [
                "letter" => "F",
                "index" => "BILLING TO"
            ],
            [
                "letter" => "G",
                "index" => "GST NO."
            ],
            [
                "letter" => "H",
                "index" => "PAN"
            ],
            [
                "letter" => "K",
                "index" => "Address 1"
            ],
            [
                "letter" => "L",
                "index" => "Address 2"
            ],
            [
                "letter" => "M",
                "index" => "Pin Code"
            ],
            [
                "letter" => "N",
                "index" => "State"
            ]
        ];
        return $columns;
    }

    
    public function importCmsColumns()
    {
        $columns = [
            [
                "letter" => "A",
                "index" => "txn_id"
            ],
            [
                "letter" => "B",
                "index" => "date_time"
            ],
            [
                "letter" => "C",
                "index" => "phone"
            ],
            [
                "letter" => "D",
                "index" => "name"
            ],
            [
                "letter" => "E",
                "index" => "vendor_name"
            ],
            [
                "letter" => "F",
                "index" => "email"
            ],
            [
                "letter" => "G",
                "index" => "charging_station"
            ],
            [
                "letter" => "H",
                "index" => "lat_lng"
            ],
            [
                "letter" => "I",
                "index" => "charge_point"
            ],
            [
                "letter" => "J",
                "index" => "charge_point_code"
            ],
            [
                "letter" => "K",
                "index" => "connector_id"
            ],
            [
                "letter" => "L",
                "index" => "connector_type"
            ],
            [
                "letter" => "M",
                "index" => "charge_point_type"
            ],
            [
                "letter" => "N",
                "index" => "charging_station_category"
            ],
            [
                "letter" => "O",
                "index" => "start_time"
            ],
            [
                "letter" => "P",
                "index" => "stop_time"
            ],
            [
                "letter" => "Q",
                "index" => "duration_seconds"
            ],
            [
                "letter" => "R",
                "index" => "duration_hh_mm"
            ],
            [
                "letter" => "S",
                "index" => "meter_start_wh"
            ],
            [
                "letter" => "T",
                "index" => "meter_stop_wh"
            ],
            [
                "letter" => "U",
                "index" => "start_soc"
            ],
            [
                "letter" => "V",
                "index" => "stop_soc"
            ],
            [
                "letter" => "W",
                "index" => "energy_delivered_kWh"
            ],
            [
                "letter" => "X",
                "index" => "unit_rate_applicable"
            ],
            [
                "letter" => "Y",
                "index" => "charging_session_cost"
            ],
            [
                "letter" => "Z",
                "index" => "service_fee"
            ], [
                "letter" => "AA",
                "index" => "service_fee_for_minutes"
            ],
            [
                "letter" => "AB",
                "index" => "gst"
            ],
            [
                "letter" => "AC",
                "index" => "previous_unpaid_amount"
            ],
            [
                "letter" => "AD",
                "index" => "payment_processing_fee"
            ],
            [
                "letter" => "AE",
                "index" => "total"
            ],
            [
                "letter" => "AF",
                "index" => "refund_amount"
            ],
            [
                "letter" => "AG",
                "index" => "payment_method"
            ],
            [
                "letter" => "AH",
                "index" => "fleet"
            ],
            [
                "letter" => "AI",
                "index" => "vehicle_number_plate"
            ]
        ];
        return $columns;
    }

}
