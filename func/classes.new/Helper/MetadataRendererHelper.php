<?php

class MetadataRendererHelper {
	
	public static function render($metadata, $type = '', $cssClasses = '', $alternativeLabel = '', $hideLabel = false) {

        $label = $metadata['label'];
	    if(!empty($alternativeLabel)) {
	        $label = $alternativeLabel;
        }

        if(is_array($cssClasses))
            $cssClasses = implode(' ', $cssClasses);
	    else
            $cssClasses = $cssClasses;




		switch($type) {
			
			case 'date':
				
				if(!empty($metadata)) {
					return '<label class="edusharing_rendering_metadata_body_label">' . ucfirst($label) . '</label>
							<span class="edusharing_rendering_metadata_body_value ' . $cssClasses . '">' . date('d.m.Y, H:i', strtotime($metadata['value'])) . '</span>';
				}
				
			break;
			
			default:
				
				$valString = '';
				if(strpos($metadata['value'], '[#]') !== false) {
					$valArray = explode('[#]', $metadata['value']);
					foreach($valArray as $value) {
						if(!empty($value))
							$valString .= '<span class="edusharing_rendering_metadata_body_value ' . $cssClasses . '">' . htmlentities($value) . '</span>';
					}
				} else {
					if(!empty($metadata['value']))
						$valString = '<span class="edusharing_rendering_metadata_body_value ' . $cssClasses . '">' . htmlentities($metadata['value']) . '</span>';
				}
				if(!empty($valString) && !$hideLabel)
					return '<label class="edusharing_rendering_metadata_body_label">' . ucfirst($label) . '</label>' . $valString;
				
				if(!empty($valString))
					return $valString;
		}
		
		return '';
		
	}
	
}