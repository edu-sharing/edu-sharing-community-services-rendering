<?php
/**
 * include this file to enforce the validation of a given signature and public key
 * will fail if the sig is invalid
 */

if(!isset($Logger)) {
    $Logger = require_once(MC_LIB_PATH . 'Log/init.php');
}
global $ts;
$ts = mc_Request::fetch('ts', 'CHAR');
if (empty($ts)) {
    $Logger->error('Missing request-param "timestamp".');
    throw new ESRender_Exception_MissingRequestParam('timestamp');
}
if (empty($_GET['sig'])) {
    $Logger->error('Missing request-param "sig".');
    throw new ESRender_Exception_MissingRequestParam('sig');
}

try {
    $pubkeyid = openssl_get_publickey($homeRep->prop_array['public_key']);
    $signature = rawurldecode($_GET['sig']);
    $signature = base64_decode($signature);
    $sigString = null;
    if(isset($data)) {
        $sigString = $data->node->ref->repo . $data->node->ref->id;
    } else {
        if (empty($_GET['sig_token']) || strlen($_GET['sig_token']) < 32) {
            $Logger->error('Missing request-param "sig_token" or "sig_token" too short, since no node was provided.');
            throw new ESRender_Exception_MissingRequestParam('sig');
        }
        $sigString = $_GET['sig_token'];
    }
    $ok = openssl_verify($sigString . $ts, $signature, $pubkeyid, 'sha1WithRSAEncryption');
    if ($ok != 1) {
        throw new ESRender_Exception_SslVerification('SSL signature check failed');
    }
} catch (Exception $e) {
    throw new ESRender_Exception_SslVerification('Error checking signature');
}