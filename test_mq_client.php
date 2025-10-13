<?php
require_once __DIR__ . "/mq_client.php";

echo "Sending test register request...\n";
$res = mq_rpc(['type'=>'register','username'=>'__testuser'.rand(1000,9999),'email'=>'test@example.com','password'=>'abc123']);
var_dump($res);

echo "\nSending test login request (expected to fail unless that user exists)\n";
$res2 = mq_rpc(['type'=>'login','username'=>'__testuser','password'=>'abc123']);
var_dump($res2);
