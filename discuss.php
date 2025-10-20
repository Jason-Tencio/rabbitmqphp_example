<?php
require_once "mq_client.php";
require_once "session_check.php"; //The User here HAS to be logged in

$uid = (int)($_SESSION['user_id'] ?? 0);
$action = $_POST['action'] ?? $_GET['action'] ?? 'list';

if ($action === 'create' && $_SERVER['REQUEST_METHOD']==='POST') {
  $title = trim($_POST['title'] ?? '');
  $tt    = $_POST['topic_type'] ?? 'general';
  $ref   = trim($_POST['topic_ref'] ?? '');
  $res = mq_rpc(['type'=>'disc.create','user_id'=>$uid,'title'=>$title,'topic_type'=>$tt,'topic_ref'=>$ref]);
  if (($res['status'] ?? '') === 'ok') {
    header("Location: discussion.php?action=view&id=".$res['id']); exit;
  }
  $action='list';
}

if ($action === 'post' && $_SERVER['REQUEST_METHOD']==='POST') {
  $id = (int)($_POST['id'] ?? 0);
  $content = trim($_POST['content'] ?? '');
  mq_rpc(['type'=>'disc.post','id'=>$id,'user_id'=>$uid,'content'=>$content]);
  header("Location: discussion.php?action=view&id=".$id); exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Discussion Board</title>
  <link rel="stylesheet" href="style.css">
</head>
<body style="background:lavender;color:white;-webkit-text-stroke:1px black;">
<a href="home.php"><button>Home</button></a>
<?php
if ($action === 'list') {
  $topic = $_GET['topic'] ?? 'all';
  $res = mq_rpc(['type'=>'disc.list','topic'=>$topic]);
  echo "<h2>Discussions</h2>";
  echo '<form method="get" action="discussion.php">
          <select name="topic">
            <option value="all">all</option>
            <option value="artist">artist</option>
            <option value="song">song</option>
            <option value="concert">concert</option>
            <option value="general">general</option>
          </select>
          <button type="submit">Filter</button>
        </form>';
  echo '<ul>';
  foreach (($res['discussions'] ?? []) as $d) {
    $t = htmlspecialchars($d['title']);
    $tt= htmlspecialchars($d['topic_type']);
    $ref= htmlspecialchars($d['topic_ref'] ?? '');
    echo '<li><a href="discussion.php?action=view&id='.$d['id'].'">'.$t.
         '</a> <small>['.$tt.($ref?": ".$ref:"").']</small></li>';
  }
  echo '</ul>';
  echo '<h3>Start a new discussion</h3>
        <form method="post">
          <input type="hidden" name="action" value="create">
          <input name="title" placeholder="Title" required><br>
          <select name="topic_type">
            <option value="general">general</option>
            <option value="artist">artist</option>
            <option value="song">song</option>
            <option value="concert">concert</option>
          </select><br>
          <input name="topic_ref" placeholder="Artist/Song/Concert name (optional)"><br>
          <button type="submit">Create</button>
        </form>';
}
elseif ($action === 'view') {
  $id = (int)($_GET['id'] ?? 0);
  $res = mq_rpc(['type'=>'disc.get','id'=>$id]);
  $d = $res['discussion'] ?? null;
  if (!$d) { echo "<p>Not found</p><p><a href='discussion.php'>Back</a></p>"; exit; }
  $t = htmlspecialchars($d['title']);
  $tt= htmlspecialchars($d['topic_type']);
  $ref= htmlspecialchars($d['topic_ref'] ?? '');
  echo '<p><a href="discussion.php"><button>Back</button></a></p>';
  echo "<h2>$t</h2><p><small>$tt".($ref?": ".$ref:"")."</small></p>";
  echo '<div>';
  foreach (($res['messages'] ?? []) as $m) {
    $c  = nl2br(htmlspecialchars($m['content']));
    $ts = htmlspecialchars($m['created_at']);
    echo "<div style='border:1px solid #000;padding:8px;margin:6px 0'>
            <div>$c</div>
            <div><small>$ts</small></div>
          </div>";
  }
  echo '</div>';
  echo '<h3>Reply</h3>
        <form method="post">
          <input type="hidden" name="action" value="post">
          <input type="hidden" name="id" value="'.$id.'">
          <textarea name="content" rows="3" cols="60" required></textarea><br>
          <button type="submit">Post</button>
        </form>';
}
?>
</body>
</html>
