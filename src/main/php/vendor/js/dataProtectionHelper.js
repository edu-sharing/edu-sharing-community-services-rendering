const addParameters = (id) => {
    const hash = window.location.hash;
    const videoElement = jQuery('#' + id + ' video');
    if (videoElement.length && hash.includes('t=')) {
        videoElement.attr('src', videoElement.attr('src') + hash);
    }
}
