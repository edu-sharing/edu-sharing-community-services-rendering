let customEduStyle = document.createElement('style');
customEduStyle.type = 'text/css';
customEduStyle.innerText = '* { display: none  !important; }';
customEduStyle.setAttribute('media', 'print');
document.head.appendChild(customEduStyle);
