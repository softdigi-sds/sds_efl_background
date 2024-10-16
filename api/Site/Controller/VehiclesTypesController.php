<?php

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartData as Data;


use Site\Helpers\VehiclesTypesHelper;



class VehiclesTypesController extends BaseController
{

    private VehiclesTypesHelper $_helper;

    function __construct($params)
    {
        parent::__construct($params);

        $this->_helper = new VehiclesTypesHelper($this->db);
    }

    /**
     * 
     */
    public function insert()
    {
        $columns = ["vehicle_type"];
        // do validations
        $this->_helper->validate(VehiclesTypesHelper::validations, $columns, $this->post);
        $columns[] = "created_by";
        $columns[] = "created_time";
        // insert and get id
        $id = $this->_helper->insert($columns, $this->post);

        //
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
    public function getAllSelect()
    {
        $select = ["t1.ID as value", "t1.vehicle_type as label"];
        $data = $this->_helper->getAllData("", [], $select);
        $this->response($data);
    }
}
