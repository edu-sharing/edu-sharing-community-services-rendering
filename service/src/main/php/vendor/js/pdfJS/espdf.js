document.addEventListener('DOMContentLoaded', () => {
    const isPrint = event => event.keyCode === 80 && (event.ctrlKey || event.metaKey) && !event.altKey && (!event.shiftKey || window.chrome || window.opera);
    const isF12 = event => event.which === 123;
    const isDownload = event =>  event.which === 83 && (event.ctrlKey || event.metaKey);

    document.body.addEventListener('contextmenu', (event) => {event.preventDefault();});
    ['keyup', 'keydown'].forEach((type) => {
        document.addEventListener(type, event => (isF12(event) || isDownload(event)) && suppressEvent(event));
        window.addEventListener(type, event => isPrint(event) && suppressEvent(event));
    })
}, false);

const suppressEvent = event => {
    event.cancelBubble = true;
    event.preventDefault();
    if (event.stopImmediatePropagation) {
        event.stopImmediatePropagation();
    } else {
        event.stopPropagation();
    }
}
