<?php

namespace Site\Controller;

use Core\Helpers\SmartData as Data;
use Core\BaseController;
use Core\Helpers\SmartData;
use Site\Helpers\VendorRateHelper as VendorRateHelper;
use Site\Helpers\VendorRateSubHelper as VendorRateSubHelper;
use Site\Helpers\HubsHelper;
use Site\Helpers\VendorsHelper;

class VendorRateController extends BaseController
{

    private VendorRateHelper $_helper;
    private VendorRateSubHelper $_sub_helper;
    private HubsHelper $_hubs_helper;
    private VendorsHelper $_vendor_helper;


    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new VendorRateHelper($this->db);
        //
        $this->_sub_helper = new VendorRateSubHelper($this->db);
        // 
        $this->_hubs_helper = new HubsHelper($this->db);
        //
        $this->_vendor_helper = new VendorsHelper($this->db);
    }

    /**
     * 
     */
    public function insert()
    {
        $columns = ["sd_hubs_id", "sd_vendors_id", "effective_date"];
        // do validations
        $this->_helper->validate(VendorRateHelper::validations, $columns, $this->post);
        // columns     
        $columns[] = "created_time";
        $columns[] = "created_by";
        // data
        $this->post["sd_hubs_id"] = Data::post_select_value("sd_hubs_id");
        $this->post["sd_vendors_id"] = Data::post_select_value("sd_vendors_id");
        $data = $this->_helper->checkEffectiveDateClash($this->post["sd_vendors_id"], $this->post["effective_date"]);
        // var_dump($data);exit();
        if (!empty($data)) {
            \CustomErrorHandler::triggerInvalid("There is already an Effective date available upto " . $data->effective_date);
        }
        // $this->db->_db->Begin();
        // insert and get id
        $id = $this->_helper->insert($columns, $this->post);
        // after that insert the sub data 
        $rate_data = Data::post_array_data("rate_data");
        // 
        $this->_sub_helper->insert_update_data($id, $rate_data);
        //  exit();
        $this->db->_db->commit();
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
        $this->db->_db->Begin();
        $columns = [];
        $columns[] = "last_modified_time";
        $columns[] = "last_modified_by";
        $this->_helper->update($columns, $this->post, $id);
        //
        $rate_data = Data::post_array_data("rate_data");
        // 
        $this->_sub_helper->insert_update_data($id, $rate_data);
        $this->db->_db->commit();
        $this->response($id);
    }


    public function getAll()
    {
        // insert and get id
        $data = $this->_helper->getAllData();
        $out = [];
        foreach ($data as $obj) {
            $obj->rates =  $this->_sub_helper->getAllByVendorRateId($obj->ID);
            $out[] = $obj;
        }
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
        //
        if (isset($data->ID)) {
            $data->rate_data = $this->_sub_helper->getAllByVendorRateId($data->ID);
        }
        $this->response($data);
    }
    /**
     * getting rates with one single vendor
     */

    public function getOneVendor()
    {
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if ($id < 1) {
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }
        $_date = SmartData::post_data("_date", "DATE");
        // insert and get id
        $data = $this->_helper->getOneWithEffectiveDate($id, $_date);
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
        $out->msg = "Deleted Successfully";
        $this->response($out);
    }

    /*
     { value: "1", label: "Parking & Charging" },
    { value: "2", label: "Parking" },
    { value: "3", label: "Charging (AC)" },
    { value: "5", label: "Charging (DC)" },
    { value: "4", label: "Rent" },

     { value: "1", label: "Fixed" },
    { value: "2", label: "Minimum" },
    { value: "3", label: "Per Unit" },
     */


    private function insertMain($_data)
    {
        $columns = ["sd_hubs_id", "sd_vendors_id", "effective_date", "created_time", "created_by"];
        $id = $this->_helper->insert($columns, $_data);
        // after that insert the sub data 
        $rate_data = $_data["rate_data"];
        var_dump($_data);
        var_dump($rate_data);
        $this->_sub_helper->insert_update_data($id, $rate_data);
        //  exit();
    }

    private function getSingleData($hsn, $rate_type, $min_start, $min_end, $price, $extra, $min)
    {
        return [
            "sd_hsn_id" => ["value" => $hsn],
            "rate_type" => ["value" => $hsn],
            "min_start" => $min_start,
            "min_end" => $min_end,
            "price" => $price,
            "extra_price" => $extra,
            "min_units_vehicle" => $min
        ];
    }

    private function process_rate($ven_data, $obj)
    {
        $_data = [
            "sd_hubs_id" => $ven_data->sd_hub_id,
            "sd_vendors_id" => $ven_data->ID,
            "effective_date" => "2024-07-01",
            "rate_data" => []
        ];
        if ($obj->type == "PC") {
            // check ranges -1 2, 3
            if (isset($obj->range_one) && strlen($obj->range_one) > 2) {
                $exploded = explode("-", $obj->range_one);
                $_data["rate_data"][] = $this->getSingleData(1, 2, $exploded[0], $exploded[1], $obj->rate_one, $obj->extra_price, $obj->min_units);
            }
            if (isset($obj->range_two) && strlen($obj->range_two) > 2) {
                $exploded = explode("-", $obj->range_two);
                $_data["rate_data"][] = $this->getSingleData(1, 2, $exploded[0], $exploded[1], $obj->rate_two, $obj->extra_price, $obj->min_units);
            }
            if (isset($obj->range_three) && strlen($obj->range_three) > 2) {
                $exploded = explode("-", $obj->range_three);
                $_data["rate_data"][] = $this->getSingleData(1, 2, $exploded[0], $exploded[1], $obj->rate_three, $obj->extra_price, $obj->min_units);
            }
        } else if ($obj->type == "P-C") {
            if (isset($obj->range_one) && strlen($obj->range_one) > 2) {
                $exploded = explode("-", $obj->range_one);
                $_data["rate_data"][] = $this->getSingleData(2, 2, $exploded[0], $exploded[1], $obj->rate_one, 0, 0);
            }
            if (isset($obj->extra_price) && intval($obj->extra_price) > 0) {
                $_data["rate_data"][] = $this->getSingleData(3, 3, 0, 0, $obj->extra_price, 0, 0);
            }
        } else if ($obj->type == "C") {
            if (isset($obj->extra_price) && intval($obj->extra_price) > 0) {
                $_data["rate_data"][] = $this->getSingleData(3, 3, 0, 0, $obj->extra_price, 0, 0);
            }
        } else if ($obj->type == "P") {
            if (isset($obj->range_one) && strlen($obj->range_one) > 2) {
                $exploded = explode("-", $obj->range_one);
                $_data["rate_data"][] = $this->getSingleData(2, 2, $exploded[0], $exploded[1], $obj->rate_one, 0, 0);
            }
        }
        if (count($_data['rate_data']) > 0) {
            $this->insertMain($_data);
        }
    }

    public function migrate()
    {
        $_data = $this->_helper->getData();
        $i = 1;
        foreach ($_data as $obj) {
            if ($obj->hub !== "HUB_ID") {
                $v_data = $this->_vendor_helper->checkVendorByCodeCompanyWithHub("#", $obj->vendor, $obj->hub);
                // $hub_id = $this->_hubs_helper->getHubID($obj->hub);
                if (isset($v_data->ID)) {
                    // hub availle go inside
                    $this->process_rate($v_data, $obj);
                    // if ($i > 2) {
                    //    break;
                    //  }
                    $i++;
                } else {
                    // echo $i . " Hub/ Vendor  Not Availble " . $obj->hub . "  =  " . $obj->vendor . " <br/>";
                    $i++;
                }
                //var_dump($obj);
            }
        }
        //var_dump($_data);
    }
}
