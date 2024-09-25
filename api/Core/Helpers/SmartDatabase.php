<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Database
 *
 * @author kms
 */
namespace Core\Helpers;

use Core\Helpers\SmartLogger as Logger;

use PDO;

class SmartDatabase {
    //put your code here
    private $_host;  
    private $_user;  
    private $_pass; 
    private $_dbname;
    private $_port;
    
    private $dbh;
    private $error;
    private $stmt;

    //
     // Create a flag to track if a transaction is in progress
    private $transactionInProgress = false;
    
    private static $_instance = null;
    /**
     * 
     */
    public function __construct(){ 
        $this->_host = $_ENV["DB_HOST"]; 
        $this->_user = $_ENV["DB_USER"]; 
        $this->_pass = $_ENV["DB_PASS"]; 
        $this->_dbname = $_ENV["DB_NAME"]; 
        $this->_port = isset($_ENV["DB_PORT"]) ? $_ENV["DB_PORT"]: 3306;
        $this->connectdb();
    }
    /**
     * 
     */
    public static function get_instance(){
        if(!isset(self::$_instance)){
           self::$_instance = new SmartDatabase();
        }
        return self::$_instance;
    }
    /**
     * 
     */
    private function connectdb(){
         // Set DSN
        $dsn = 'mysql:host='. $this->_host . ':'.$this->_port.';dbname=' . $this->_dbname.";
        charset=utf8";
        // Set options
        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );
        // Create a new PDO instanace
        try{
            $this->dbh = new PDO($dsn, $this->_user, $this->_pass, $options);
        }
        // Catch any errors
        catch(\PDOException $e){
            $this->trigger_error_message($e);
        } 
    }
    //
    public function Begin(){
        if ($this->dbh->inTransaction()) {
            $this->transactionInProgress = true;
        } else {
           // echo "BEGIN TRANASACTION <br/>";
            // Start a new transaction
            $this->dbh->beginTransaction();
            $this->transactionInProgress = true;
        }         
     }

     public function commit(){
        // If everything is successful, commit the transaction
        if($this->transactionInProgress){
           // echo "COMMIT TRANASACTION <br/>";
            $this->dbh->commit();
            $this->transactionInProgress = false;
        }
        
     }

     
     public function RollBack(){
       // echo "ROLL BACK called <br/>";
        // If everything is successful, commit the transaction
        if($this->transactionInProgress){
           // echo "ROLL BACK TRANASACTION <br/>";
            $this->dbh->rollBack();
            $this->transactionInProgress = false;
        }       
     }


    /**
     * 
     */
    private function trigger_error_message($e){
         //var_dump($e);
         $msg = $e->getMessage();
         \CustomErrorHandler::triggerDbError($msg);
    }
    
    /*
     * 
     * 
     */
    public function query($query){   
        try {
          // echo "quer y = " . $query;
           $this->stmt = $this->dbh->prepare($query);        

          } catch (\PDOException $e) {
            $this->trigger_error_message($e);             
            die();
       } 
    }
    /*
     * 
     */
    public function bind($param, $value, $type = null){
            if (is_null($type)) {
                switch (true) {
                    case is_int($value):
                        $type = PDO::PARAM_INT;
                        break;
                    case is_bool($value):
                        $type = PDO::PARAM_BOOL;
                        break;
                    case is_null($value):
                        $type = PDO::PARAM_NULL;
                        break;
                    default:
                        $type = PDO::PARAM_STR;
                }
            }
            $this->stmt->bindValue($param, $value, $type);
    }
    /**
     * 
     * @return type
     */
    public function execute(){
        try{
            $return = $this->stmt->execute(); 
            return $return;
         } catch(\PDOException $e){             
            $this->trigger_error_message($e);        
        }
    }
        
        /*
         * 
         * 
         */
        public function resultset(){
            $this->execute();
            return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        }
        
        /*
         * 
         */
        public function single(){
            $this->execute();
            return $this->stmt->fetch(PDO::FETCH_OBJ);
        }
        
        /*
         * 
         */
        public function rowCount(){
            return $this->stmt->rowCount();
        }
        
        /*
         * 
         */
       public function lastInsertId(){
            return $this->dbh->lastInsertId();
        }
        
      // insert the data in database  
      public function InsertData($table,$data) {
         $insertData = is_object($data) ? (array)$data : $data;
         $keys = array_keys( $insertData);
         // query preparation
         $sql = "INSERT INTO ".$table."";
         $sql .= "(". implode(",", $keys)  .") VALUES (:". implode(", :", $keys).")";
          // preparation of the query 
         $this->prepare_sql_with_param($sql, $insertData);
         try{           
            $this->execute();
            return  $this->lastInsertId();
          // return $insertData;
         }catch (\PDOException $e){
            $this->trigger_error_message($e);
         }
      }
      /**
       * 
       * @param type $table
       * @param type $data
       * @param type $where_sql
       * @param type $where_parameters
       */
       public function UpdateData($table,$data,$where_sql) {
          $insertData = is_object($data) ? (array)$data : $data;
          
          $keys = array_keys( $insertData);
          
          $sql = "UPDATE ".$table." SET ";
          //echo "SQL1 = " . $sql;
          $temp = array();
          
          foreach($keys as $key) { $temp[] = $key." = :".$key;}
         // var_dump($temp);
          $sql .= implode(", ",$temp);         
          // get the where key from sql          
          $sql .= " WHERE " . $where_sql;

        
         // var_dump($insertData);
          $this->prepare_sql_with_param($sql, $insertData);
         try{           
           $this->execute();  
           // var_dump($ret);
            return $this->rowCount();
         }catch (\PDOException $e){
            $this->trigger_error_message($e);
         }
          
       }
      
      // prepare and check exeute the sql
      private function prepare_sql_with_param($sql,$data){          
         $this->query($sql);         
         if(!is_array($data) || count($data) < 1) { return false;}
       //  Logger::info(json_encode($data),"SQL INSERT DATA");
         foreach( $data as $key=>$value) {
            $this->bind(':'.$key,($value));
         }
      }
       /**
        * 
        * @param type $table
        * @param type $sql_str
        * @param type $data
        */        
      public function DeleteData($table,$sql_str,$data){
         $sql = "DELETE FROM ".$table." WHERE ".$sql_str."";
         $final_data = is_object($data) ? (array)$data : $data;
         $this->prepare_sql_with_param($sql, $final_data);
         try{           
           $this->execute();  
           return $this->rowCount();
           //   return $final_data;
          }catch (\PDOException $e){
            $this->trigger_error_message($e);
         }
       }
       /**
        * 
        * @param type $sql_str
        * @param type $data
        * @param type $single
        */
       public function getData($sql_str,$data,$single=false){  
        // final data preparations      
         $final_data = is_object($data) ? (array)$data : $data;     
         // preprae or bind the data if required  
         $this->prepare_sql_with_param($sql_str, $final_data);
         //echo $sql_str;
         try{ 
           // return data as per the query
           return $single==true ? $this->single() : $this->resultset();
           //   return $final_data;
          }catch (\PDOException $e){
             // var_dump($e);
            $this->trigger_error_message($e);
         }
       }
       /**
        * 
        * @param type $table
        */
       public function fetchAllData($table){
           $sql = "SELECT *,ID as id FROM " . $table;
           $this->query($sql);
           try{           
            return  $this->resultset();        
            }catch (\PDOException $e){
                $this->trigger_error_message($e);
            }
       }
      
      
     
      
      
 }  
