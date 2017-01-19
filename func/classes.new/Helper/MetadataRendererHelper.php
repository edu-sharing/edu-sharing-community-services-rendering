<?php

class MetadataRendererHelper {
	
	public static function render($metadata, $type = '', $cssClasses = array()) {
		
		switch($type) {
			
			case 'date':
				
				if(!empty($metadata)) {
					return '<label class="edusharing_rendering_metadata_body_label">' . ucfirst($metadata['label']) . '</label>
							<span class="edusharing_rendering_metadata_body_value ' . implode(' ', $cssClasses) . '">' . date('d.m.Y, H:i', strtotime($metadata['value'])) . '</span>';
				}
				
			break;
			
			default:
				
				$valString = '';
				if(strpos($metadata['value'], '[#]') !== false) {
					$valArray = explode('[#]', $metadata['value']);
					foreach($valArray as $value) {
						if(!empty($value))
							$valString .= '<span class="edusharing_rendering_metadata_body_value ' . implode(' ', $cssClasses) . '">' . htmlentities($value) . '</span>';
					}
				} else {
					if(!empty(trim($metadata['value'])))
						$valString = '<span class="edusharing_rendering_metadata_body_value ' . implode(' ', $cssClasses) . '">' . htmlentities($metadata['value']) . '</span>';
				}
				if(!empty($valString))
					return '<label class="edusharing_rendering_metadata_body_label">' . ucfirst($metadata['label']) . '</label>' . $valString;
		}
		
		return '';
		
	}
	
}