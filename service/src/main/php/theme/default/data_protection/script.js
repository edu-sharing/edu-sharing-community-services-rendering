function replaceData(event, id, html) {
    event.preventDefault();
    const htmlElement = document.getElementById(id + '-data');
    const appSelector = document.querySelector('es-app[ngCspNonce]');
    if(appSelector) {
        const nonce = appSelector.getAttribute('ngCspNonce');
        if (nonce) {
            html = html.replaceAll(
                '<style',
                '<style nonce="' +
                nonce +
                '"',
            );
        }
    }
    htmlElement.innerHTML = html;
    if (typeof addParameters === 'function') {
        addParameters(id + '-data');
        jQuery('#' + id + '-data').show();
        jQuery('#' + id + '-privacy').hide();
    } else {
        const getJsScript = document.getElementById('eduGetJS');
        let script = document.createElement('script');
        script.setAttribute('src', getJsScript.getAttribute('src').replace('getJS.php', 'dataProtectionHelper.js'));
        script.setAttribute('type', 'text/javascript');
        document.head.appendChild(script);
        script.addEventListener('load', () => {
            addParameters(id + '-data');
            jQuery('#' + id + '-data').show();
            jQuery('#' + id + '-privacy').hide();
        })
    }
}
function continueDataRegulation(event, id = null) {
    event.preventDefault();
    jQuery(this.parentElement.parentElement.parentElement).fadeOut({
        complete: function() {
            var frame=document.getElementById(id);
            console.log(frame);
            if(frame) {
                frame.src=frame.getAttribute('data-src');
                jQuery(frame).fadeIn();
                frame.parentElement.style.position='';
                frame.parentElement.style.position='relative';
                frame.parentElement.style.paddingBottom='56.25%';
                frame.parentElement.style.paddingTop='25px';
                frame.parentElement.style.height='0';
            }
            try {
                jQuery('#videoWrapperInner_' + id).fadeIn();
            } catch(e) { }
            try {
                jQuery('#' + id).fadeIn();
            } catch(e) { }
            window.dispatchEvent(new Event('resize'));
        }
    });
}
jQuery( document ).ready(() => {
    document.querySelectorAll('.dataProtectionRegulationsButton').forEach(b => {
        if (b.hasAttribute('data-id')) {
            b.addEventListener('click', evt =>
                replaceData(event, b.getAttribute('data-id'), b.getAttribute('data-content'))
            )
        }
    });
    const downloadUrl = document.getElementById("esRenderDownloadUrl");
    if(downloadUrl) {
        function registerDownloadUrl() {
           try{window.ngRender.setDownloadUrl(downloadUrl.value);}catch(err){console.log(err)}
        }
        registerDownloadUrl();
        setTimeout(registerDownloadUrl, 1000);
    }
});