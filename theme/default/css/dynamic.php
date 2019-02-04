<?php
header("Content-type: text/css");
global $MC_URL;?>

body {
	padding: 0;
	margin: 0;
	height: auto !important;
}

.edusharing_rendering_cursor_pointer {
	cursor: pointer;
}

.edusharing_rendering_wrapper {
	background-color: #666;
	padding: 0;
	margin: 0;
	font-size: 100%;
	min-height: 100%;
}

#edusharing_rendering_metadata {
	background-color: #f6f6f6;
	padding-bottom: 30px;
}
.edusharing_rendering_metadata_header{
	display:flex;
}
img.edusharing_rendering_metadata_header_icon {
	display:flex;
	height: 51px;
	width: 68px;
	margin: 10px -10px -10px 20px;
	cursor: pointer;
	float: left;
	object-fit: contain;
}

h1.edusharing_rendering_metadata_header_title {
	display:flex;
	flex-grow: 1;
	font-size: 130%;
	font-weight: 400;
	margin-top: 1.8rem;
	margin-left: 20px;
	float: left;
	max-width: 100%;
}

.edusharing_rendering_metadata_body {
	clear: both;
	display: flex;
	flex-wrap: wrap;
}

.edusharing_rendering_content_wrapper {
	text-align: center;
	margin: 30px 20px;
}

.edusharing_rendering_content_wrapper h3, .edusharing_rendering_content_wrapper h4 {
	color: #fff;
}

.edusharing_rendering_content_wrapper h3 {
	font-size: 120%;
	font-weight: 600;
	color: #ccc;
}

.edusharing_rendering_content_wrapper h4 {
	font-size: 140%;
}

h1.edusharing_rendering_content_title {
	font-size: 120%;
	font-weight: 600;
	color: #fff;
	text-align: center;
}

.edusharing_rendering_content {
	text-align: center;
}

a.edusharing_rendering_content {
	border-radius: 3px;
	padding: 8px 16px;
	text-decoration: none;
	color: #fff;
	background-color: #4F7A98;
	display: inline-block;
	margin-top: 20px;
	text-transform: uppercase;
	font-weight: 600;
}

img.edusharing_rendering_content {
	background-image: url('<?php echo $MC_URL?>/theme/default/img/background-transparent.png');
}

.edusharing_rendering_content, video, #edusharing_htmlobject, .edusharing_rendering_content_preview {
	-webkit-box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.3);
	-moz-box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.3);
	box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.3);
	max-width: 100%;
	max-height: calc(100vh - 170px);
}

.edusharing_rendering_content.edusharing_rendering_content_embedded {
	height: calc(100vh - 170px);
	-webkit-overflow-scrolling: touch;
}

img.edusharing_rendering_content_preview {
	display: block;
	margin: 20px auto;
	max-width: 400px;
	max-height: 400px;
}

.videoWrapperOuter {
	margin: 0 auto;
}

.es_progressbar_container {
	border: 5px solid #555 !important;
	width: 100%;
	text-align: left !important;
	box-sizing: content-box;
	margin: 0 auto;
}

.es_progressbar_progress {
	border-right: 1px solid #fff;
}

.renderservice_message {
	color: #fff;
	font-size: 120%;
}

#edusharing_htmlobject, .edusharing_etherpad {
	display: block;
	min-height: 400px;
	width: 100%;
	background: #fff;
	max-width: 1024px;
	margin: 0 auto;
	padding: 20px 40px;
	text-align: left;
	overflow: auto;
	max-height: calc(100vh - 165px);
}

.edusharing_etherpad {
	border: 0;
	padding: 0;
}

.dataProtectionRegulations {
	background: #fff;
	padding: 20px;
	border-radius: 5px;
	-webkit-box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.3);
	-moz-box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.3);
	box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.3);
	max-width: 800px;
	margin: auto;
}

.dataProtectionRegulationsHeading {
	font-size: 1.8em;
}

a.dataProtectionRegulationsButton {
	line-height: initial;
}

div.h5p-container {
    margin-bottom: 30px;
}

@media (max-width: 900px) {

	.edusharing_rendering_content_wrapper h3 {
		font-size: 90%;
		margin: 0.5rem 0rem;
	}

	.edusharing_rendering_content_wrapper h4 {
		font-size: 110%;
		margin: 0.5rem 0rem;
	}

	img.edusharing_rendering_content_preview {
		margin: 10px auto;
	}
	h1.edusharing_rendering_metadata_header_title {
		margin: 1.8rem 0 2rem 20px;
	}
	#edusharing_htmlobject, .edusharing_etherpad {
		max-width: 100%;
	}

}


/*to override inline style (brought by video dynamic)*/
.edusharing_rendering_metadata_body .mdsGroup {
	padding: 0 20px !important;
}
.edusharing_rendering_metadata_body .mdsGroup:first-child {
	margin-top: 0 !important;
}