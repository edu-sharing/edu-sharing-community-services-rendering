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

.edusharing_rendering_content_wrapper {
    min-width: 300px;
    line-height: 1.2;
}

div.edusharing_rendering_content_video_options {
    position: absolute;
    top: 2%;
    right: 2%;
    color: #fff;
    margin: 0;
    padding: 5px;
    text-align: right;
}

div.edusharing_rendering_content_video_options i {
    opacity: 0.8;
    cursor: pointer;
}

div.edusharing_rendering_content_video_options_content {
    display: none;
}

div.edusharing_rendering_content_video_wrapper {
    display: inline-block;
    position: relative;
}

div.edusharing_rendering_content_video_wrapper:hover div.edusharing_rendering_content_video_options {
    display: block;
}

ul.edusharing_rendering_content_video_options_resolutions {
    padding: 0;
    margin: 0;
    font-size: 18px !important;
}

ul.edusharing_rendering_content_video_options_resolutions i {
    vertical-align: middle;
    margin-right: 10px;
    font-size: 18px !important;
}

i.edusharing_rendering_content_video_options_toggle {
    text-shadow: 0px 0px 4px #333;
}

ul.edusharing_rendering_content_video_options_resolutions > li {
    cursor: pointer;
    padding: 4px 0;
    list-style: none;
}

