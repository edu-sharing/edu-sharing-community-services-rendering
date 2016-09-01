<?php

require_once("misc.php");
require_once("ims-blti/blti_util.php");

    $lmsdata = array(
      "resource_link_id" => "120988f929-274612",
      "resource_link_title" => "Weekly Blog",
      "resource_link_description" => "A weekly blog.",
      "user_id" => "textPossible",
      "roles" => "Instructor",  // or Learner
      "lis_person_name_full" => 'admin1',
      "lis_person_contact_email_primary" => "a1@edu-sahring.edu",
      "lis_person_sourcedid" => "school.edu:user",
      "context_id" => "456434513",
      "context_title" => "Design of Personal Environments",
      "context_label" => "SI182",//=thread
      "tool_consumer_instance_guid" => "lmsng.school.edu",
      "tool_consumer_instance_description" => "University of School (LMSng)",
      "custom_gotocategory" => "4",
      );

  foreach ($lmsdata as $k => $val ) {
      if ( $_POST[$k] && strlen($_POST[$k]) > 0 ) {
          $lmsdata[$k] = $_POST[$k];
      }
  }

  $cur_url = curPageURL();
  $key = $_REQUEST["key"];
  if ( ! $key ) $key = "mc";
  $secret = $_REQUEST["secret"];
  if ( ! $secret ) $secret = "secret";
  $endpoint = $_REQUEST["endpoint"];


  if ( ! $endpoint ) $endpoint = 'http://141.54.178.191/vanilla-lti/index.php?p=/entry/signin';
  $urlformat = $_REQUEST["format"];
  $urlformat = ( $urlformat != 'XML' );
  $tool_consumer_instance_guid = $lmsdata['tool_consumer_instance_guid'];
  $tool_consumer_instance_description = $lmsdata['tool_consumer_instance_description'];

  $xmldesc = str_replace("\\\"","\"",$_REQUEST["xmldesc"]);
  if ( ! $xmldesc ) $xmldesc = $default_desc;

  if ( ! $lmspw ) unset($tool_consumer_instance_guid);

  if ( $urlformat ) {
    $parms = $lmsdata;
  } else {
    $cx = launchInfo($xmldesc);
    $endpoint = $cx["launch_url"];
    if ( ! $endpoint ) {
      echo("<p>Error, did not find a launch_url or secure_launch_url in the XML descriptor</p>\n");
      exit();
    }
#    $cx['custom']=array('gotocategory' =>'1');
    $custom = $cx["custom"];
#    custom_gotocategory=1
    $parms = array_merge($custom, $lmsdata);
  }

  // Cleanup parms before we sign
  foreach( $parms as $k => $val ) {
    if (strlen(trim($parms[$k]) ) < 1 ) {
       unset($parms[$k]);
    }
  }

  // Add oauth_callback to be compliant with the 1.0A spec
  $parms["oauth_callback"] = "about:blank";

  $parms = signParameters($parms, $endpoint, "POST", $key, $secret, "Press to Launch", $tool_consumer_instance_guid, $tool_consumer_instance_description);

  $content = postLaunchHTML($parms, $endpoint, false, false);//"width=\"100%\" height=\"900\" scrolling=\"auto\" frameborder=\"1\" transparency");
print($content);
?>

