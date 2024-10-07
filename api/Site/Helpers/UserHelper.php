<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Helpers;

use Core\BaseHelper;
use Core\Helpers\SmartConst;
use Core\Helpers\SmartGeneral;
use Core\Helpers\SmartLogger;


use Core\Helpers\SmartSiteSettings;
use Site\Helpers\TableHelper as Table;


/**
 * Description of Data
 * 
 *  class helps to get the data from post with specified type 
 *
 * @author kms
 */
class UserHelper extends BaseHelper
{

    const schema = [
        "ename" => SmartConst::SCHEMA_VARCHAR,
        "euserid" => SmartConst::SCHEMA_VARCHAR,
        "epassword" => SmartConst::SCHEMA_VARCHAR,
        "emailid" => SmartConst::SCHEMA_VARCHAR,
        "mobile_no" => SmartConst::SCHEMA_VARCHAR,
        "designation" => SmartConst::SCHEMA_VARCHAR,        
        "profile_img" => SmartConst::SCHEMA_VARCHAR,
        "last_login_time" => SmartConst::SCHEMA_CDATETIME,
        "last_login_ip" => SmartConst::SCHEMA_VARCHAR,
        "last_reset_time" => SmartConst::SCHEMA_CDATETIME,
        "change_pass" => SmartConst::SCHEMA_INTEGER,
        "created_time" => SmartConst::SCHEMA_CDATETIME,
        "created_by" => SmartConst::SCHEMA_CUSER_ID,
        "active_status" => SmartConst::SCHEMA_INTEGER,
        "failed_attempts" => SmartConst::SCHEMA_INTEGER,
    ];
    /**
     * 
     */
    const validations = [
        "ename" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Username "
            ], 
            [
                "type" => SmartConst::VALID_MAX_LENGTH,
                "max"=>100,
                "msg"=>"Maximum Length For Employee Name is 100 characters"
            ]           
        ],
        // "euserid" => [
        //     [
        //         "type" => SmartConst::VALID_REQUIRED,
        //         "msg" => "Please Enter Userid"
        //     ],
        //     [
        //         "type" => SmartConst::VALID_MAX_LENGTH,
        //         "max"=>100,
        //         "msg"=>"EuserId Max character 100"
        //     ]
         
        // ],
        "epassword" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Password"
            ],
            [
                "type" => SmartConst::VALID_MAX_LENGTH,
                "max"=>256,
                "msg"=>"Epassword Max character 256"
            ]
        ],
        "currentPassword" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Current Password"
            ],
        ],
        "newPassword"=>[
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter New Password"
            ]
        ],
        "confirmPassword"=>[
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Confirm Password"
            ] 
         ],
        "emailid" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Enter Email"
            ],
            [
                "type" => SmartConst::VALID_MAX_LENGTH,
                "max"=>100,
                "msg"=>"Mail ID Max character 100"
            ]
        ],
        "mobile_no" => [
            [
                "type" => SmartConst::VALID_NUM,
                "msg" => "Please enter a valid Mobile number"
            ],
            [
                "type" => SmartConst::VALID_MAX_LENGTH,
                "max"=>10,
                "msg"=>"Mobile Number Number Max character"
            ]
        ],
        "designation" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please Specify Designation"
            ]
        ],
       
        "active_status" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please prefer your Status"
            ]
        ],
        "profile_img" => [
            [
                "type" => SmartConst::VALID_REQUIRED,
                "msg" => "Please prefer your Status"
            ]
        ]

    ];
    /**
     * 
     */
    public function insert(array $columns, array $data)
    {
        return $this->insertDb(self::schema, Table::USERS, $columns, $data);
    }
    /**
     * 
     */
    public function update(array $columns, array $data, int $id)
    {
        return $this->updateDb(self::schema, Table::USERS, $columns, $data, $id);
    }
    /**
     * 
     */
    public function getAllData($sql = "", $data_in = [], $select_in=[], $group_by = "", $count = false)
    {
        $from = Table::USERS." t1 ";
        $select = ["t1.*"];
        if(empty($sql)){
        $sql = "euserid <> 'admin' ";
        }
        if(!empty($select_in)){
            $select = $select_in;
        }
        $order_by="t1.created_time DESC";
        return $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, false, [], $count);
    }
   
    public function getSelectedRolesWithUserId(int $role_id) {
        $sql = "t1.sd_mt_role_id =:ID";
        $select = ["t1.sd_mt_role_id as value", "t3.role_name as label"];
        return $this->getAllData($sql, ["ID" => $role_id], $select);
    }
    
    
    
    /**
     * 
     */
    public function getOneData($id)
    {
        $from = Table::USERS." t1  ";
        $select = ["t1.*"];
       // $select[] = "(SELECT GROUP_CONCAT(t2.sd_mt_role_id) FROM ".Table::USERROLE." t2 WHERE t2.sd_mt_userdb_id=t1.ID) as role";
        $sql = "t1.ID=:ID";
        $data_in = ["ID" => $id];
        $group_by = "";
        $order_by = "";
        $data = $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
        if(isset($data->ID)){
            $user_role_helper = new UserRoleHelper($this->db);
            $data->role = $user_role_helper->getSelectedRolesWithUserId($data->ID);
        }
        return $data;
    }
    /**
     * 
     */
    public function getOneDataWithUserId($userid)
    {
        $from = Table::USERS." t1 ";
        $select = ["t1.*"];
        $sql = "t1.euserid=:ID";
        $data_in = ["ID" => $userid];
        $group_by = "";
        $order_by = "";
        return $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
    }
    /**
     * 
     */
    public function getOneDataWithEmailId($emailid)
    {
        $from = Table::USERS." t1 ";
        $select = ["t1.*"];
        $sql = "t1.emailid=:ID";
        $data_in = ["ID" => $emailid];
        $group_by = "";
        $order_by = "";
        return $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, true, []);
    }
    /**
     * 
     */
    public function deleteOneId($id)
    {
        $from = Table::USERS;
        $this->deleteId($from,$id);
    }
    /**
     * 
     */
    public function getLoginCount($type){
        $sql = "DATE(last_login_time)=CURRENT_DATE()";
        if($type==1){
            $sql = "MONTH(last_login_time)=" . SmartGeneral::getMonth();
        }else if($type==2){
            $sql = "YEAR(last_login_time)=" . SmartGeneral::getYear();
        }
        $data =  $this->getAllData($sql,[],[],"",true);
         return isset($data) ? count($data) : 0;
    }

    public function getRecentUsers(){
        $from = Table::USERS;
        $select = ["*"];
        $sql = "euserid <> 'admin' ";
        $data_in = [];
        $group_by = "";
        $order_by = "last_login_time DESC";
        $limit=["10"];
        return $this->getAll($select, $from, $sql, $group_by, $order_by, $data_in, false, [],$limit);
    }
    
    /**
     * 
     */
    public function updateLastLogin($id){
       $columns = ["last_login_time","last_login_ip","failed_attempts"];
       $post = ["last_login_ip"=>SmartLogger::get_client_ip(),"failed_attempts"=>"0"];
       $this->update($columns,$post,$id);  
    }
     /**
     * 
     */
    public function checkFailedAttempts($user_data){
        $size = SmartSiteSettings::getSetting("failed_password_attempts",2);
        if(($user_data->failed_attempts) >= $size){
            \CustomErrorHandler::triggerInvalid("Account Locked, Please Contact Administrator");
        }
    }
    /**
     * 
     */
    public function updateFailedAttempts($user_data){   
        $failedCount = $user_data->failed_attempts + 1;
        $columns=["failed_attempts"];
        $post = ["failed_attempts"=>$failedCount];
        $this->update($columns,$post,$user_data->ID);
     }   
   

}
