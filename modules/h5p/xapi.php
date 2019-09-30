<?php

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

$data = $json_obj['data'];
$h5p_url = $json_obj['url'];
$h5p_title = $json_obj['title'];

$h5pxapi_response_message=NULL;
$statementObject=json_decode(stripslashes($data["statement"]),TRUE);

if (isset($statementObject["context"]["extensions"]) && !$statementObject["context"]["extensions"]) {
    unset($statementObject["context"]["extensions"]);
}

$settings=[
        "endpoint_url"=>"http://192.168.16.74/data/xAPI",
        "username"=>"fbec2aae966c8a5f42b2bd8d10eb070ad0644dea",
        "password"=>"c94971c025c5cc573085cac523712b457ea50932",
    ];

//$statementObject['actor']['actor']['name'] = $h5p_url;
$statementObject['object']['id'] = $h5p_url;
$statementObject['object']['definition']['name']['en-US'] = $h5p_title;
$content=json_encode($statementObject);
error_log($content);
//echo 'CONTENT: '.print_r($statementObject['context']['contextActivities']['category'][0]['id'], true);die();
//echo 'CONTENT: '.print_r($content, true);

$url=$settings["endpoint_url"];
if (!trim($url)) {
    echo json_encode(array(
        "ok"=>1,
        "message"=>$h5pxapi_response_message
    ));
    exit;
}

if (substr($url,-1)!="/")
    $url.="/";
$url.="statements";
$userpwd=$settings["username"].":".$settings["password"];

$headers=array(
    "Content-Type: application/json",
    "X-Experience-API-Version: 1.0.1",
);

$curl=curl_init();
curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE);
curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
curl_setopt($curl,CURLOPT_USERPWD,$userpwd);
curl_setopt($curl,CURLOPT_URL,$url);
curl_setopt($curl,CURLOPT_POST,1);
curl_setopt($curl,CURLOPT_POSTFIELDS,$content);

$res=curl_exec($curl);
$decoded=json_decode($res,TRUE);
$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);

error_log('code: '.$code);

// We rely on the response to be an array with a single entry
// constituting a uuid for the inserted statement, something like
// ["70de9692-2a4e-4f66-8441-c15ef534b690"].
// Is this learninglocker specific?
if ($code!=200 || sizeof($decoded)!=1 || strlen($decoded[0])!=36) {
    $response = array(
        "ok" => 0,
        "message" => "Unknown error",
        "code" => $code
    );

    if ($decoded["message"]){
        $response["message"] = $decoded["message"];
    }

    if (is_string($res)){
        $response["message"] = $res;
    }

    if ($res == FALSE) {
        $response["message"] = curl_error($curl);
    }

    echo json_encode($response);
    exit;
}

//error_log(print_r($h5pxapi_response_message, true));
$response=array(
    "ok"=>1,
    "message"=>$h5pxapi_response_message
);
echo json_encode($response);