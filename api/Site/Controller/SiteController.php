<?php 

namespace Site\Controller;

use Core\BaseController;
use Core\Helpers\SmartFileHelper;

use Core\Helpers\SmartAuthHelper;
use Site\Helpers\SiteHelper as SiteHelper;


class SiteController extends BaseController{
    
    private SiteHelper $_site_helper;

    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_site_helper = new SiteHelper($this->db);
    }

   /**
     * 
     */
    public function insert(){
        // only insert function there is not update : setting will come in the form of array 
        foreach($this->post as $key=>$value){
            // check whether key exists in settings : if exists get id
            $exists_data = $this->_site_helper->getOneSettingData($key);
            // var_dump($exists_data);
            $data = ["setting_value"=>$value];            
            if(!$exists_data){
                $columns = [ "setting_name","setting_value","created_by"];
                $data["setting_name"] = $key;
                $id = $this->_site_helper->insert($columns,$data);
            }else{
                $id = $exists_data->ID;
                $columns =  [ "setting_value","last_modified_time"];
                $this->_site_helper->update($columns,$data,$id);
            }

        }
        // add log
        $this->addLog("ADDED A NEW SETTING","",SmartAuthHelper::getLoggedInUserName());
        //
        $this->response("added");
    }
  

    public function getAll(){      
        // this function as to be modified 
        $data = $this->_site_helper->getAllData();
        //var_dump($data);
        $out = new \stdClass();
        foreach($data as $obj){
            $out->{$obj->setting_name} = $obj->setting_value;
        }
        $this->response($out);
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
        $data = $this->_site_helper->getOneData($id);
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
        // insert and get id
        $this->_site_helper->deleteOneId($id);
        // add log
        $this->addLog("DELETED A SETTING","",SmartAuthHelper::getLoggedInUserName());
        //
        $out = new \stdClass();
        $out->msg = "Deleted Successfully";
        $this->response($out);
    }
    /**
     * 
     */
    public function getAllSelect(){      
        $select = ["ID as value,setting_name as label"];
        $data = $this->_site_helper->getAllData("",[],$select);
        $this->response($data);
    }


}