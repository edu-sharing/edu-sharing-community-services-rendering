<?php session_start();
require_once (dirname(__FILE__) . '/../../conf.inc.php');
define('USERNAME', $dbuser);
define('PASSWORD_MD5', md5($pwd));
define('SESSION_LIFETIME_MINUTES', 30);

if (!empty($_SESSION['expire']) && time() > $_SESSION['expire']) {
    $_SESSION['loggedin'] = 0;
    $showTimeout = true;
}

if ($_GET['logout']) {
    $_SESSION['loggedin'] = 0;
}

if ($_GET['login']) {
    if ($_POST['username'] == USERNAME && md5($_POST['password']) == PASSWORD_MD5) {
        $_SESSION['loggedin'] = 1;
        $_SESSION['expire'] = time() + (SESSION_LIFETIME_MINUTES * 60);
    } else {
        $showLoginError = true;
        $_SESSION['loggedin'] = 0;
    }
}

if (!import_metadata)
    die('metadata import disabled');

require_once (MC_LIB_PATH . 'ESApp.php');
require_once (MC_LIB_PATH . 'EsApplications.php');
require_once (MC_LIB_PATH . 'EsApplication.php');

if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'de') {
    $langArr['moodleExampleUrl'] = 'moodle';
    $langArr['mcExampleUrl'] = 'metacoon';
    $langArr['exampleEndpoints'] = 'Beispielendpunkt';
    $langArr['exampleEndpointsHint'] = '(Zum &Uuml;bernehmen anklicken)';
    $langArr['import'] = 'Import';
    $langArr['importHeading'] = 'edu-sharing Metadatenimport';
    $langArr['importSuccess'] = 'Import erfolgreich';
    $langArr['addedToRegistry'] = 'registriert';
    $langArr['Application'] = 'Applikation';
    $langArr['propsUpdatedForApplication'] = 'Metadaten wurden aktualisiert f&uuml;r ';
    $langArr['addedValuesFor'] = 'Werte wurden hinzugef&uuml;gt f&uuml;r';
    $langArr['alreadyRegistered'] = 'ist bereits registriert.';
    $langArr['couldNotLoad'] = 'Fehler beim Laden von';
    $langArr['checkUrl'] = 'Bitte &uuml;berpr&uuml;fen Sie die URL.';
    $langArr['homerepoExampleUrl'] = 'Heimrepositorium';
    $langArr['obsolete'] = '(obsolet bei Neuinstallation)';
    $langArr['endpoint'] = 'Metadatenendpunkt';
    $langArr['loginError'] = 'Ungültige Zugangsdaten';
    $langArr['username'] = 'Nutzername';
    $langArr['password'] = 'Passwort';
    $langArr['timeout'] = 'Ihre Sitzung ist abgelaufen. Bitte loggen Sie sich erneut ein.';

} else {
    $langArr['moodleExampleUrl'] = 'moodle example endpoint';
    $langArr['mcExampleUrl'] = 'metacoon example endpoint';
    $langArr['exampleEndpoints'] = 'Example endpoint';
    $langArr['exampleEndpointsHint'] = '(Click to apply)';
    $langArr['import'] = 'Import';
    $langArr['importHeading'] = 'edu-sharing metadata import';
    $langArr['importSuccess'] = 'Import successful';
    $langArr['addedToRegistry'] = 'added to registry';
    $langArr['Application'] = 'Application';
    $langArr['propsUpdatedForApplication'] = 'Metadata updated for';
    $langArr['addedValuesFor'] = 'Values added for';
    $langArr['alreadyRegistered'] = 'is already registered.';
    $langArr['couldNotLoad'] = 'Error while loading';
    $langArr['checkUrl'] = 'Please check the URL.';
    $langArr['homerepoExampleUrl'] = 'Home repository';
    $langArr['obsolete'] = '(obsolete for fresh installations)';
    $langArr['endpoint'] = 'Metadata endpoint';
    $langArr['loginError'] = 'Wrong credentials';
    $langArr['username'] = 'Username';
    $langArr['password'] = 'Password';
    $langArr['timeout'] = 'Your session expired. Please log in again.';
}

