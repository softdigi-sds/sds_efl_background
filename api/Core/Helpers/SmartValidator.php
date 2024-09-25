<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Helpers;

/**
 * Description of Validator
 *
 * @author kms
 */
class SmartValidator
{
    // errors
    private $_errors;
    // post
    private $_post;
    // valid field
    private $_value;
    // msg
    private $_msg;
    //
    private $_error;
    //
    private $_rule;
    //
    private $_file_value;

    function __construct()
    {
    }

    public function valid_field($rule, $index, array $post)
    {
        $this->_rule = $rule;
        $this->_post = $post;
        // 
        $type = isset($rule["type"]) ? $rule["type"] : "";
        $this->_value = isset($this->_post[$index]) ?  $this->_post[$index] : null;
        $this->_file_value = isset($_FILES[$index]) ? $_FILES[$index] : null;
        $this->_msg = isset($rule["msg"]) ? $rule["msg"] : "Field Required";       
        switch ($type) {
            case SmartConst::VALID_REQUIRED:
                $this->required_check();
                break;
            case SmartConst::VALID_MIN_LENGTH:
                $this->minLength_check();
                break;
            case SmartConst::VALID_MAX_LENGTH:
                $this->maxLength_check();
                break;
            case SmartConst::VALID_DATE:
                $this->date_check();
                break;
            case SmartConst::VALID_MIN:
                $this->min_check();
                break;
            case SmartConst::VALID_MAX:
                $this->max_check();
                break;
            case SmartConst::VALID_ALPHANUMERIC:
                $this->isAlphanumeric();
                break;
            case SmartConst::VALID_ALPHA:
                $this->isAlpha();
                break;
            case SmartConst::VALID_PATTERN:
                $this->isPattern();
                break;
            case SmartConst::VALID_EMAIL:
                $this->isEmail();
                break;
            case SmartConst::VALID_FILE_REQUIRED:
                $this->isRequiredFile();
                break;
            case SmartConst::VALID_FILE_SIZE:
                $this->file_size_check();
                break;
            case SmartConst::VALID_FILE_TYPE:
                $this->file_ext_check();
                break;
            case SmartConst::VALID_MULTIPLE:
                $this->required_multiple();
                break;
            default:
                break;
        }
        return [$this->_error, $this->_msg];
    }

    private function required_multiple(){       
        if(is_array($this->_value) && count($this->_value) < 1){
            $this->_error = true;
        }
    }

    /**
     *  required 
     */
    private function required_check()
    {
        if ($this->_value === NULL) {
            $this->_error = true;
        }
    }
    /**
     * 
     */
    private function min_check()
    {
        $min = isset($this->_rule["min"]) ? floatval($this->_rule["min"]) : 0;
        if ($this->_value !== NULL && floatval($this->_value) <= intval($min)) {
            $this->_error = true;
        }
    }
    /**
     * 
     */
    private function max_check()
    {
        $max = isset($this->_rule["max"]) ? floatval($this->_rule["max"]) : 0;
        if ($this->_value !== NULL && floatval($this->_value) >= intval($max)) {
            $this->_error = true;
        }
    }
    /**
     * 
     */
    private function minLength_check()
    {
        $max = isset($this->_rule["max"]) ? floatval($this->_rule["max"]) : 0;
        if ($this->_value !== NULL && is_string($this->_value) && strlen($this->_value) >= intval($max)) {
            $this->_error = true;
        }
    }
    /**
     * 
     */
    private function maxLength_check()
    {
        $max = isset($this->_rule["max"]) ? floatval($this->_rule["max"]) : 0;
        // echo "V = " . $this->_value . "  L " . strlen($this->_value);
        if ($this->_value !== NULL && is_string($this->_value) && strlen($this->_value) > intval($max)) {
            $this->_error = true;
        }
    }
    /**
     * 
     */
    private function date_check()
    {
        if ($this->_value === null) return true;
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->_value)) {
            $this->_error = true;
        }
    }
    /**
     * 
     */
    private function isAlphanumeric()
    {
        if ($this->_value !== NULL && !preg_match('/[^a-z0-9A-Z]/i', $this->_value)) {
            $this->_error = true;
        }
    }
    /**
     * 
     */
    private function isAlpha()
    {
        if (preg_match('/[^a-zA-Z]/i', $this->_value)) {
            $this->_error = true;
        }
    }

    private function isEmail()
    {
        if (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $this->_value)) {
            $this->_error = true;
        }
    }
    /**
     * 
     */
    private function isPattern()
    {
        $pattern = $this->_rule["pattern"];
        if ($this->_value === null) return true;
        if (!preg_match($pattern, $this->_value)) {
            $this->_error = true;
        }
    }
    /**
     * 
     */

    private function isRequiredFile()
    {
        if ($this->_file_value === NULL) {
            $this->_error = true;          
        }
    }

    private function file_size_check()
    {
        $size = isset($this->_rule["size"]) ? ($this->_rule["size"]) : [];
        $min = isset($size[0]) ? floatval($size[0]) : 0;
        $max = isset($size[1]) ? floatval($size[1]) : 255;
        if ($this->_file_value !== NULL) {
            $file_size = $this->_file_value["size"] / (1000 * 1000);          
            if ($file_size < $min || $file_size > $max) {
                $this->_error = true;
            }
        }
    }

    private function file_ext_check()
    {
        if ($this->_file_value !== NULL) {
            $file_name =  $this->_file_value["name"];
            $extension = SmartGeneral::getExt($file_name);
            $allowedExtensions = isset($this->_rule["ext"]) ? ($this->_rule["ext"]) : [];
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                $this->_error = true;
            }
        }
    }
}
