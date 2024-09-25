<?php
//require_once('path/to/JWT.php');
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$jwt = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;

//echo "jwt = " . $jwt;
$secret_key = isset($_ENV["JWT"]) ? $_ENV["JWT"] : "";// 'your_secret_key';
if (isset($jwt) && strlen($jwt) > 10) {
    list($bearer, $token) = explode(' ', $jwt);
   // echo "jwt = " . $token;
    try {
        $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
        $GLOBALS["USER"] = $decoded;
       // var_dump($GLOBALS["USER"]);        
    } catch (\Exception $e) {
       var_dump($e);
    }
} 