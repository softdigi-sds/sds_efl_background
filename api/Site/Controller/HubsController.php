<?php

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartData as Data;
use Site\Helpers\HubsHelper;
use Site\Helpers\HubGroupsHelper;
use Site\Helpers\VendorRateHelper;



class HubsController extends BaseController
{

    private HubGroupsHelper $_hub_group_helper;
    private HubsHelper $_helper;
    private VendorRateHelper $_vendor_rate_helper;
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new HubsHelper($this->db);
        $this->_vendor_rate_helper = new VendorRateHelper($this->db);
        $this->_hub_group_helper = new HubGroupsHelper($this->db);
    }

    /**
     * k
     */
    public function insert()
    {
        $columns = ["hub_id", "hub_name", "sd_efl_office_id"];
        // do validations
        $this->_helper->validate(HubsHelper::validations, $columns, $this->post);
        $columns[] = "created_by";
        $columns[] = "created_time";
        $this->post["sd_efl_office_id"] = Data::post_select_value("sd_efl_office_id");
        $data = $this->_helper->checkHubExist($this->post["hub_id"]);
        if (!empty($data)) {
            \CustomErrorHandler::triggerInvalid("Hub ID already exist ");
        }
        $this->db->_db->Begin();
        // insert and get id
        $id = $this->_helper->insert($columns, $this->post);
        // insert roles
        if (!($this->post["role"]) == NULL) {
            $this->_hub_group_helper->insertRoles($id, $this->post["role"]);
        }
        $this->db->_db->commit();
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
        $columns = ["hub_name", "hub_location"];
        // do validations
        $this->_helper->validate(HubsHelper::validations, $columns, $this->post);
        // extra columns
        $columns[] = "last_modified_by";
        $columns[] =  "last_modified_time";
        // begin transition
        $this->db->_db->Begin();
        // insert and get id
        $this->_helper->update($columns, $this->post, $id);
        // insert roles
        if (!($this->post["role"]) == NULL) {
            $this->_hub_group_helper->insertRoles($id, $this->post["role"]);
        } else {
            // delete from user role table
            $this->_hub_group_helper->deleteHubRole($id);
        }
        //
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
        $data = $this->_vendor_rate_helper->checkVenodrByHubId($id);
        if (!empty($data)) {
            \CustomErrorHandler::triggerInvalid("Cannot remove Hub, Hub is allocated to a vendor.");
        }
        // insert and get id
        $this->_helper->deleteOneId($id);

        $out = new \stdClass();
        $out->msg = "Removed Successfully";
        $this->response($out);
    }
    /**
     * 
     */
    public function getAllSelect()
    {
        $select = ["t1.ID as value,t1.hub_id as label"];
        $data = $this->_helper->getAllData("", [], $select);
        $this->response($data);
    }
}
