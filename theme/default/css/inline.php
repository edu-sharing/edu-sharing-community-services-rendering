<?php
header("Content-type: text/css");
global $MC_URL;?>

@font-face {
    font-family: 'Material Icons';
    font-style: normal;
    font-weight: 400;
    src: url('<?php echo $MC_URL?>/vendor/fonts/materialicons/MaterialIcons-Regular.eot'); /* For IE6-8 */
    src: local('Material Icons'),
    local('MaterialIcons-Regular'),
    url('<?php echo $MC_URL?>/vendor/fonts/materialicons/MaterialIcons-Regular.woff2') format('woff2'),
    url('<?php echo $MC_URL?>/vendor/fonts/materialicons/MaterialIcons-Regular.woff') format('woff'),
    url('<?php echo $MC_URL?>/vendor/fonts/materialicons/MaterialIcons-Regular.ttf') format('truetype');
}

.material-icons {
    font-family: 'Material Icons';
    font-weight: normal;
    font-style: normal;
    font-size: 24px;  /* Preferred icon size */
    display: inline-block;
    line-height: 1;
    text-transform: none;
    letter-spacing: normal;
    word-wrap: normal;
    white-space: nowrap;
    direction: ltr;

    /* Support for all WebKit browsers. */
    -webkit-font-smoothing: antialiased;
    /* Support for Safari and Chrome. */
    text-rendering: optimizeLegibility;

    /* Support for Firefox. */
    -moz-osx-font-smoothing: grayscale;

    /* Support for IE. */
    font-feature-settings: 'liga';
}

.material-icons {
    vertical-align: middle !important;
}

.edusharing_metadata_wrapper {
margin-top: 15px;
}

.edusharing_metadata_toggle_button {
cursor: pointer;
}

.edusharing_metadata {
position: absolute;
margin-top: 4px;
padding: 11px 0px;
z-index: 50;
}

.edusharing_metadata_inner {
padding: 0 0 20px 0;
background-color: #f6f6f6;
border: 1px solid #ccc;
-webkit-box-shadow: 0px 4px 5px 0px rgba(0,0,0,0.5);
-moz-box-shadow: 0px 4px 5px 0px rgba(0,0,0,0.5);
box-shadow: 0px 4px 5px 0px rgba(0,0,0,0.5);
max-height:500px;
overflow-y: scroll;
}

.edusharing_metadata:before {
border-bottom: 15px solid #ccc;
border-left: 15px solid transparent;
border-right: 15px solid transparent;
top: -4px;
content: "";
position: absolute;
left: 10%;
width: 0;
height: 0;
}

.edusharing_metadata:after {
border-bottom: 15px solid #fff;
border-left: 15px solid transparent;
border-right: 15px solid transparent;
top: -3px;
content: "";
position: absolute;
left: 10%;
width: 0;
height: 0;
}

.mdsGroup {
padding: 0;
}

.mdsGroup:first-child {
margin-top: -30px;
}}