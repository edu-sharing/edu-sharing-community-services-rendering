<?php

class ESRender_DataProtectionRegulation_Handler {
    private function getDataPrivacyMessage($msg, $providerUrlTermsOfUse, $providerName){
                global $Locale, $Translate;
        $return = '<b>'.$msg['dataProtectionRegulations3']->localize($Locale, $Translate).'</b><br/>
                  <a href="'.$providerUrlTermsOfUse.'" target="_blank">'.$msg['dataProtectionRegulations']->localize($Locale, $Translate).'</a>';
        if(!empty($providerName)) {
            // $return .= ' '.$msg['of']->localize($Locale, $Translate).' '.$providerName;
        }
        $return .= '<br/>';
        return $return;
    }
    public function getApplyDataProtectionRegulationsDialog($esObject, $uniqueId, $providerName, $providerUrlTermsOfUse, $target, $type = null) {
        global $Locale, $Translate;

        if(defined('DISABLE_DATAPROTECTIONREGULATIONHANDLER_FOR') && !empty(DISABLE_DATAPROTECTIONREGULATIONHANDLER_FOR)) {
            $arr = explode(',', DISABLE_DATAPROTECTIONREGULATIONHANDLER_FOR);
            foreach($arr as $value) {
                if(strpos($target, $value) !== false)
                    return '';
            }
        }

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

        $button = '<a href="#" onclick="'.$this->getClickEvent($uniqueId).'" class="edusharing_rendering_content btn btn-primary dataProtectionRegulationsButton">'.$msg['dataProtectionRegulations4']->localize($Locale, $Translate).'</a>';

        switch($type) {
            case 'LTI_INLINE':
                $return = $this->getHeaderMessage($esObject, $msg);
                if(empty($providerUrlTermsOfUse)) {
                            $return .= '<b>'.$msg['dataProtectionRegulationsHintDefault']->localize($Locale, $Translate).'</b><br/>';
                        } else {
                            $return .= $this->getDataPrivacyMessage($msg, $providerUrlTermsOfUse, $providerName);
                        }
                $return .= '<a href="#" class="edusharing_rendering_content btn btn-secondary" onclick="document.getElementById(\'dataProtectionRegulations_'.$uniqueId.'\').style.display=\'none\';return false;">'.$msg['abort']->localize($Locale, $Translate).'</a>'.$button;
                $return .= '</p>
                    </div>
                </div>
                ';
                break;
            case 'LTI_DYNAMIC':
                $return = $this->getHeaderMessage($esObject, $msg);
                if(empty($providerUrlTermsOfUse)) {
                    $return .= '<b>'.$msg['dataProtectionRegulationsHintDefault']->localize($Locale, $Translate).'</b><br/>';

                } else {
                    $return .= $this->getDataPrivacyMessage($msg, $providerUrlTermsOfUse, $providerName);
                }
                $return .= $button.'
                        </p>
                    </div>
                </div>
                ';
                break;
            case 'VIDEO_DEFAULT':
                $return = $this->getHeaderMessage($esObject, $msg);
                if(empty($providerUrlTermsOfUse)) {
                    $return .= '<b>'.$msg['dataProtectionRegulationsHintDefault']->localize($Locale, $Translate).'</b><br/>';

                } else {
                    $return .= $this->getDataPrivacyMessage($msg, $providerUrlTermsOfUse, $providerName);
                }
                $return .= $button.'
                        </p>
                    </div>
                </div>
                ';
                break;
            case 'YOUTUBE':
            case 'VIMEO':
                $return = $this->getHeaderMessage($esObject, $msg);
            if(empty($providerUrlTermsOfUse)) {
                            $return .= '<b>'.$msg['dataProtectionRegulationsHintDefault']->localize($Locale, $Translate).'</b><br/>';

                        } else {
                            $return .= $this->getDataPrivacyMessage($msg, $providerUrlTermsOfUse, $providerName);
                        }
                            $return .= $button.'
                        </p>
                    </div>
                </div>
                ';
                break;
            case 'H5P':
            
                echo "<script>console.log(".json_encode($Translate).")</script>";

                $button = '<a href="#" onclick="'.$this->getClickEvent().'document.getElementById(\''.$uniqueId.'\').style.display=\'block\';" class="edusharing_rendering_content btn btn-primary dataProtectionRegulationsButton">'.$msg['dataProtectionRegulations4']->localize($Locale, $Translate).'</a>';
                $return = $this->getHeaderMessage($esObject, $msg);
                if(empty($providerUrlTermsOfUse)) {
                    $return .= '<b>'.$msg['dataProtectionRegulationsHintDefault']->localize($Locale, $Translate).'</b><br/>';

                } else {
                    $return .= $this->getDataPrivacyMessage($msg, $providerUrlTermsOfUse, $providerName);
                }
                $return .= $button.'
                        </p>
                    </div>
                </div>
                ';
                break;
            default:
                $button = '<a href="#" onclick="'.$this->getClickEvent().'document.getElementById(\''.$uniqueId.'\').style.display=\'block\';" class="edusharing_rendering_content btn btn-primary dataProtectionRegulationsButton">'.$msg['dataProtectionRegulations4']->localize($Locale, $Translate).'</a>';
                $return = $this->getHeaderMessage($esObject, $msg);
                if(empty($providerUrlTermsOfUse)) {
                    $return .= '<b>'.$msg['dataProtectionRegulationsHintDefault']->localize($Locale, $Translate).'</b><br/>';

                } else {
                    $return .= $this->getDataPrivacyMessage($msg, $providerUrlTermsOfUse, $providerName);
                }
                $return .= $button.'
                        </p>
                    </div>
                </div>
                ';
        }
        return $return;
    }

    private function getHeaderMessage($esObject, $msg)
    {
        //return '<pre>'.print_r( $esObject->getNode(), true);
        global $Locale, $Translate;
        return '<div class="dataProtectionRegulations">
                <img class="dataProtectionVideoBg" src="'. $esObject->getNode()->preview->url .'"></img>
                <div class="dataProtectionRegulationsContainer">
                    <span class="dataProtectionRegulationsHeading">'.$msg['dataProtectionRegulations1']->localize($Locale, $Translate).'</span>
                    <p>'.$msg['dataProtectionRegulations2']->localize($Locale, $Translate).'</p>
                    <p>';
    }

    private function getClickEvent($uniqueId)
    {
        return 'event.preventDefault();
                jQuery(this.parentElement.parentElement.parentElement).fadeOut({
                    complete: function() {
                        var frame=document.getElementById(\''.$uniqueId.'\');
                        console.log(frame);
                        if(frame) {
                            frame.src=frame.getAttribute(\'data-src\');
                            jQuery(frame).fadeIn();
                            frame.parentElement.style.position=\'\';
                            frame.parentElement.style.position=\'relative\';
                            frame.parentElement.style.paddingBottom=\'56.25%\';
                            frame.parentElement.style.paddingTop=\'25px\';
                            frame.parentElement.style.height=\'0\';
                        }
                        try {
                            jQuery(\'#videoWrapperInner_'.$uniqueId.'\').fadeIn();
                        } catch(e) { }
                        try {
                            jQuery(\'#'.$uniqueId.'\').fadeIn();
                        } catch(e) { }
                        window.dispatchEvent(new Event(\'resize\'));
                    }
                });
                
        ';
    }
}
