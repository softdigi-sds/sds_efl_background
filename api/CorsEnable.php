<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */


// Function to set cache control headers
function setCacheControlHeaders()
{
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
}



function checkDuplicateHeaders()
{
    // Get all headers
    $headers = getallheaders();

    // Check for duplicate headers
    $headerCounts = array_count_values(array_keys($headers));
    foreach ($headerCounts as $header => $count) {
        if ($count > 1) {
            // Block request if duplicate headers are found
            header('HTTP/1.1 400 Bad Request');
            echo 'Duplicate headers detected: ' . $header;
            exit;
        }
    }
}

function checkMobile()
{
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
    $mobile_agent = 0;
    $GLOBALS["SDS_MOBILE"] = 0;
    if (strpos($user_agent, "sds_hims_mobile_app") !== false && $origin == '') {
        $mobile_agent = 1;
        $GLOBALS["SDS_MOBILE"] = 1;
    }
    return $mobile_agent;
}

// validate the referrer 
function validateReferer($allowed_referrers)
{
    // Get the referrer of the request
    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

    // Check if the referrer is in the allowed list
    $is_allowed_referrer = false;
    foreach ($allowed_referrers as $allowed_referrer) {
        if (strpos($referrer, $allowed_referrer) === 0) {
            $is_allowed_referrer = true;
            break;
        }
    }

    if (!$is_allowed_referrer) {
        header('HTTP/1.1 403 Forbidden');
        echo 'Invalid referrer';
        exit;
    }
}

function normalizeAndValidateHeaders()
{
    $headers = getallheaders();

    // Normalize header keys to lowercase
    $normalizedHeaders = [];
    foreach ($headers as $key => $value) {
        $normalizedHeaders[strtolower($key)] = $value;
    }

    // Check for both Transfer-Encoding and Content-Length headers
    if (isset($normalizedHeaders['transfer-encoding']) && isset($normalizedHeaders['content-length'])) {
        header('HTTP/1.1 400 Bad Request');
        echo 'Invalid request: Conflicting headers (Transfer-Encoding and Content-Length)';
        exit;
    }

    // Check for obfuscated or unexpected Transfer-Encoding headers
    if (isset($normalizedHeaders['transfer-encoding'])) {
        $teHeader = strtolower($normalizedHeaders['transfer-encoding']);
        if ($teHeader !== 'chunked' && $teHeader !== 'identity') {
            header('HTTP/1.1 400 Bad Request');
            echo 'Invalid Transfer-Encoding header';
            exit;
        }
    }
}




function strict_orgin_check()
{
    $live_allowed_origins = [
        'http://efl.softdigisolutions.com/',
    ];  
    $allowed_origins = $live_allowed_origins;
    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
    if (in_array($origin, $allowed_origins) || checkMobile()) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Credentials: true');
    } else {
        header('HTTP/1.1 403 Forbidden');
        echo 'CORS policy does not allow access from this origin.';
        exit;
    }
    if (!checkMobile()) {
        // restrict the referrrs also
        validateReferer($allowed_origins);
    }
}



function cors()
{
    //echo "oringin =" .  $_SERVER['HTTP_ORIGIN'] . "<br/>";
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS,DELETE");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

    //echo "You have CORS!";
}



$api_mode = isset($_ENV["API_MODE"]) ? trim($_ENV["API_MODE"]) : "";


if ($api_mode == "production") {
    //addLogHeaders();
    checkMobile();
    // to prevent duplicate headers in the request
    checkDuplicateHeaders();
    //
    normalizeAndValidateHeaders();
    // set the cache control
    setCacheControlHeaders();
    // strict origin check
    strict_orgin_check();
} else {
    cors();
}
