function toggle_edusharing_rendering_metadata() {
	var el = document.getElementById('edusharing_rendering_metadata');
	var toptoggle = document.getElementById('edusharing_rendering_metadata_top_toggle');
	if(el.style.display != 'none') {
		el.style.display = 'none';
		toptoggle.innerHTML = '<?php echo $msg['showInformation']->localize($Locale, $Translate);?>';
		toptoggle.title = '<?php echo $msg['showInformation']->localize($Locale, $Translate);?>';
	} else {
		el.style.display = '';
		toptoggle.innerHTML = '<?php echo $msg['hideInformation']->localize($Locale, $Translate);?>';
		toptoggle.title = '<?php echo $msg['hideInformation']->localize($Locale, $Translate);?>';
	}
}

function close_edusharing_rendering_metadata() {
	document.getElementById('edusharing_rendering_metadata').style.display = 'none';
	document.getElementById('edusharing_rendering_metadata_top_toggle').innerHTML = '<?php echo $msg['showInformation']->localize($Locale, $Translate);?>';
	document.getElementById('edusharing_rendering_metadata_top_toggle').title = '<?php echo $msg['showInformation']->localize($Locale, $Translate);?>';
	return true;
}

var renderwidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
if(renderwidth < 1000) {
	toggle_edusharing_rendering_metadata();
}