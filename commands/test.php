<?php

// Getting SUB count & last name
require 'inc/php_inc.php';

$accessToken = load_accesstoken($KANALID);

$database=new db("sqlite","");
$database->connect_db("data/".$KANALID.".sqlite3");

// Google Verbindung
$client = new Google_Client();
$client->setClientId($OAUTH2_CLIENT_ID);
$client->setClientSecret($OAUTH2_CLIENT_SECRET);
$client->setDeveloperKey($DEV_KEY);
$client->setScopes('https: //www.googleapis.com/auth/youtube');

$client->setAccessToken($accessToken);

$youtube = new Google_Service_YouTube($client);


//Load from Data TABLE: channels_liveStreamChat
$_tmp_tabellename="channels_liveStreamChat";
$db_stats = $database->sql_select($_tmp_tabellename, "*", "id='".$KANALID."'", true);

$BroadcastId=$db_stats[0]["broadcastId"];
$ChatId=$db_stats[0]["chatId"];

if ($BroadcastId!="null") {
  $broadcastsResponse = $youtube->liveChatMessages->listLiveChatMessages('snippet,authorDetails',array("liveChatId"=>$ChatId,"maxResults"=>2000));
  debug_log($broadcastsResponse);
} else {
  echo "Kein Livestream gefunden!";
}
?>