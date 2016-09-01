<?php
/*
* $McLicense$
*
* $Id$
*
*/


class terms
    extends Step
{
    // IMPORTANT NOTE : the first step does not need a function check() !
    // the check function triggers the fallback to the previous step - so you see, eh? :)

    /**
     *
     */
    function check($p_post)
    {
        $this->writeLog('_REQUEST', $p_post);
        return true;
    }


    /**
     *
     */
    function getPage($p_post, $p_step)
    {

        return strtr(file_get_contents('./_layout/terms.lay'), array(
          '{step}' => $p_step,
          '{lang_id}' => INST_LANG_ID,
         // '{intro}'    => install_intro,
            '{label_14}' => install_label_14,
            '{label_15}' => install_label_15,
            '{terms.txt}' => file_get_contents(INST_PATH_LANG.'terms.txt'),
          '{form_name}'   => 'settings',
          '{form_target}' => '_self',
          '{form_action}' => 'install.php',
          '{form_submit}' => install_continue,
        ));

    } // end method getPage



} // end class form

