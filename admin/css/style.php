<?php
header("Content-type: text/css");
global $MC_URL?>

html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td, article, aside, canvas, details, embed, figure, figcaption, footer, header, hgroup, menu, nav, output, ruby, section, summary, time, mark, audio, video {
	font-size: 100%;
}


@font-face {
	font-family: 'Open Sans';
	font-style: normal;
	font-weight: 400;
	src: url('<?php echo $MC_URL?>/vendor/fonts/opensans/open-sans-v15-latin-regular.eot'); /* IE9 Compat Modes */
	src: local('Open Sans Regular'), local('OpenSans-Regular'),
	url('<?php echo $MC_URL?>/vendor/fonts/opensans/open-sans-v15-latin-regular.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
	url('<?php echo $MC_URL?>/vendor/fonts/opensans/open-sans-v15-latin-regular.woff2') format('woff2'), /* Super Modern Browsers */
	url('<?php echo $MC_URL?>/vendor/fonts/opensans/open-sans-v15-latin-regular.woff') format('woff'), /* Modern Browsers */
	url('<?php echo $MC_URL?>/vendor/fonts/opensans/open-sans-v15-latin-regular.ttf') format('truetype'), /* Safari, Android, iOS */
	url('<?php echo $MC_URL?>/vendor/fonts/opensans/open-sans-v15-latin-regular.svg#OpenSans') format('svg'); /* Legacy iOS */
}
/* open-sans-600 - latin */
@font-face {
	font-family: 'Open Sans';
	font-style: normal;
	font-weight: 600;
	src: url('<?php echo $MC_URL?>/vendor/fonts/opensans/open-sans-v15-latin-600.eot'); /* IE9 Compat Modes */
	src: local('Open Sans SemiBold'), local('OpenSans-SemiBold'),
	url('<?php echo $MC_URL?>/vendor/fonts/opensans/open-sans-v15-latin-600.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
	url('<?php echo $MC_URL?>/vendor/fonts/opensans/open-sans-v15-latin-600.woff2') format('woff2'), /* Super Modern Browsers */
	url('<?php echo $MC_URL?>/vendor/fonts/opensans/open-sans-v15-latin-600.woff') format('woff'), /* Modern Browsers */
	url('<?php echo $MC_URL?>/vendor/fonts/opensans/open-sans-v15-latin-600.ttf') format('truetype'), /* Safari, Android, iOS */
	url('<?php echo $MC_URL?>/vendor/fonts/opensans/open-sans-v15-latin-600.svg#OpenSans') format('svg'); /* Legacy iOS */
}
body {
	padding: 0;
    margin: 0;
	font-family: 'Open Sans', sans-serif;
	background-color: #e4f3f9;
}

h1, h2 {
	font-size: 1.5em;
    fot-weight: bold;
	margin-bottom: 40px;
}

button::-moz-focus-inner {
	border: 0;
}

a:focus {
	outline: none;
}

td {
	padding: 2px 15px 2px 5px;
}

table button, .import_button, .updatebackbutton, .updateButton {
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #4288a2), color-stop(1, #317f95));
	background: -moz-linear-gradient(top, #4288a2 5%, #317f95 100%);
	background: -webkit-linear-gradient(top, #4288a2 5%, #317f95 100%);
	background: -o-linear-gradient(top, #4288a2 5%, #317f95 100%);
	background: -ms-linear-gradient(top, #4288a2 5%, #317f95 100%);
	background: linear-gradient(to bottom, #4288a2 5%, #317f95 100%);
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#4288a2', endColorstr='#317f95',GradientType=0);
	background-color: #4288a2;
	-moz-border-radius: 2px;
	-webkit-border-radius: 2px;
	border-radius: 2px;
	display: inline-block;
	cursor: pointer;
	color: #ffffff;
	font-family: arial;
	font-size: 1.3em;
	padding: 5px 18px;
	text-decoration: none;
	border: none;
	text-shadow: 1px 1px 1px #666;
}
table button:hover, .import_button:hover, .updatebackbutton:hover, .updateButton:hover {
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #317f95), color-stop(1, #4288a2));
	background: -moz-linear-gradient(top, #317f95 5%, #4288a2 100%);
	background: -webkit-linear-gradient(top, #317f95 5%, #4288a2 100%);
	background: -o-linear-gradient(top, #317f95 5%, #4288a2 100%);
	background: -ms-linear-gradient(top, #317f95 5%, #4288a2 100%);
	background: linear-gradient(to bottom, #317f95 5%, #4288a2 100%);
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#317f95', endColorstr='#4288a2',GradientType=0);
	background-color: #317f95;
}

body.execute {
	margin-bottom: 20px;
}

div {
	padding: 0;
}

div button {
	padding: 5px;
	font-weight: bold;
}

table.update_info {
	margin: 10px 0px;
}

h2 {
	text-align: left;
	margin-top: 10px;
}

h5 {
	margin: 10px 0px;
}

li {
	color: #E00000;
	margin: 0px;
	padding: 0px;
}

.replace {
	color: #804040;
}

span.ouch_info {
	color: #FF0000;
	font-weight: bold;
}

div.fileblock, div.sqlblock, div.mpsblock {
	background-color: #E0E0E0;
	padding: 5px 5px 10px;
	margin: 5px 0px;
	border: 0px dotted darkgray;
	border-top: 1px;
}

div.fileblock .action, div.sqlblock .action, div.mpsblock .action {
	color: #888888;
}

div.ouch {
	line-height: 20px;
}

a.link, a.link:link, a.link:hover, a.link:visited, a.link:active {
	color: #CE6F21;
	text-decoration: none;
}

a.toggle_block {
	color: #555555;
}

a.link:hover, a.toggle_block:hover {
	text-decoration: underline;
}

div.user_message {
	margin: 4px 0px;
	background-repeat: no-repeat;
	background-position: 3px center;
	font-family: arial, helvetica;
	padding: 10px 10px 10px 20px;
	width: 760px;
}

div.user_info {
	border: 1px solid #d6e9c6;
	color: #3c763d;
	-moz-border-radius: 2px;
	-webkit-border-radius: 2px;
	border-radius: 2px;
	background: #dff0d8;
}

div.user_warning {
	border: 1px solid #faebcc;
	color: #8a6d3b;
	-moz-border-radius: 2px;
	-webkit-border-radius: 2px;
	border-radius: 2px;
	background: #fcf8e3;
}

div.user_error {
	border: 1px solid #ebccd1;
	color: #a94442;
	-moz-border-radius: 2px;
	-webkit-border-radius: 2px;
	border-radius: 2px;
	background: #f2dede;
}

div.hit {
	color: #702030;
}

div.user_message.action {
}

div.big_info, div.big_warning, div.big_error {
	border-width: 1px;
	border-style: solid;
}

div.big_info TD, div.big_warning TD, div.big_error TD {
	padding: 2px 8px 2px 20px;
	color: #444444;
}

div.big_info {
	border: 1px solid #d6e9c6;
	color: #3c763d;
	-moz-border-radius: 2px;
	-webkit-border-radius: 2px;
	border-radius: 2px;
	background: #dff0d8;
}

div.big_warning {
	border: 1px solid #faebcc;
	color: #8a6d3b;
	-moz-border-radius: 2px;
	-webkit-border-radius: 2px;
	border-radius: 2px;
	background: #fcf8e3;
}

div.big_error {
	border: 1px solid #ebccd1;
	color: #a94442;
	-moz-border-radius: 2px;
	-webkit-border-radius: 2px;
	border-radius: 2px;
	background: #f2dede;
}

input, select {
	padding: 4px 6px;
	-moz-border-radius: 2px;
	-webkit-border-radius: 2px;
	border-radius: 2px;
	border: 1px solid #9ccce1;
	font-size: 1em;
}

#terms {
	display: inline-block;
	font-family: 'Courier New', Arial;
	margin: 20px 0;
	padding: 10px;
	white-space: -moz-pre-wrap !important;
	white-space: -pre-wrap;
	white-space: -o-pre-wrap;
	white-space: pre-wrap;
	background-color: #fff;
	width: 760px;
	-moz-border-radius: 2px;
	-webkit-border-radius: 2px;
	border-radius: 2px;
	font-size: 0.9em;
}

.header{
    width: 100%;
    display: flex;
    align-content: baseline;
    color: #fff;
    background: #383838;
    background-image: url('../img/edulogo-white-text.svg');
    background-position: right 20px top 20px;
    background-size: 120px auto;
    background-repeat: no-repeat;
    margin-bottom: 24px;
    border-bottom: 6px solid #fff;
    box-shadow: 0 1px 4px rgba(0, 0, 0, .1);
}

.header h1{
    padding-left: 40px;
}

.wrap{
    padding: 10px 40px;
}

.login {

}

.login label, .login input {
	display: block;
	width: 200px;
}

.login input[type=text], .login input[type=password] {
	padding: 4px;
	border: none;
}

.login label {
	margin-top: 10px;
}

.login input[type=submit] {
	margin-top: 20px;
}

#version_running {
	color: #555;
}

.rs-admin{

}

.file-upload{
    padding: 8px;
    padding-left: 0;
    margin-top: 24px;
}

.file-upload h3{
    font-size: 1.2em;
    margin: 0;
}

.choose-core{
    padding: 8px;
    background: white;

}

.btn {
    display: inline-block;
    margin: 12px 4px;
    padding: 12px 24px;
    overflow: hidden;
    border-width: 0;
    outline: none;
    font-weight: bold;
    text-decoration: none;
    cursor: pointer;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .4);
    background-color: #48708e;
    color: #ecf0f1;
    transition: ease-in-out 0.2s;
}

.btn:hover, .btn:focus {
    background-color: #183f5c;
transform:scale(1.02);
}
