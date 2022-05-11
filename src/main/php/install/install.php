<?php
/*
* $McLicense$
*
* $Id$
*
*/

function getStep($_req)
{
    if (!isset($_req['step']))
    {
        return 0;
    }

    $step = intval($_req['step']) + 1;

    if ($step < 0)
    {
        return 0;
    }

    return $step;
} // end method getStep

@set_time_limit(0);
$localDir = '.' . DIRECTORY_SEPARATOR;

if(@file_exists(__DIR__ . '/../conf/system.conf.php')) {
    error_log('Delete conf/system.conf.php to restart installation process');
    die();
}

if ( @file_exists($localDir . 'install' . DIRECTORY_SEPARATOR . 'install.php') )
{
    header('Location: ./install/install.php');
    die('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="expires" content="0">
        <meta http-equiv="refresh" content="0;URL=./install/install.php">
    </head>
    <body>
        <h2>Sie wurden nicht automatisch weitergeleitet?</h2>
        Bitte klicken Sie hier:
        <a href="./install/install.php?LANG=1">Weiter zur Startseite</a><br>
        <hr>
        <br>
        <h2>You weren\'t redirected?</h2>
        Please click here:
        <a href="./install/install.php?LANG=2">goto home page</a>
    </body>
</html>');
}

require_once($localDir . '_inc' . DIRECTORY_SEPARATOR . 'conf.php');
require_once(INST_PATH_LIB.'Step.php');

$step = getStep($_REQUEST);


$list = array(
    'welcome',
    'terms',
    'form',
    'initdb',
    'execute',
);

// check data
$msg = '';
while (true)
{
    // create object for current step
    $stepName = $list[$step];
    $stepFile = $stepName . '.php';
    require_once($localDir . $stepFile);
    $stepObj = new $stepName();

    // fetch stored request data into superglobal request array
    $_REQUEST = array_merge($stepObj->readlog('_REQUEST', array()), $_REQUEST);

    // IMPORTANT NOTE :
    // object of the first step must return just TRUE on check() and process() !
    if ($stepObj->check($_REQUEST) == false) {
        // data check failed, get error message, step back and try again
        $msg .= $stepObj->getMsg();
        $step--;
        continue;
    }

    if ($stepObj->process($_REQUEST) == false) {
        // data process failed, get error message, step back and try again
        $msg .= $stepObj->getMsg();
        $step--;
        continue;
    }

    break;
} // end while

$fileHead = $localDir . '_layout' . DIRECTORY_SEPARATOR  . 'head.lay';
$htmlHead = file_get_contents($fileHead);

$file2Do  = $localDir . '2do.htm';
if ( file_exists($file2Do) ) {
    $info2do = file_get_contents('.'.DIRECTORY_SEPARATOR.'2do.htm');
}
else {
    $info2do = '';
}

$message = $msg . $stepObj->getMsg();

echo strtr($htmlHead, array(
  '{title}'     => install_headline,
  '{headline}'  => install_headline,
  '{edulogo}'  => install_logo,
  '{style_uri}' => './_style/',
  '{msg}' => $message,
  '{2do}' => $info2do,
  
));
echo $stepObj->getPage($_REQUEST, $step);
echo '
</body>
</html>';