if (!$_SESSION['loggedin']) {
    $err = '';
    if ($showLoginError) {
        $err = '<span id="message"><div class="user_message user_error">' . $langArr['loginError'] . '</div></span>';
    }
    if ($showTimeout) {
        $err = '<span id="message"><div class="user_message user_error">' . $langArr['timeout'] . '</div></span>';
    }
    echo '
     <html><head><title>' . $langArr['importHeading'] . '</title><meta charset="utf-8"><link rel="stylesheet" type="text/css" href="../../admin/css/style.php"></head><body>
       <h2>' . $langArr['importHeading'] . '</h2>
       ' . $err . '
        <form class="login" action="?login=1" method="post">
        <label for="username">' . $langArr['username'] . '</label><input type="text" name="username" />
        <label for="password">' . $langArr['password'] . '</label><input type="password" name="password" />
        <input class="import_button" type="submit" value="Login"/>
        </form></body></html>';
    exit(0);
}
?>

<html>
<head>
<title>edu-sharing metadata import</title>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="../../admin/css/style.php">
</head>
<body>
<?php

function getForm($url, $langArr) {

    $form = '
            
            <p><b>' . $langArr['exampleEndpoints'] . '</b>&nbsp;' . $langArr['exampleEndpointsHint'] . '</p>
            <form action="import_metadata.php" method="post" name="mdform">
                <table>
                    <tr>
                    <td>' . $langArr['homerepoExampleUrl'] . ': </td><td><a href="javascript:void();" onclick="document.forms[0].mdataurl.value=\'http://your-server-name/edu-sharing/metadata?format=render\'">http://edu-sharing-server/edu-sharing/metadata?format=render</a>&nbsp;' . $langArr['obsolete'] . '</td>
                    </tr> 
                   <!-- <tr>
                    <td>' . $langArr['moodleExampleUrl'] . ': </td><td> <a href="javascript:void();" onclick="document.forms[0].mdataurl.value=\'http://your-server-name/moodle/mod/edusharing/metadata.php\'">http://moodle-server/mod/edusharing/metadata.php?format=render</a></td>
                    </tr>
                    <tr>
                    <td>' . $langArr['mcExampleUrl'] . ': </td><td><a href="javascript:void();" onclick="document.forms[0].mdataurl.value=\'http://your-server-name/metacoon/extension/edu-sharing/metadata.php?format=render\'">http://metacoon-server/metacoon/extension/edu-sharing/metadata.php?format=render</a></td>
                    </tr> -->
                    </td>
                    </tr>
                    <tr><td colspan="2" style="padding-top: 20px"></td</tr>
                    <tr>
                    <td><label for="metadata">' . $langArr['endpoint'] . ': </label></td>
                    <td><input type="text" size="80" id="metadata" name="mdataurl" value="' . $url . '"></td>
                    </tr>
                    <tr>
                    <td></td><td><input type="submit" class="import_button" value="' . $langArr['import'] . '"></td>
                    </tr>
                </table>
            </form>';
    return $form;

}

echo '<h2>' . $langArr['importHeading'] . '</h2>';
echo '<div style="position: absolute; top: 10px; left: 0px; width: 815px; text-align: right;"><a href="?logout=1" class="import_button">Logout</a></div>';

$path = dirname(dirname(dirname(__FILE__)));

$filename = '';

