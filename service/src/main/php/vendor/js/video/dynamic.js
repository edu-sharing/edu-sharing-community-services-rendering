(function () {
	var content = document.getElementById('edusharing_rendering_content');
	if (!content) {
		return;
	}

	var videoFormat = 'webm';
	var v = document.createElement('video');
	if (v.canPlayType && v.canPlayType('video/mp4').replace(/no/, '')) {
		videoFormat = 'mp4';
	}

	var ajaxUrl = content.getAttribute('data-ajax-url');
	var authString = content.getAttribute('data-auth-string');

	window.get_resource = function (authstring) {
		jQuery.ajax({
			url: ajaxUrl + '&callback=get_resource&' + authstring + '&videoFormat=' + videoFormat,
			success: function (data) {
				jQuery('#edusharing_rendering_content').html(data);
			}
		});
	};

	window.get_resource(authString);
})();
