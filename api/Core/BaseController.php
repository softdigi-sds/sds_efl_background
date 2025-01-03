<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Core;

use \Core\Helpers\SmartModel;
use \Core\Helpers\SmartFileHelper;
use \Core\Helpers\SmartLogger as Logger;

/**
 * Description of CustomEroorHandler
 *
 * @author kms
 */
class BaseController
{
    /**
     * 
     */
    public $params = [];
    /**
     * 
     */
    public $post = [];
    /**
     * 
     */
    public SmartModel $db;
    /**
     * 
     */


    /**
     * 
     */
    function __construct($params)
    {
        // set the paramters 
        $this->setParams($params);
        // set the db
        $this->db = new SmartModel();
        // load site settings if required

    }



    /**
     * 
     */
    public function setParams($params)
    {
        // get parameters
        $this->params = $params;
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST)) {
            $this->post =  $_POST = json_decode(file_get_contents('php://input'), true);
            if (!is_array($this->post)) {
                $this->post = [];
            }
        } else {
            $this->post =  $_POST;
        }
    }


    public function cleanBuffer()
    {
        if (ob_get_level() > 0) {
            ob_clean();
        }
    }

    /**
     * 
     */
    public function response($data)
    {
        $this->cleanBuffer();
        http_response_code(200);
        echo json_encode($data);
        exit();
    }
    /**
     * 
     */
    public function responseMsg($msg)
    {
        $this->cleanBuffer();
        $db = new \stdClass();
        $db->msg = $msg;
        http_response_code(200);
        echo json_encode($db);
        exit();
    }

    public function responsePdf($file_path)
    {
        $full_path = SmartFileHelper::getDataPath() . $file_path;
        // echo $full_path;
        //exit();
        if (file_exists($full_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($full_path));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($full_path));
            readfile($full_path);
            exit;
        } else {
            //  \CustomErrorHandler::triggerInternalError("Invalid Pdf Path");
        }
    }

    public function responsePdfNew($file_path)
    {
        $full_path = SmartFileHelper::getDataPath() . $file_path;
        // echo $full_path;
        //exit();
        if (file_exists($full_path)) {
            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename=' . basename($full_path) . '');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($full_path));
            @readfile($full_path);
            /*
            header('Content-Description: File Transfer');
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');
            header('Content-Disposition: inline; filename=' . basename($full_path));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($full_path));
            readfile($full_path);
            */
            exit;
        } else {
            //  \CustomErrorHandler::triggerInternalError("Invalid Pdf Path");
        }
    }

    public function responseImage($file_path)
    {
        $full_path = SmartFileHelper::getDataPath() . $file_path;
        // echo $full_path;
        //exit();
        if (file_exists($full_path)) {
            $imageMimeType = mime_content_type($full_path);
            // Set the appropriate Content-Type header
            header('Content-Type: ' . $imageMimeType);
            readfile($full_path);
            exit;
        } else {
            //  \CustomErrorHandler::triggerInternalError("Invalid Pdf Path");
        }
    }

    public function responseImageDb($img_content)
    {
        // Set the appropriate Content-Type header
        header('Content-Type: image/png'); // Change 'image/png' based on your actual image type

        // Output the image content
        echo $img_content;
        //  $full_path =SmartFileHelper::getDataPath() . $file_path;
        // echo $full_path;
        //exit();
        //if(file_exists($full_path)){
        //  $imageMimeType = mime_content_type($full_path);
        // Set the appropriate Content-Type header
        // header('Content-Type: ' . $imageMimeType);            
        // readfile( $full_path);
        exit;
        //}else{
        //  \CustomErrorHandler::triggerInternalError("Invalid Pdf Path");
        // }
    }

    public function responseBase64($content)
    {
        $this->cleanBuffer();
        $db = new \stdClass();
        $db->content = $content;
        http_response_code(200);
        echo json_encode($db);
        exit();
    }

    public function responseFileBase64($file_path, $file_name = "")
    {
        if (file_exists($file_path)) {
            $content = file_get_contents($file_path);
            $base_64 = base64_encode($content);
            $db = new \stdClass();
            $db->content = $base_64;
            if (strlen($file_name) > 3) {
                $db->file_name = $file_name;
            }
            $this->response($db);
        } else {
            \CustomErrorHandler::triggerInvalid("Invalid File Path");
        }
    }

    public function addLog($action, $msg = "", $user = "")
    {
        Logger::info($msg, $action, $user);
    }
}
