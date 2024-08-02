<?php
require_once (__DIR__ . '/../../conf.inc.php');
session_id($_GET["PHPSESSID"]);
session_start();
$data = $_SESSION["mod_audio"][$_GET["ID"]];
header('Content-Type: text/javascript');
?>
get_resource = function(authstring) {
jQuery.ajax({
url:"<?php echo $data["ajax_url"] ?>&callback=get_resource&"+authstring,
success:function(data){
jQuery("#edusharing_rendering_content").html(data);
}
});
}
get_resource("<?php echo $data["authString"] ?>");

