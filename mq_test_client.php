<?php
require_once("/opt/it490/path.inc");
require_once("/opt/it490/get_host_info.inc");
require_once("/opt/it490/rabbitMQLib.inc");

$client = new rabbitMQClient("/opt/it490/testRabbitMQ.ini","testServer");

// Test payload
$request = [
    "type" => "test",
    "msg"  => "Hello from webvm!"
];

$response = $client->send_request($request);
error_log('CLIENT OUT: '.json_encode($req));

$res = $client->send_request($req);
echo "Got response:\n";
var_dump($response);
?>
