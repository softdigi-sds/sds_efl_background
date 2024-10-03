<?php

namespace Site\Controller;

use Core\Helpers\SmartData as Data;
use Core\BaseController;
use Site\Helpers\VendorRateHelper as VendorRateHelper;
use Site\Helpers\VendorRateSubHelper as VendorRateSubHelper;


class VendorRateController extends BaseController
{

    private VendorRateHelper $_helper;
    private VendorRateSubHelper $_sub_helper;

    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new VendorRateHelper($this->db);
        //
        $this->_sub_helper = new VendorRateSubHelper($this->db);
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
        $data = $this->_helper->checkEffectiveDateClash($this->post["effective_date"]);
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
        foreach($data as $obj){
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
}
