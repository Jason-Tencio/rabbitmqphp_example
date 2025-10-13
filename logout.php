<?php
require_once "mq_client.php";
session_start();
if(!empty($_SESSION['user_id']) && !empty($_SESSION['session_key'])){
  mq_rpc(['type'=>'logout','user_id'=>(int)$_SESSION['user_id'],'session_key'=>$_SESSION['session_key']]);
}
session_destroy();
header("Location: login.php"); exit;
