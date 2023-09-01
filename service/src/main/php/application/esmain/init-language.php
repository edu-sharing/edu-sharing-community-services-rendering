<?php
global $Translate, $LanguageCode, $Locale;
$Translate = new Phools_Translate_Array();
// LANGUAGE
$Locale = new Phools_Locale_Default(strtolower($LanguageCode), strtoupper($LanguageCode), ',', '.');
if (file_exists(dirname(__FILE__) . '/../../locale/esmain/'.strtoupper($LanguageCode).'/lang.common.php') ){
    require_once dirname(__FILE__) . '/../../locale/esmain/'.strtoupper($LanguageCode).'/lang.common.php';
}else{
    // use DE as fallback language
    $Locale = new Phools_Locale_Default('de', 'DE', ',', '.');
    require_once dirname(__FILE__) . '/../../locale/esmain/DE/lang.common.php';
}