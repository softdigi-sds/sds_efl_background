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
class SmartCurl
{
    private $baseUrl;
    private $headers;

    public function __construct($url_type = "")
    {
        $this->baseUrl = "http://smartpdf.softdigisolutions.com/api";
        //http://localhost:9191/api";
        $this->headers = [];
    }

    public function setBearerToken($token)
    {
        $this->headers[] = 'Authorization: Bearer ' . $token;
    }

    public function setAuthorization($token)
    {
        $this->headers[] = 'Authorization:' . $token;
    }


    public function get($endpoint)
    {
        return $this->request('GET', $endpoint);
    }

    public function post($endpoint, $data)
    {
        return $this->request('POST', $endpoint, $data);
    }

    public function put($endpoint, $data)
    {
        return $this->request('PUT', $endpoint, $data);
    }

    public function delete($endpoint)
    {
        return $this->request('DELETE', $endpoint);
    }

    private function request($method, $endpoint, $data = null)
    {
        $ch = curl_init();
        //var_dump($this->headers);
        //var_dump($data);
        // $url = $this->baseUrl . $endpoint;
        //echo "url = ". $url;
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($method === 'POST' || $method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($this->headers, array('Content-Type: application/json')));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response = curl_exec($ch);
        var_dump($response);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //echo " http code " , $httpCode;
        if ($response === false) {
            throw new \Exception(curl_error($ch), curl_errno($ch));
            \CustomErrorHandler::triggerInternalError("Server Not Available");
        }

        curl_close($ch);

        if ($httpCode >= 400) {
            $decoded_data = json_decode($response);
            if ($decoded_data === null && json_last_error() !== JSON_ERROR_NONE) {
                \CustomErrorHandler::triggerInvalid($response);
            } else {
                $msg = isset($decoded_data->message) ? $decoded_data->message : "";
                \CustomErrorHandler::triggerInternalError($msg);
                // echo "The JSON string is valid.";
            }
            // throw new \Exception("HTTP Error: $httpCode", $httpCode);
        }

        return $response;
    }
}
