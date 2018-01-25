<?php
$cronjob_id=basename(__FILE__, '.php');
$do_job=check_settings($database, $cronjob_id);

if ($do_job==false) {
  return;
  die();
} else {
  $token[$cronjob_id]=load_cronjobtoken($database, $cronjob_id, $_SESSION['user']['email']);
}
$_tmp_tabellename=strtolower($cronjob_id);


if (!isset($token[$_tmp_tabellename])) {
  $token[$_tmp_tabellename] = init_token($_tmp_tabellename);
}
$tt=$token[strtolower($_tmp_tabellename)];

if ($tt["last_used"]+$tt["cooldown"]<time()) {
  
  $oldest_date=$SYTHS->get_timestamp('tag',true, -32);
  $database->sql_delete("bot_chatstats", "`date`<".$oldest_date."");
  $database->sql_delete("bot_chathosts", "`last_seen`<".$oldest_date."000");
  $database->sql_delete("bot_chatuser", "`last_seen`<".$oldest_date."000");
  
  $oldest_date=$SYTHS->get_timestamp('tag',true, -7);
  $database->sql_delete("youtube_videos", "`simple_lastupdate`<".$oldest_date."");
  
  
  $tt["cooldown"]=1*60*60;
}
// Save Token
echo date("d.m.Y - H:i:s")." - ".$_tmp_tabellename." updated!<br>";
$tt["last_used"]=time();
$tt["user"]=$_SESSION['user']['email'];
if($tt["token"]==""){$tt["token"]="null";}
if ($_SESSION['user']['email']!="") {
  $database->sql_insert_update("bot_token",$tt);
}
$token[strtolower($cronjob_id)]=$tt;
unset($tt);
?>
