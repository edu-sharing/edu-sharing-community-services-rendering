<?php

require_once '../../../conf.inc.php';

global $MC_URL, $CC_RENDER_PATH;

// start session
if (empty($ESRENDER_SESSION_NAME)) {
    error_log('ESRENDER_SESSION_NAME not set in conf/system.conf.php');
    $ESRENDER_SESSION_NAME = 'ESSID';
}

session_name($ESRENDER_SESSION_NAME);

$sessid = mc_Request::fetch($ESRENDER_SESSION_NAME, 'CHAR', '');
if (!empty($sessid)) {
    session_id($sessid);
}

try {
    if (!session_start()) {
        throw new Exception('Could not start session.');
    }
    $esrenderSessionId = session_id();
    if (!$esrenderSessionId) {
        throw new Exception('Could not get current session_id().');
    }
    $idEnc = $_GET['object'] ?? 0;
    $id    = (int)base64_decode($idEnc);
    $id === 0 && throw new Exception('PDF object id not found or not numeric');
    $pdo  = RsPDO::getInstance();
    $sql  = 'SELECT * FROM "ESOBJECT" WHERE "ESOBJECT_ID" = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    empty($result) && throw new Exception('PDF result set empty');
    $path        = $CC_RENDER_PATH . DIRECTORY_SEPARATOR . 'doc' . DIRECTORY_SEPARATOR . $result['ESOBJECT_PATH'];
    $path        .= DIRECTORY_SEPARATOR . $result['ESOBJECT_OBJECT_ID'] . '_' . $result["ESOBJECT_OBJECT_VERSION"];
    $data        = file_get_contents($path);
    $encodedData = base64_encode($data);
    $response    = ['data' => $encodedData, 'error' => null];
} catch (Exception $exception) {
    error_log($exception->getMessage());
    $response = ['data' => null, 'error' => 'Internal error'];
}

echo json_encode($response);
