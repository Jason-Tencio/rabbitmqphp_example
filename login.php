<?php
require_once "mq_client.php";
session_start();
$error='';
if($_SERVER["REQUEST_METHOD"]==="POST"){
  $u=$_POST['username']; $p=$_POST['password'];
  $res=mq_rpc(['type'=>'login','username'=>$u,'password'=>$p]);
  if(($res['status']??'')==='ok'){
    $_SESSION['user_id']=$res['user_id'];
    $_SESSION['username']=$u;
    $_SESSION['session_key']=$res['session_key'];
    header("Location: home.php"); exit;
  } elseif(($res['status']??'')==='fail'){ $error="Invalid login."; }
  else { $error="Login temporarily unavailable."; }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h2>Login</h2>
    <?php if (!empty($error)) echo "<p>$error</p>"; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
