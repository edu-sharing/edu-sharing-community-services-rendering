const href = document.getElementById('edusharing_rendering_content_href');
if(href) {
    href.addEventListener('click', () => {
        const nodeId=href.getAttribute('data-nodeId');
        const req = new XMLHttpRequest();
        req.open("PUT", "/edu-sharing/rest/tracking/v1/tracking/-home/OPEN_EXTERNAL_LINK?node=" + encodeURIComponent(nodeId));
        req.send();
    })
}