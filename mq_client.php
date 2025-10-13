<?php
require_once("/opt/it490/path.inc");
require_once("/opt/it490/get_host_info.inc");
require_once("/opt/it490/rabbitMQLib.inc");

function mq_rpc(array $req): array {
  $client = new rabbitMQClient("/opt/it490/testRabbitMQ.ini", "testServer");
  $resp = $client->send_request($req);
  return is_array($resp)? $resp : ['status'=>'error','message'=>'Bad MQ response'];
}

