const addParameters = (id) => {
    const hash = window.location.hash;
    const videoElement = document.querySelector('#' + CSS.escape(id) + ' video');
    if (videoElement != null) {
        videoElement.addEventListener('contextmenu', (event) => {
            event.preventDefault();
            return false;
        })
        if (hash.includes('t=')) {
            videoElement.setAttribute('src', videoElement.getAttribute('src') + hash);
        }
    }
}
