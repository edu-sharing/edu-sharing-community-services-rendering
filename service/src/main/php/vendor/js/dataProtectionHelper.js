const addParameters = (id) => {
    const hash = window.location.hash;
    const videoElement = document.querySelector('#' + CSS.escape(id) + ' video');
    if (videoElement != null) {
        videoElement.addEventListener('contextmenu', (event) => {
            event.preventDefault();
            return false;
        })
        if (hash.includes('t=')) {
            if(videoElement.hasAttribute("src")) {
                videoElement.setAttribute('src', videoElement.getAttribute('src') + hash);
            }
            const tags = videoElement.getElementsByTagName("source");
            if(tags.length > 0) {
                videoElement.setAttribute('src', tags[0].getAttribute('src') + hash);
            }
        }
    }
}
