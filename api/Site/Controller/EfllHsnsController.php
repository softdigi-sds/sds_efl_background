<?php 

namespace Site\Controller;

use Core\BaseController;

use Core\Helpers\SmartAuthHelper;
use Site\Helpers\EflHsnsHelper;



class EfllHsnsController extends BaseController{
  
  private EflHsnsHelper $_helper;
    function __construct($params)
    {
        parent::__construct($params);
        // 
        $this->_helper = new EflHsnsHelper($this->db);
    }
 /**
     * 
     */
    public function insert(){
        $columns = [   "hsn","bill_title" ];
        // do validations
        $this->_helper->validate(EflHsnsHelper::validations,$columns,$this->post);
        $columns[] =  "last_modified_time";
      
        // insert and get id
         $id = $this->_helper->insert($columns,$this->post);
       
        //
         $this->response($id);
    }
   /**
     * 
     */

    public function getAllSelect(){      
        $select = ["ID as value, hsn as label"];
        $data = $this->_helper->getAllData("",[],$select);
        $this->response($data);
    }

}