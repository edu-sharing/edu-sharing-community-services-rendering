<?php

class ESRender_DataProtectionRegulation_Handler {

    public function getApplyDataProtectionRegulationsDialog($uniqueId, $providerName, $providerUrlTermsOfUse, $type = null) {
        global $Locale, $Translate;

        $msg = array();
        $msg['dataProtectionRegulations1'] = new Phools_Message_Default('dataProtectionRegulations1 :providerName', array(new Phools_Message_Param_String(':providerName', $providerName)));
        $msg['dataProtectionRegulations2'] = new Phools_Message_Default('dataProtectionRegulations2 :providerName', array(new Phools_Message_Param_String(':providerName', $providerName)));
        if(empty($providerName)) {
            $msg['dataProtectionRegulations1'] = new Phools_Message_Default('dataProtectionRegulations1default');
            $msg['dataProtectionRegulations2'] = new Phools_Message_Default('dataProtectionRegulations2default');
        }
        $msg['dataProtectionRegulationsHintDefault'] = new Phools_Message_Default('dataProtectionRegulationsHintDefault');
        $msg['dataProtectionRegulations3'] = new Phools_Message_Default('dataProtectionRegulations3');
        $msg['dataProtectionRegulations4'] = new Phools_Message_Default('dataProtectionRegulations4');
        $msg['dataProtectionRegulations'] = new Phools_Message_Default('dataProtectionRegulations');
        $msg['abort'] = new Phools_Message_Default('abort');
        $msg['of'] = new Phools_Message_Default('of');

        switch($type) {
            case 'LTI_INLINE':
                $button = '<a href="#" onclick="document.getElementById(\'dataProtectionRegulations_'.$uniqueId.'\').style.display=\'none\';document.getElementById(\'ltiLaunchForm_'.$uniqueId.'\').submit();document.getElementById(\'ltiLaunchForm_'.$uniqueId.'\').target = \'_blank\';document.getElementById(\'lti_frame_'.$uniqueId.'\').style.display=\'block\';" class="edusharing_rendering_content btn btn-primary dataProtectionRegulationsButton">'.$msg['dataProtectionRegulations4']->localize($Locale, $Translate).'</a>';
                $return = '
                    <div id="dataProtectionRegulations_'.$uniqueId.'" class="dataProtectionRegulationsDialog" style="position:absolute; display:none;max-width:500px;">
                        <span class="dataProtectionRegulationsHeading">'.$msg['dataProtectionRegulations1']->localize($Locale, $Translate).'</span>
                        <p>'.$msg['dataProtectionRegulations2']->localize($Locale, $Translate).'</p>
                        <p>';
                        if(empty($providerUrlTermsOfUse)) {
                            $return .= '<b>'.$msg['dataProtectionRegulationsHintDefault']->localize($Locale, $Translate).'</b><br/>';
                        } else {
                            $return .= '<b>'.$msg['dataProtectionRegulations3']->localize($Locale, $Translate).'</b><br/>
                            <a href="'.$providerUrlTermsOfUse.'" target="_blank">'.$msg['dataProtectionRegulations']->localize($Locale, $Translate).'</a> '.$msg['of']->localize($Locale, $Translate).' '.$providerName.'<br/>';
                        }
                $return .= '<a href="#" class="edusharing_rendering_content btn btn-secondary" onclick="document.getElementById(\'dataProtectionRegulations_'.$uniqueId.'\').style.display=\'none\';return false;">'.$msg['abort']->localize($Locale, $Translate).'</a>'.$button;
                $return .= '</p>
                    </div>
                ';
                break;
            case 'LTI_DYNAMIC':
                $button = '<a href="#" onclick="event.preventDefault();event.preventDefault();this.parentElement.parentElement.style.display=\'none\';document.getElementById(\'ltiLaunchForm_'.$uniqueId.'\').submit();document.getElementById(\'ltiLaunchForm_'.$uniqueId.'\').target = \'_blank\';document.getElementById(\'lti_frame_'.$uniqueId.'\').style.display=\'block\';" class="edusharing_rendering_content btn btn-primary dataProtectionRegulationsButton">'.$msg['dataProtectionRegulations4']->localize($Locale, $Translate).'</a>';
                $return = '
                    <div class="dataProtectionRegulations">
                        <span class="dataProtectionRegulationsHeading">'.$msg['dataProtectionRegulations1']->localize($Locale, $Translate).'</span>
                        <p>'.$msg['dataProtectionRegulations2']->localize($Locale, $Translate).'</p>
                        <p>';
                if(empty($providerUrlTermsOfUse)) {
                    $return .= '<b>'.$msg['dataProtectionRegulationsHintDefault']->localize($Locale, $Translate).'</b><br/>';

                } else {
                    $return .= '<b>'.$msg['dataProtectionRegulations3']->localize($Locale, $Translate).'</b><br/>
                                        <a href="'.$providerUrlTermsOfUse.'" target="_blank">'.$msg['dataProtectionRegulations']->localize($Locale, $Translate).'</a> '.$msg['of']->localize($Locale, $Translate).' '.$providerName.'<br/>';

                }
                $return .= $button.'
                        </p>
                    </div>
                ';
                break;
            case 'VIDEO_DEFAULT':
                $button = '<a href="#" onclick="event.preventDefault();this.parentElement.parentElement.style.display=\'none\';document.getElementById(\'videoWrapperInner_'.$uniqueId.'\').style.display=\'block\';" class="edusharing_rendering_content btn btn-primary dataProtectionRegulationsButton">'.$msg['dataProtectionRegulations4']->localize($Locale, $Translate).'</a>';
                $return = '
                    <div class="dataProtectionRegulations">
                        <span class="dataProtectionRegulationsHeading">'.$msg['dataProtectionRegulations1']->localize($Locale, $Translate).'</span>
                        <p>'.$msg['dataProtectionRegulations2']->localize($Locale, $Translate).'</p>
                        <p>';
                if(empty($providerUrlTermsOfUse)) {
                    $return .= '<b>'.$msg['dataProtectionRegulationsHintDefault']->localize($Locale, $Translate).'</b><br/>';

                } else {
                    $return .= '<b>'.$msg['dataProtectionRegulations3']->localize($Locale, $Translate).'</b><br/>
                                        <a href="'.$providerUrlTermsOfUse.'" target="_blank">'.$msg['dataProtectionRegulations']->localize($Locale, $Translate).'</a> '.$msg['of']->localize($Locale, $Translate).' '.$providerName.'<br/>';

                }
                $return .= $button.'
                        </p>
                    </div>
                ';
                break;
            case 'YOUTUBE':
            case 'VIMEO':
                $button = '<a href="#" onclick="event.preventDefault();var frame=document.getElementById(\''.$uniqueId.'\');this.parentElement.parentElement.style.display=\'none\';frame.src=frame.getAttribute(\'data-src\');frame.style.display=\'block\';" class="edusharing_rendering_content btn btn-primary dataProtectionRegulationsButton">'.$msg['dataProtectionRegulations4']->localize($Locale, $Translate).'</a>';
                $return = '
                    <div class="dataProtectionRegulations">
                        <span class="dataProtectionRegulationsHeading">'.$msg['dataProtectionRegulations1']->localize($Locale, $Translate).'</span>
                        <p>'.$msg['dataProtectionRegulations2']->localize($Locale, $Translate).'</p>
                        <p>';
                        if(empty($providerUrlTermsOfUse)) {
                            $return .= '<b>'.$msg['dataProtectionRegulationsHintDefault']->localize($Locale, $Translate).'</b><br/>';

                        } else {
                            $return .= '<b>'.$msg['dataProtectionRegulations3']->localize($Locale, $Translate).'</b><br/>
                                        <a href="'.$providerUrlTermsOfUse.'" target="_blank">'.$msg['dataProtectionRegulations']->localize($Locale, $Translate).'</a> '.$msg['of']->localize($Locale, $Translate).' '.$providerName.'<br/>';

                        }
                            $return .= $button.'
                        </p>
                    </div>
                ';
                break;
            default:
                $button = '<a href="#" onclick="event.preventDefault();this.parentElement.parentElement.style.display=\'none\';document.getElementById(\''.$uniqueId.'\').style.display=\'block\';" class="edusharing_rendering_content btn btn-primary dataProtectionRegulationsButton">'.$msg['dataProtectionRegulations4']->localize($Locale, $Translate).'</a>';
                $return = '
                    <div class="dataProtectionRegulations">
                        <span class="dataProtectionRegulationsHeading">'.$msg['dataProtectionRegulations1']->localize($Locale, $Translate).'</span>
                        <p>'.$msg['dataProtectionRegulations2']->localize($Locale, $Translate).'</p>
                        <p>';
                if(empty($providerUrlTermsOfUse)) {
                    $return .= '<b>'.$msg['dataProtectionRegulationsHintDefault']->localize($Locale, $Translate).'</b><br/>';

                } else {
                    $return .= '<b>'.$msg['dataProtectionRegulations3']->localize($Locale, $Translate).'</b><br/>
                                        <a href="'.$providerUrlTermsOfUse.'" target="_blank">'.$msg['dataProtectionRegulations']->localize($Locale, $Translate).'</a> '.$msg['of']->localize($Locale, $Translate).' '.$providerName.'<br/>';

                }
                $return .= $button.'
                        </p>
                    </div>
                ';
        }
        return $return;
    }
}