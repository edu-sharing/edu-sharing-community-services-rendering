<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
//todo check params
if(!isset($_POST['formSubmitted'])) {

    if(empty($_GET['user_id']) || empty($_GET['course_id']) || empty($_GET['resource_link_id']) || empty($_GET['token'])) {
	header('Location: https://esrender.logineo.de/esrender');
	exit();
}

$token = realpath(dirname(__FILE__)).'/token/'.$_GET['token'];

if(!file_exists($token)) {
    header('Location: https://esrender.logineo.de/esrender');
    exit();
} else {
    unlink($token);

}

	$fname = $user_id = $resource_link_description = $resource_link_id = $course_id = $resource_link_title = $roles = $authorID = $groupID = '';

	if(!empty($_GET['fname']))
		$fname = strip_tags($_GET['fname']);
	if(!empty($_GET['user_id']))
		$user_id = strip_tags($_GET['user_id']);
	if(!empty($_GET['resource_link_description']))
		$resource_link_description = strip_tags($_GET['resource_link_description']);
	if(!empty($_GET['resource_link_id']))
		$resource_link_id = strip_tags($_GET['resource_link_id']);
	if(!empty($_GET['course_id']))
		$course_id = strip_tags($_GET['course_id']);
	if(!empty($_GET['resource_link_title']))
		$resource_link_title = strip_tags($_GET['resource_link_title']);
	if(!empty($_GET['roles']))
		$roles = strip_tags($_GET['roles']);
	if(!empty($_GET['authorID']))
		$authorID = strip_tags($_GET['authorID']);
	if(!empty($_GET['groupID']))
		$groupID = strip_tags($_GET['groupID']);

?>
<form name="etherpadLti" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
	<input type="hidden" name="formSubmitted" value="true" />
	<input type="hidden" name="fname" value="<?php echo $fname ?>" />
	<input type="hidden" name="user_id" value="<?php echo $user_id ?>" />
	<input type="hidden" name="resource_link_description" value="<?php echo $resource_link_description ?>" />
	<input type="hidden" name="resource_link_id" value="<?php echo $resource_link_id ?>" />
	<input type="hidden" name="course_id" value="<?php echo $course_id ?>" />
	<input type="hidden" name="resource_link_title" value="<?php echo $resource_link_title ?>" />
	<input type="hidden" name="roles" value="<?php echo $roles ?>" />
	<input type="hidden" name="authorID" value="<?php echo $authorID ?>" />
	<input type="hidden" name="groupID" value="<?php echo $groupID ?>" />
	<input type="submit" value="Press to continue to external tool"/>
</form>
<script>
	document.etherpadLti.submit();
</script>
	
<?php 

 } else {

}

include('config.php');

$server = SERVER;
$protocol = PROTOCOL;
$path = PADPATH;
$apiKey = APIKEY;

$padUrl = $protocol . '://' . $server . '/' . $path;

$fname = $_POST['fname'];
$user_id = $_POST['user_id'];
$resource_link_description = $_POST['resource_link_description']; //'superPad';
$resource_link_id = $_POST['resource_link_id']; //'padName';
$course_id = $_POST['course_id']; //'22';
$resource_link_title = $_POST['resource_link_title']; //'superPad';
$roles = $_POST['roles']; //'';
$authorID = $_POST['authorID']; //''; 
$groupID = $_POST['groupID']; //'';   
	 
?>

