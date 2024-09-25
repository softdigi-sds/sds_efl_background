<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Helpers;

/**
 * Description of Data
 * 
 *  class helps to get the data from post with specified type 
 *
 * @author kms
 */
class SmartConst {
   // request types
   const  REQUEST_GET="GET";
   const  REQUEST_POST="POST";
   const  REQUEST_DELETE="DELETE";

   // schema types
   const SCHEMA_INTEGER = "INTEGER";
   const SCHEMA_FLOAT = "FLOAT";
   const SCHEMA_VARCHAR = "VARCHAR";
   const SCHEMA_TEXT = "TEXT";
   const SCHEMA_DATE = "DATE";
   const SCHEMA_CDATE = "CDATE";
   const SCHEMA_CTIME = "CTIME";
   const SCHEMA_CDATETIME = "CDATETIME";
   const SCHEMA_CUSER_ID = "CID";
   const SCHEMA_CUSER_USERID = "CUSERID";
   const SCHEMA_CUSER_USERNAME = " CUSERNAME";


   // validation types
   const VALID_REQUIRED = "required";
   const VALID_STRING = "string";
   const VALID_NUM = "number";
   const VALID_MIN = "min";
   const VALID_MAX = "max";
   const VALID_MIN_LENGTH="minLength";
   const VALID_MAX_LENGTH="maxLength";
   const VALID_DATE = "date";
   const VALID_PATTERN = "pattern";
   const VALID_ALPHANUMERIC = "alphaNumeric";
   const VALID_ALPHA = "alpha";
   const VALID_EMAIL = "email";
   const VALID_FILE_REQUIRED="requiredfile";
   const VALID_FILE_SIZE="filesize";
   const VALID_FILE_TYPE="filetype";
   const VALID_MULTIPLE="required_multiple";


}