ul.edusharing_rendering_content_video_options_resolutions > li.edusharing_rendering_content_video_options_resolutions_converting {
    color: #999999
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

.edusharing_metadata_wrapper {
    margin-left: 10px;
}

.edusharing_metadata_toggle_button {
    width: 30px; height: 30px; display: inline-block;
    border-left: 1px solid #cdcdcd;
    padding-left: 5px;
    cursor:pointer;
    padding-top: 3px;
    color: #4D799A;
    transition: ease-in-out 0.3s;
}

.edusharing_metadata_toggle_button:hover {
    color: #00a0d2;
}

.edusharing_metadata {
    position: absolute;
    margin-top: 4px;
    margin-left: -295px;
    width: 350px;
    padding: 11px 0px;
    z-index: 50;
}

.edusharing_metadata_inner {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    background-color: #f6f6f6;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-shadow: 2px 2px 6px rgba(0, 0, 0, .2);
    max-height:500px;
    overflow-y: auto;
    font-size: .9em;
    line-height: 1;
    transition: ease-in-out 0.3s;
}

.edusharing_metadata_inner img {
    height: 16px;
}

.edusharing_metadata_inner .mdsGroup .mdsCaption {
    font-size: 1.5em;
    height: auto;
    padding-bottom: 10px;
    margin: 0;
}

.edusharing_rendering_metadata_body .mdsCaption{
    border-bottom: 1px solid #777 !important;
    color: #777 !important;
    padding: 0 12px !important;
    margin-top: 0 !important;
}

.edusharing_metadata:before {
    border-bottom: 15px solid #ddd;
    border-left: 15px solid transparent;
    border-right: 15px solid transparent;
    top: -4px;
    content: "";
    position: absolute;
    right: 23px;
    width: 0;
    height: 0;
}

.edusharing_metadata:after {
    border-bottom: 15px solid #f6f6f6;
    border-left: 15px solid transparent;
    border-right: 15px solid transparent;
    top: -3px;
    content: "";
    position: absolute;
    right: 23px;
    width: 0;
    height: 0;
}

.mdsGroup {
    background: #fff;
    margin: 8px;
    padding: 12px 0;
    border: 1px solid #ddd;
}

.mdsGroup .mdsWidget {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
}

.mdsGroup .mdsWidget .mdsWidgetCaption {
    color: #aaa;
    font-weight: 500;
}

.edusharing_rendering_metadata_body .mdsGroup{
    width: 94%;
    padding: 12px 0;
    border-radius: 4px;
}

.edusharing_rendering_metadata_body .licenseName {
    margin-top: 8px;
}

.edusharing_rendering_metadata_body .mdsContributorTitle {
    margin: 0 !important;
    margin-top: 25px !important;
}

.mdsGroup:first-child {

}

.edusharing_metadata_inner .mdsGroup .mdsContributorGroup {
    margin-left: 15px;
}

.edusharing_warning {
    display: inline-block;
    background-color: #c6c6c6;
    color: #383838;
    padding: 5px;
    border-radius: 3px;
}

.edusharing_rendering_content_footer {
    width: 100%;
    background: #f6f6f6;
    padding: 6px;
}

.edusharing_rendering_content_footer a, .edusharing_rendering_content_footer a:visited, .edusharing_rendering_content_footer a:focus, .edusharing_rendering_content_footer a:hover {
   color: #4D799A;
}

.edusharing_rendering_content_footer_top {
    overflow-y: hidden;
    overflow-x: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-height: 2rem;
}

.edusharing_rendering_content_footer_bot {
}

.edusharing_rendering_content_footer_metadata {
    width: 30px;
    padding: 10px;
}

.edusharing_rendering_content_footer_sequence {
    position: relative;

}

.edusharing_rendering_content_footer_directory {
    width: 100%;
    background: #f6f6f6;
    padding: 12px;
}

.edusharing_rendering_content_footer_directory_img{
    max-height: 33px;
}

.edusharing_rendering_content_footer_directory_header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.edusharing_rendering_content_footer_directory_header a {
    color: #4D799A;
}

.edusharing_rendering_content_footer_directory_header a:hover {
    text-decoration: none;
}

.edusharing_rendering_content_footer_directory_toggle > * {
    margin-right: 20px;
}

.edusharing_rendering_content_footer_directory_creator {
    display:block;
    color: #999;
    font-size: 90%;
}

.edusharing_rendering_content_footer_sequence .material-icons {
    vertical-align: middle !important;
}

.edusharing_rendering_content_footer_directory .material-icons {
    vertical-align: middle !important;
}

.edusharing_rendering_content_footer_sequence_toggle, .edusharing_rendering_content_footer_directory_toggle {
    color: #4D799A;
    cursor:pointer;
    display: flex;
    align-items: center;
}

.edusharing_rendering_content_footer_sequence_toggle {
    margin-top: 6px;
}

.edusharing_rendering_content_footer_directory_toggle {
    display: flex;
    align-items: center;
    width: 100%;
}

.edusharing_rendering_content_footer_sequence ul {
    padding: 12px;
    position: absolute;
    z-index: 49;
    background: #f6f6f6;
    width: calc(100% + 12px);
    margin-left: -6px;
}

.edusharing_rendering_content_footer_directory ul {
    margin: 10px 0 0 0;
    background: #f6f6f6;
    width: 100%;
    padding: 0 !important;
}

.edusharing_rendering_content_footer_sequence ul li, .edusharing_rendering_content_footer_directory ul li {
    list-style: none;
    height: 60px;
    background: #fff;
    font-size: 14px;
    margin: 3px 0;
    display: flex;
    align-items: center;
    width: 100%;
    border-radius: 3px;
}

.edusharing_rendering_content_footer_sequence ul li a, .edusharing_rendering_content_footer_directory ul li a {
    display: flex;
    align-items: center;
    color: #383838;
    text-decoration: none;
    width: 100%;
}

.edusharing_rendering_content_footer_sequence ul li a:hover, .edusharing_rendering_content_footer_directory ul li a:hover {
    color: #383838;
    text-decoration: none;
}

.edusharing_rendering_content_footer_sequence ul li img, .edusharing_rendering_content_footer_directory ul li img {
    margin: 10px;
}

.edusharing_rendering_content_footer_sequence_showall {
    margin-bottom: 0;
    text-align: right;
    display: inline-block;
    width: 100%;
    margin-top: 8px;
}

.edusharing_rendering_content_footer_top .license_permalink, .edusharing_rendering_content_footer_top .license {
    margin-right: 40px;
}

video, audio {
    margin-bottom: -6px;
    border: 0;
}

.edu_audio_wrapper{
    display: inline-block;
    min-width: 70%;
}

.dataProtectionRegulations, .dataProtectionRegulationsDialog {
background: #fff;
padding: 20px;
-webkit-box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.3);
-moz-box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.3);
box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.3);
text-align: center;
background: #f6f6f6;
margin: auto;
border: 0;
}

.dataProtectionRegulationsHeading {
font-size: 1.8em;
}

a.edusharing_rendering_content {
    margin-top: 20px;
    margin-left: 10px;
    margin-right: 10px;
}

.cdk-visually-hidden {
    border: 0;
    clip: rect(0 0 0 0);
    height: 1px;
    margin: -1px;
    overflow: hidden;
    padding: 0;
    position: absolute;
    width: 1px;
    outline: 0;
    -webkit-appearance: none;
    -moz-appearance: none;
}