<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Etherpad</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="./jquery-1.10.2.min.js"></script>
    <script src="./etherpad.js"></script>
    <script src="./jquery.cookie.js"></script>

    <link rel="shortcut icon" href="res/vcrp.gif" type="image/x-icon">
    <style type="text/css" id="internalStyle">
        /*<![CDATA[*/
        BODY {  margin: 0; padding: 0;}
        /*]]>*/
    </style>

   <script type="text/javascript">
   
     var server = "<?php echo $server?>";
     var padUrl = "<?php echo $padUrl?>";
     var apiKey = "<?php echo $apiKey?>";
        
     var fname =  "<?php echo $fname?>";
     var user_id = "<?php echo $user_id?>";
     var pad_desc = "<?php echo $resource_link_description?>";
     var pad_id = "<?php echo $resource_link_id?>";
     var course_id = "<?php echo $course_id?>";
     var pad_title = "<?php echo $resource_link_title?>";
     var roles = "<?php echo $roles?>";
     var authorID = "<?php echo $authorID?>"; 
     var groupID = "<?php echo $groupID?>";   

    function pad_user() {
        if(fname == '' ){
            //roles=Learner,Instructor,Administrator
            if(roles.indexOf('Instructor') != -1){
                //alert('Bei der Konfiguration der LTI Bausteins muss das Feld \"Name zum Anbieter senden\" ausgewählt werden!\n\n Andernfalls ist Etherpad nicht zugänglich!');
            }
            else{
                //alert('Es sind keine LTI Daten vorhanden.\nEvtl. ist das Modul nicht richtig konfiguriert.\n\nWenden Sie sich an den Betreuer des Kurses!');
            }
        }
        else{
            Url = padUrl+'/api/1/createAuthorIfNotExistsFor?apikey='+apiKey+'&name='+fname+'&authorMapper='+user_id+'&jsonp=?';
            $.ajax({
                    async: false,
                //type: 'GET',
                    url: Url,
                processData: true,
                //data:  Data,
                //contentType: 'application/x-www-form-urlencoded',
                dataType: 'json',
                statusCode: {
                    404: function() {
                        alert('Seite nicht gefunden');
                    },
                    405: function() {
                        alert('Request method not supported');
                    },
                    415: function() {
                        alert('Unsupportet Media Type');
                    },
                },
                    success: function (data) {
                    if(data.message.indexOf('ok') != -1){
                                authorID = data.data.authorID;
                            }
                    
                    },
                complete: function (data) {
                //alert(data.responseText);
                    if(authorID != ''){
                        //alert('200 OK');
                        pad_group();
                    }
                    else{
                        alert('Fehler im Etherpad. Server nicht erreichbar?');  

                    }
                }
            })
        }
    }
    
    function pad_group(){
        Url = padUrl+'/api/1/createGroupIfNotExistsFor?apikey='+apiKey+'&groupMapper='+course_id+'&jsonp=?';
        $.ajax({
                async: false,
                url: Url,
            processData: true,
            dataType: 'json',
            statusCode: {
                404: function() {
                    alert('Seite nicht gefunden');
                },
                405: function() {
                    alert('Request method not supported');
                },
                415: function() {
                    alert('Unsupportet Media Type');
                },
            },
                success: function (data) {
                if(data.message.indexOf('ok') != -1){
                            groupID = data.data.groupID;
                        }
                
                },
            complete: function (data) {
                if(groupID != ''){
                    pad_create();
                }
                else{
                    alert('unexpected error');  
                    }
            }
        })
    }

    function pad_create(){
        Url = padUrl+'/api/1/createGroupPad?apikey='+apiKey+'&groupID='+groupID+'&text=&padName='+pad_id+'&jsonp=?';
        $.ajax({
                async: false,
                url: Url,
            processData: true,
            dataType: 'json',
            statusCode: {
                404: function() {
                    alert('Seite nicht gefunden');
                },
                405: function() {
                    alert('Request method not supported');
                },
                415: function() {
                    alert('Unsupportet Media Type');
                },
            },
                success: function (data) {
                if(data.message.indexOf('ok') != -1 || data.message.indexOf('padName does already exist') != -1){

                    session_start();
                    }
                
            },
        })
    }

    function session_start(){
        validUntil=Math.round(new Date().getTime() / 1000 + 3600)
        Url = padUrl+'/api/1/createSession?apikey='+apiKey+'&groupID='+groupID+'&authorID='+authorID+'&validUntil='+validUntil+'&jsonp=?';
        $.ajax({
                async: false,
                url: Url,
            processData: true,
            dataType: 'json',
            statusCode: {
                404: function() {
                    alert('Seite nicht gefunden');
                },
                405: function() {
                    alert('Request method not supported');
                },
                415: function() {
                    alert('Unsupportet Media Type');
                },
            },
                success: function (data) {
                if(data.message.indexOf('ok') != -1){

                    var sessionID = data.data.sessionID;

                    var pad_Path = '/p/';
					
                    $.cookie('sessionID', sessionID, { expires: 7, path: '/'});
					
                    $('#user_Pad').pad({'host':padUrl,'baseUrl':'/p/','padId':groupID+'$'+pad_id,'userName':fname,'showControls':'true','showChat':'true','showLineNumbers':'true','height':$( window ).height()});
					
					$('#padId').text(groupID+'$'+pad_id);
					$('#authorId').text(authorID);
                }
            },
        })
    }


    $(document).ready(function(){

        pad_user();
    });

  </script>


</head>
<body>
    <div id="user_Pad"></div>
	<!-- <p style="color:#aaa; text-align: right;" id="padInfos">Pad-ID:&nbsp;<span id="padId"></span><br/>
	Author-ID:&nbsp;<span id="authorId"></span></p>	-->
</body>
</html>
