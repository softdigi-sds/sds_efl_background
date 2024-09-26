<?php 

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Core\Helpers\SmartData as Data;
use Site\Helpers\EflOfficeHelper;
use Site\Helpers\HubsHelper;



class EflOfficeController extends BaseController{
  
  private EflOfficeHelper $_helper;
  private HubsHelper $_hubs_helper;
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new EflOfficeHelper($this->db);
        $this->_hubs_helper = new HubsHelper($this->db);
    }

   /**
     * 
     */
    public function insert(){
        $columns = [ "office_city" ,"address_one","gst_no" ,"pan_no" ,
        "cin_no"  ,"state"  ,"pin_code"  ];
        // do validations
        $this->_helper->validate(EflOfficeHelper::validations,$columns,$this->post);
        $columns[] = "status";
        $columns[] = "created_by" ;
        $columns[] = "created_time" ;
        // var_dump($this->post["state"]);exit();
        $state = $this->post["state"];
        $this->post["state"]  = $state["value"];
        $this->post["status"] = 5;
        // check office already exist
        $data = $this->_helper->checkOfficeExist($this->post["office_city"]);
        if (!empty($data)) {
            \CustomErrorHandler::triggerInvalid("City Office already available ");
        }
        // insert and get id
         $id = $this->_helper->insert($columns,$this->post);
       
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
        $columns = ["address_one", "address_two", "gst_no" ,"pan_no" ,
        "cin_no"  ,"state"  ,"pin_code","status" ];
        // do validations
        $this->_helper->validate(EflOfficeHelper::validations, $columns, $this->post);
        // extra columns
        $columns[] = "last_modified_by";
        $columns[] =  "last_modified_time";
        // begin transition
        $this->db->_db->Begin();
          // insert and get id
        $id = $this->_helper->update($columns, $this->post, $id);
        $this->db->_db->commit();
        $this->response($id);
    }
 /**
     * 
     */
    public function getAll(){      
        $data = $this->_helper->getAllData();
        $this->response($data);
    }
    /**
     * 
     */
    public function getOne(){  
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if($id < 1){
            \CustomErrorHandler::triggerInvalid("Invalid ID");
        }    
        // insert and get id
        $data = $this->_helper->getOneData($id);
        $this->response($data);
    }
    /**
     * 
     */
    public function deleteOne(){  
        $id = isset($this->post["id"]) ? intval($this->post["id"]) : 0;
        if($id < 1){
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
        $this->response($out);

    }    
     /**
     * 
     */
    public function getAllSelect(){      
        $select = ["ID as value,office_city as label"];
        $data = $this->_helper->getAllData("",[],$select);
        $this->response($data);
    }

}