if (!empty($_POST['mdataurl'])) {

    $xml = new DOMDocument();

    $internal_errors = libxml_use_internal_errors(true);

    if ($xml -> load($_POST['mdataurl']) == false) {
        echo '<span id="message"><div class="user_message user_error">' . $langArr['couldNotLoad'] . ' ' . $_POST['mdataurl'] . '. ' . $langArr['checkUrl'] . '</div></span>';
        echo getForm($_POST['mdataurl'], $langArr);
        die();
    } else {

    }

    libxml_use_internal_errors($internal_errors);
    $xml -> preserveWhiteSpace = false;
    $xml -> formatOutput = true;
    $entrys = $xml -> getElementsByTagName('entry');

    $setHomerep = false;

    foreach ($entrys as $entry) {

        if ($entry -> getAttribute('key') == 'appid') {
            $filename = "app-" . $entry -> nodeValue . ".properties.xml";
            $appId = $entry -> nodeValue;
        }

        if ($entry -> getAttribute('key') == 'type') {
            if ($entry -> nodeValue == 'REPOSITORY') {
                $setHomerep = true;
            }
        }
    }

    if ($setHomerep) {

        $homeAppProps = new DOMDocument();
        $homeAppProps -> load($path . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'esmain' . DIRECTORY_SEPARATOR . 'homeApplication.properties.xml');
        $existingEntries = $homeAppProps -> getElementsByTagName('entry');

        foreach ($existingEntries as $existingEntry) {
            if ($existingEntry -> getAttribute('key') == 'homerepid') {
                $existingEntry -> nodeValue = $appId;
                $homeAppProps -> save($path . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'esmain' . DIRECTORY_SEPARATOR . 'homeApplication.properties.xml');
            } else {
                $xmlTmp = simplexml_load_file($path . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'esmain' . DIRECTORY_SEPARATOR . 'homeApplication.properties.xml');
                if ($xmlTmp) {
                    $entry = $xmlTmp -> addChild("entry");
                    $entry -> addAttribute("key", 'homerepid');
                    $entry[0] = $appId;
                    $xmlTmp -> asXML($path . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'esmain' . DIRECTORY_SEPARATOR . 'homeApplication.properties.xml');
                }
            }

        }
    }

    $path = dirname(dirname(dirname(__FILE__)));

    $file = $path . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'esmain' . DIRECTORY_SEPARATOR . $filename;

    $app_reg = new DOMDocument();
    $app_reg_file = $path . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'esmain' . DIRECTORY_SEPARATOR . 'ccapp-registry.properties.xml';
    $app_reg -> load($app_reg_file);
    $app_reg -> preserveWhiteSpace = false;

    $entrys = $app_reg -> getElementsByTagName('entry');
    foreach ($entrys as $entry) {
        if ($entry -> getAttribute('key') == 'applicationfiles') {
            if (!strrpos($entry -> nodeValue, $filename)) {
                $app_reg -> save($app_reg_file . '_' . time() . '.bak');
                $entry -> nodeValue = $entry -> nodeValue . ',' . $filename;

                $props = $app_reg -> getElementsByTagName('properties');
                $props -> item(0);

                $comment = $app_reg -> createElement('comment', 'added new trusted app ' . $filename);
                $app_reg -> save($app_reg_file);
                $xml -> save($file);

                echo '<span id="message"><div class="user_message user_info">' . $langArr['importSuccess'] . '</div></span><span id="message"><div class="user_message user_info">' . $filename . ' ' . $langArr['addedToRegistry'] . '</div></span>';
            } else {

                //get keys from received config file
                $newKeys = array();
                foreach ($xml->getElementsByTagName('entry') as $entry) {
                    $newKeys[$entry -> getAttribute('key')] = $entry -> nodeValue;
                }

                //get keys from existing config file
                $existingProps = new DOMDocument();
                $existingProps -> load($path . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'esmain' . DIRECTORY_SEPARATOR . $filename);
                $existingEntries = $existingProps -> getElementsByTagName('entry');

                foreach ($existingEntries as $existingEntry) {
                    $existingKeys[$existingEntry -> getAttribute('key')] = $existingEntry -> getAttribute('key');
                }

                //get diff
                $diff = array_diff_key($newKeys, $existingKeys);

                //add additional keys
                if (!empty($diff)) {
                    foreach ($diff as $key => $v) {
                        $xmlTmp = simplexml_load_file($path . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'esmain' . DIRECTORY_SEPARATOR . $filename);
                        $entry = $xmlTmp -> addChild("entry");
                        $entry -> addAttribute("key", $key);
                        $entry[0] = $newKeys[$key];
                        $xmlTmp -> asXML($path . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'esmain' . DIRECTORY_SEPARATOR . $filename);
                    }
                    echo '<span id="message"><div class="user_message user_warning">' . $langArr['propsUpdatedForApplication'] . ' ' . $filename . '</div></span><span id="message"><div class="user_message user_info">' . $langArr['addedValuesFor'] . ' ';
                    echo implode(', ', array_keys($diff));
                    echo '</div></span>';
                } else {
                    // no changes
                    echo '<span id="message"><div class="user_message user_warning">' . $langArr['Application'] . ' ' . $filename . ' ' . $langArr['alreadyRegistered'] . '</div></span>';
                }

            }
        };
    };
};

echo getForm('', $langArr);
die();
