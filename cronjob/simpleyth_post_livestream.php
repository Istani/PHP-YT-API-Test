<?php
$cronjob_id=basename(__FILE__, '.php');
$do_job=check_settings($database, $cronjob_id);

if ($do_job==false) {
  return;
} else {
  $token[$cronjob_id]=load_cronjobtoken($database, $cronjob_id, $_SESSION['user']['email']);
}
$tt=$token[$cronjob_id];
// Der hat keine Eigene Tablle

if (isset($video_list)) {
  unset($video_list);
}
$post_time=time();
// YT
{
  $videos_yt=$database->sql_select("youtube_livestream", "*", "youtube_snippet_channelid='".$_SESSION['user']['youtube_user']."' AND (youtube_snippet_actualendtime IS NULL OR youtube_snippet_actualendtime='') ORDER BY youtube_snippet_actualstarttime DESC LIMIT 1",false);
  if (count($videos_yt)==0) {
    $tt['token']="";
  } else {
    if ($videos_yt[0]["simple_lastupdate"]<time()-30*60) {
      $videos_yt[0]['youtube_snippet_actualendtime']="ERROR";
      $database->sql_insert_update("youtube_livestream", $videos_yt[0]);
      $tt['token']="ERROR";
    }
    if ($tt['token']=='null') {
      // Do Magic 2
      $tt['token']=$videos_yt[0]['youtube_id'];
      $my_rechte=$SYTHS->may_post_videos_on($_SESSION['user']['email']);
      foreach ($my_rechte as $t_service => $the_hosts) {
        foreach ($the_hosts as $t_host => $t_channel) {
          if ($t_channel!="0") {
            // Posten versuchen
            $t_user="-1";
            if ($t_service=="Discord") {
              $t_user=$_SESSION['user']['discord_user'];
            }
            if ($t_service=="YouTube") {
              $t_user=$_SESSION['user']['youtube_user'];
            }
            
            
            // Poste
            if ($t_user!="") {
              $add_post['service']=$t_service;
              $add_post['host']=$t_host;
              $add_post['room']=$t_channel;
              $add_post['id']=$post_time++;
              $add_post['time']=time();
              $add_post['user']=$t_user;
              $add_post['message']="!yt livestream";
              $add_post['process']=0;
              $database->sql_insert_update("bot_chatlog", $add_post);
              debug_log($add_post);
            }
          }
          
          $ad_params=explode("|", $videos_yt[0]['youtube_snippet_title']);
          // NOTE: Clean Old ADs
          $old_ads=$database->sql_select("user_ads","*", "type LIKE 'AD_Livestream' AND premcount>0 AND owner LIKE '".$_SESSION['user']['email']."'");
          for ($count_ads=0;$count_ads<count($old_ads);$count_ads++) {
            $old_ads[$count_ads]['premcount']=0;
            $database->sql_insert_update("user_ads", $old_ads[$count_ads]);
          }
          Generate_Amazon_Ad($amazon, $database, $ad_params[0], $_SESSION['user']['email'], false, true);
          /* RPG Start */
          $game_data=$database->sql_select("bot_chathosts", "*", "owner='".$_SESSION['user']['youtube_user']."' or owner='".$_SESSION['user']['discord_user']."'", false);
          for ($count_game_data=0;$count_game_data<count($game_data);$count_game_data++) {
            $this_channel=$game_data[$count_game_data];
            if ($t_service=="YouTube") {
              $add_post['room']=$videos_yt[0]['youtube_snippet_livechatid'];
            }
            if ($t_service=="Discord") {
              $add_post['room']=$this_channel['channel_rpgmain'];
            }
            
            $add_post['service']=$t_service;
            $add_post['host']=$t_host;
            $add_post['id']=$post_time++;
            $add_post['time']=time()+1;
            $add_post['user']=$t_user;
            $add_post['message']="!rpg start";
            $add_post['process']=0;
            $database->sql_insert_update("bot_chatlog", $add_post);
            debug_log($add_post);
          }
          
          unset($add_post);
        }
      }
    }
    
  }
}
// Twitch
{
  if (isset($videos)) {unset($videos);}
  $videos=$database->sql_select("twitch_livestream", "*", "twitch_user_id='".$_SESSION['user']['twitch_user']."'",false);
  if (count($videos)==0) {
    $tt['token']="";
  } else {
    if ($tt['token']=='null') {
      $tt['token']=$videos[0]['twitch_id'];
      // Do Magic 2
      $my_rechte=$SYTHS->may_post_videos_on($_SESSION['user']['email']);
      foreach ($my_rechte as $t_service => $the_hosts) {
        foreach ($the_hosts as $t_host => $t_channel) {
          if ($t_channel!="0") {
            // Posten versuchen
            $t_user="-1";
            if ($t_service=="Discord") {
              $t_user=$_SESSION['user']['discord_user'];
            }
            if ($t_service=="YouTube") {
              $t_user=$_SESSION['user']['youtube_user'];
            }
            
            
            // Poste
            if ($t_user!="") {
              $add_post['service']=$t_service;
              $add_post['host']=$t_host;
              $add_post['room']=$t_channel;
              $add_post['id']=$post_time++;
              $add_post['time']=time();
              $add_post['user']=$t_user;
              $add_post['message']="!twitch livestream";
              $add_post['process']=0;
              $database->sql_insert_update("bot_chatlog", $add_post);
              debug_log($add_post);
            }
          }
        }
      }
      /* Ads */
      /* RPG Start */
      $game_data=$database->sql_select("bot_chathosts", "*", "owner='".$_SESSION['user']['twitch_user']."' or owner='".$_SESSION['user']['youtube_user']."' or owner='".$_SESSION['user']['discord_user']."'", false);
      for ($count_game_data=0;$count_game_data<count($game_data);$count_game_data++) {
        $this_channel=$game_data[$count_game_data];
        if ($t_service=="YouTube") {
          $add_post['room']=$videos[0]['youtube_snippet_livechatid'];
        }
        if ($t_service=="Discord") {
          $add_post['room']=$this_channel['channel_rpgmain'];
        }
        
        $add_post['service']=$t_service;
        $add_post['host']=$t_host;
        $add_post['id']=$post_time++;
        $add_post['time']=time()+1;
        $add_post['user']=$t_user;
        $add_post['message']="!rpg start";
        $add_post['process']=0;
        $database->sql_insert_update("bot_chatlog", $add_post);
        debug_log($add_post);
      }
      unset($add_post);
    }
  }
}
// NOTE: Ggf weitere Dienste

// Save Token
echo date("d.m.Y - H:i:s")." - ".$_SESSION['user']['email'].': '.$cronjob_id." updated!<br>";
//$tt["cooldown"]=1*60*60; // Test
$tt["cooldown"]=60; // Test
$tt["last_used"]=time();
$tt["user"]=$_SESSION['user']['email'];
if($tt["token"]==""){$tt["token"]="null";}
$database->sql_insert_update("bot_token",$tt);
unset($tt);
//die();
?>
