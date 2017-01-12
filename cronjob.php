<?php
require 'inc/php_inc.php';
// TODO: Token laden muss umgestellt werden auf MySQL

function Temp_TokenToMysql($database) {
  $handle = opendir('token');
  while (false !== ($entry = readdir($handle))) {
    if ($entry!=str_replace(".access","",$entry)) {
      $channel=str_replace(".access","",$entry);
      if ($channel!="bot.json") {
        $accessToken=load_accesstoken($channel);
        $refreshToken=load_refreshtoken($channel);
        if (gettype($accessToken)=="string") {
          echo '- '.$channel.' -<br>';
          
          $accessToken=json_decode($accessToken, true);
          $accessToken['refresh_token']=$refreshToken;
          if (session_to_database($database, $accessToken)) {
            unlink('token/'.$entry);
          }
        }
      }
    }
  }
}
Temp_TokenToMysql($database);

die();
$accessToken = load_accesstoken($KANALID);

// Google Verbindung
$client = new Google_Client();
$client->setClientId($OAUTH2_CLIENT_ID);
$client->setClientSecret($OAUTH2_CLIENT_SECRET);
$client->setDeveloperKey($DEV_KEY);
$client->setScopes('https: //www.googleapis.com/auth/youtube');
$client->setAccessToken($accessToken);

if ($client->isAccessTokenExpired()) {
  $client->refreshToken(load_refreshtoken($KANALID));
  $_SESSION["token"]=$client->getAccessToken();
  $token=json_encode($_SESSION["token"]);
  save_accesstoken($KANALID, $token);
}

$youtube = new Google_Service_YouTube($client);

// SQL Load Token
$_tmp_tabellename="bot_token";
$check_table=$database->show_tables();
if(!in_array($_tmp_tabellename, $check_table)) {
  $felder=null;
  $felder["id"]="TEXT";
  $felder["token"]="TEXT";
  $felder["last_used"]="TEXT";
  $felder["cooldown"]="TEXT";
  $database->create_table($_tmp_tabellename, $felder, "");
  unset($felder);
}
$tmp_tokens=$database->sql_select($_tmp_tabellename,"*","",true);
foreach ($tmp_tokens as $tmp_key => $tmp_value)  {
  foreach($tmp_value as $t2key => $t2value) {
    $token[$tmp_value["id"]][$t2key] = $t2value;
  }
  if ( $token[$tmp_value["id"]]["cooldown"] == 0) {
    $token[$tmp_value["id"]]["cooldown"] = 300;
  }
}
function init_token($name) {
  $_tmp_token["id"]=$name;
  $_tmp_token["token"]="null";
  $_tmp_token["last_used"]=0;
  $_tmp_token["cooldown"]=300;
  return $_tmp_token;
}

//include("cronjob/load_channels.php");

include("cronjob/channels_contentDetails.php");
include("cronjob/channels_statistics.php");

include("cronjob/subscriptions_subscriberSnippet.php");

include("cronjob/playlistItems_snippet_uploaded.php");
include("cronjob/videos_statistics.php");
include("cronjob/videos_status.php");

include("cronjob/channels_livestreamchat.php");
include("cronjob/livestream_chat.php");


//include("cronjob/videos_contentDetails.php");
//include("cronjob/videos_liveStreamingDetails.php");
?>
