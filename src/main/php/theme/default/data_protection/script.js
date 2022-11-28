function replaceData(event, id, html) {
    console.log(event, id, html);
    event.preventDefault();
    jQuery('#' + id + '-data').html(html);
    jQuery('#' + id + '-data').show();
    jQuery('#' + id + '-privacy').hide();
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
    document.querySelectorAll('.dataProtectionRegulationsButton').forEach(b =>
        b.addEventListener('click', evt =>
            replaceData(event, b.getAttribute('data-id'), b.getAttribute('data-content'))
        )
    );
});