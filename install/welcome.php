<?php
/*
* $McLicense$
*
* $Id$
*
*/


class welcome
    extends Step
{
    // IMPORTANT NOTE : the first step does not need a function check() !
    // the check function triggers the fallback to the previous step - so you see, eh? :)

    /**
     *
     */
    function getPage($p_post, $p_step)
    {
        global $ilang, $supported_lang;

        $phpVersionRequired = '5.3.0';
        $phpVersionCurrent = phpversion();
                if (version_compare($phpVersionCurrent, $phpVersionRequired, '<'))
                {
                        SysMsg::showError("Your php version {$phpVersionCurrent} is too low!<br>");
                        SysMsg::showError("You need version {$phpVersionRequired} at least.");
                        return false;
                }

        $lang_options = array();
        foreach ($supported_lang as $lang_id => $lang_iso)
        {
            $selected = ($lang_id == INST_LANG_ID) ? 'selected="selected"' : '';
            if($lang_iso == EN)
                $langStr = 'English';
            if($lang_iso == 'DE')
                $langStr = 'deutsch';
            $lang_options[] = '<option value="'.$lang_id.'" '.$selected.'>'.$langStr.'</option>';
        }

        return strtr(file_get_contents('./_layout/welcome.lay'), array(
            '{step}'  => $p_step,
           // '{intro}' => install_intro,
            '{label_12}' => install_label_12,
            '{lang_options}' => (implode("\n", $lang_options)),
            '{welcome.htm}' => file_get_contents(INST_PATH_LANG.'welcome.htm'),
          '{form_name}'   => 'settings',
          '{form_target}' => '_self',
          '{form_action}' => 'install.php',
          '{form_submit}' => install_continue,
        ));
    } // end method getPage

} // end class form

