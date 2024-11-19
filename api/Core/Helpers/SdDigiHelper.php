<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core\Helpers;
// others
use \Firebase\JWT\JWT;
use stdClass;

/**
 * Description of Validator
 *
 * @author kms
 */
class SdDigiHelper
{

    static public function getDigiObjectSingleSign($content, $user_name, $user_id, $sig_name,$url)
    {
        $_obj = new stdClass();
        $_obj->redirectUrl = $url;
        $_obj->type = "SINGLE_SIGN";
        $single_file = self::getDscDataObj("", [[$sig_name, []]]);
        $_obj->dscData = [$single_file];
        $data = new \stdClass();
        $data->content = $content;
        $data->task_user_id = $user_id;
        $data->task_user_name =  $user_name;
        $data->task_fields = json_encode($_obj);
        return $data;
    }

    static public function getDscDataObj($fileTemplate, $signArr = [])
    {
        $_obj = new \stdClass();
        $_obj->FileTemplate = $fileTemplate;
        $_obj->signObj = [];
        foreach ($signArr as $_arr) {
            $_obj->signObj[] = self::getSignObj($_arr[0], $_arr[1]);
        }
        return $_obj;
    }

    static public function getSignObj($sign_name, $fields = [])
    {
        $sign_obj = new \stdClass();
        $sign_obj->sigName = $sign_name;
        if (count($fields) > 0) {
            $sign_obj->fields = $fields;
        }
        return $sign_obj;
    }
}
