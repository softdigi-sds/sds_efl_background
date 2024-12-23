<?php

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartData as Data;
use Site\Helpers\EflOfficeHelper;
use Site\Helpers\HubsHelper;
use Site\Helpers\EflOfficeGroupsHelper;



class EflOfficeController extends BaseController
{

    private EflOfficeHelper $_helper;
    private HubsHelper $_hubs_helper;
    private EflOfficeGroupsHelper $_group_helper;
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new EflOfficeHelper($this->db);
        $this->_hubs_helper = new HubsHelper($this->db);
        $this->_group_helper = new EflOfficeGroupsHelper($this->db);
    }

    /**
     * 
     */
    public function insert()
    {
        $columns = [
            "office_city",
            "address_one",
            "gst_no",
            "pan_no",
            "cin_no",
            "state",
            "pin_code"
        ];
        // do validations
        $this->_helper->validate(EflOfficeHelper::validations, $columns, $this->post);
        $other_columns = ["address_two", "status", "created_by", "created_time", "cgst", "igst", "sgst"];
        $columns = array_merge($columns, $other_columns);
        $this->post["state"] = Data::post_select_value("state");
        $this->post["status"] = 5;
        // check office already exist
        $data = $this->_helper->checkOfficeExist($this->post["office_city"]);
        if (!empty($data)) {
            \CustomErrorHandler::triggerInvalid("City Office already available ");
        }
        // insert and get id
        $id = $this->_helper->insert($columns, $this->post);
        // INSERT AND UPDATE SUB TINGS
        if (!($this->post["role"]) == NULL) {
            $this->_group_helper->insertRoles($id, $this->post["role"]);
        }
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
        $columns = [
            "address_one",
            "gst_no",
            "pan_no",
            "cin_no",
            "state",
            "pin_code",
            "status"
        ];
        // do validations
        $this->_helper->validate(EflOfficeHelper::validations, $columns, $this->post);
        $other_columns = ["address_two", "last_modified_by", "last_modified_time", "created_time", "cgst", "igst", "sgst"];
        $columns = array_merge($columns, $other_columns);
        // data
        $this->post["state"] = Data::post_select_value("state");
        // begin transition
        $this->db->_db->Begin();
        // insert and get id
        $id = $this->_helper->update($columns, $this->post, $id);
        if (!($this->post["role"]) == NULL) {
            $this->_group_helper->insertRoles($id, $this->post["role"]);
        } else {
            // delete from user role table
            $this->_group_helper->deleteHubRole($id);
        }
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
        if(isset($data->ID)){ 
           $data->role = $this->_group_helper->getSelectedRolesWithHubId($data->ID);
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
        $data = $this->_hubs_helper->checkHubByOfficeId($id);
        if (!empty($data)) {
            \CustomErrorHandler::triggerInvalid("Cannot remove City Office, Hub is attached with this City.");
        }
        // insert and get id
        $this->_helper->deleteOneId($id);
        $out = new \stdClass();
        $out->msg = "Removed Successfully";
        $this->response(data: $out);
    }
    /**
     * 
     */
    public function getAllSelect()
    {
        $select = ["t1.ID as value,t1.office_city as label"];
        $data = $this->_helper->getAllData("", [], $select);
        $this->response($data);
    }
}
