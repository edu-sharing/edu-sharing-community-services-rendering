<?php

/**
 *
 *
 *
 */
class Phools_Message_Default
extends Phools_Message_Abstract
{
    static function translate($str, $params = []) {
        global $Locale, $Translate;

        $paramsMapped = [];
        foreach($params as $k => $v) {
            $paramsMapped[] = new Phools_Message_Param_String($k, $v);
        }
        $msg = new Phools_Message_Default($str, $paramsMapped);
        return $msg->localize($Locale, $Translate);
    }
}
