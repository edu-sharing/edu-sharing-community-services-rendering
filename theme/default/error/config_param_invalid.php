<?php
global $Locale, $Translate;
$msg = array();
$msg['error'] = new Phools_Message_Default('Error');
$msg['back'] = new Phools_Message_Default('back');
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo $msg['error'] -> localize($Locale, $Translate); ?></title>
    <link rel="stylesheet" type="text/css" href="../../theme/default/css/display.css">
</head>
<body>
    <div class="error">
        <span class="message"><?php echo htmlentities($error) ?> </span>
    </div>
</body>
</html>
