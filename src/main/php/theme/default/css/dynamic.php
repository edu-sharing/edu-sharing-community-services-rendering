/*
<?php
header("Content-type: text/css");
global $MC_URL;?>
*/

body {
  padding: 0;
  margin: 0;
  height: auto !important;
}

.edusharing_rendering_cursor_pointer {
  cursor: pointer;
}

.edusharing_rendering_wrapper {
  background-color: #ebebeb;
  padding: 0;
  margin: 0;
  font-size: 100%;
  min-height: 100%;
  padding-bottom: 1px;
}

.edusharing_rendering_content_video_options_container {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  position: absolute;
  top: 2%;
  right: 2%;
  color: #fff;
  margin: 0;
  text-align: right;
}

.edusharing_rendering_content_video_options_container_expanded {
  background: rgba(0, 0, 0, 0.6);
}

.edusharing_rendering_content_video_options {
  display: flex;
  background: none;
  border: none;
  padding: 5px;
}

.edusharing_rendering_content_video_options:focus-visible {
  outline: 2px solid white;
  border-radius: 2px;
}

.edusharing_rendering_content_video_options i {
  opacity: 0.8;
}

.edusharing_rendering_content_video_options_container_expanded
  .edusharing_rendering_content_video_options
  i,
.edusharing_rendering_content_video_options:hover i,
.edusharing_rendering_content_video_options:focus-visible i {
  opacity: 1;
}

div.edusharing_rendering_content_video_options_content {
  display: none;
}

.edusharing_rendering_content_video_options_container_expanded
  .edusharing_rendering_content_video_options_content {
  display: block;
}

div.edusharing_rendering_content_video_wrapper {
  display: inline-block;
  position: relative;
}

div.edusharing_rendering_content_video_wrapper:hover
  div.edusharing_rendering_content_video_options {
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
  padding: 4px 10px;
  list-style: none;
}

ul.edusharing_rendering_content_video_options_resolutions > li:hover,
ul.edusharing_rendering_content_video_options_resolutions > li:focus-visible {
  background-color: rgba(255, 255, 255, 0.2);
}

ul.edusharing_rendering_content_video_options_resolutions > li:focus-visible {
  outline: 2px solid white;
  border-radius: 2px;
}

ul.edusharing_rendering_content_video_options_resolutions
  > li.edusharing_rendering_content_video_options_resolutions_converting {
  color: #999999;
}

.material-icons {
  font-family: 'Material Icons';
  font-weight: normal;
  font-style: normal;
  font-size: 24px; /* Preferred icon size */
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

#edusharing_rendering_metadata {
  background-color: #fff;
  padding-bottom: 30px;
}
.edusharing_rendering_metadata_header {
  display: flex;
}
img.edusharing_rendering_metadata_header_icon {
  display: flex;
  height: 51px;
  width: 68px;
  margin: 10px -10px -10px 20px;
  cursor: pointer;
  float: left;
  object-fit: contain;
}

h1.edusharing_rendering_metadata_header_title {
  display: flex;
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

.edusharing_rendering_metadata_body .mdsContent {
  overflow: hidden;
}

.edusharing_rendering_wrapper .error-msg{
    background-color: lightcoral;
    border-radius: 16px;
    padding: 5px 10px;
    width: fit-content;
    margin: 12px auto;
}

.edusharing_rendering_content_wrapper {
  text-align: center;
  margin: 30px 20px;
}

.edusharing_rendering_content_wrapper h3,
.edusharing_rendering_content_wrapper h4 {
  color: #585858;
}

.edusharing_rendering_content_wrapper h3 {
  font-size: 120%;
  font-weight: 600;
  color: #585858;
}

.edusharing_rendering_content_wrapper h4 {
  font-size: 140%;
}

.edusharing_rendering_fullscreen_wrapper{
    position: relative;
    width: fit-content;
    margin: 0 auto;
}

.edusharing_rendering_fullscreen_wrapper:fullscreen .edusharing_rendering_content{
    height: 100vh;
    max-height: 100vh;
}

#edusharing_toggle_fullscreen{
    position: absolute;
    top: 0.25em;
    right: 0.25em;
    color: white;
    font-size: 34px;
    cursor: pointer;
    opacity: 0.8;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
    transition: 0.3s;
}

#edusharing_toggle_fullscreen:hover{
    opacity: 1;
}

.edusharing_rendering_fullscreen_wrapper{
    position: relative;
    width: fit-content;
    margin: 0 auto;
}

.edusharing_rendering_fullscreen_wrapper:fullscreen .edusharing_rendering_content{
    height: 100vh;
    max-height: 100vh;
}

#edusharing_toggle_fullscreen{
    position: absolute;
    top: 0.25em;
    right: 0.25em;
    color: white;
    font-size: 34px;
    cursor: pointer;
    opacity: 0.8;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
    transition: 0.3s;
}

#edusharing_toggle_fullscreen:hover{
    opacity: 1;
}

h1.edusharing_rendering_content_title {
  font-size: 120%;
  font-weight: 600;
  color: #585858;
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
  background-color: #4f7a98;
  display: inline-block;
  margin-top: 20px;
  font-weight: 600;
}

img.edusharing_rendering_content {
  background-image: url('<?php echo $MC_URL?>/theme/default/img/background-transparent.png');
}

.edusharing_rendering_content,
video,
#edusharing_htmlobject,
.edusharing_rendering_content_preview {
  -webkit-box-shadow: 0px 2px 3px 0px rgba(0, 0, 0, 0.3);
  -moz-box-shadow: 0px 2px 3px 0px rgba(0, 0, 0, 0.3);
  box-shadow: 0px 2px 3px 0px rgba(0, 0, 0, 0.3);
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
  background-color: #fff;
  border: 1px solid #6f6f6f !important;
  border-radius: 12px;
  width: 100%;
  text-align: left !important;
  box-sizing: content-box;
  margin: 0 auto;
}

.es_progressbar_progress {
  background-color: #48708e;
  border-radius: 12px;
}

.renderservice_message {
  color: #585858;
  font-size: 120%;
}

#edusharing_htmlobject,
.edusharing_etherpad {
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
  position: relative;
  background: #fff;
  padding: 20px;
  border-radius: 5px;
  -webkit-box-shadow: 0px 2px 3px 0px rgba(0, 0, 0, 0.3);
  -moz-box-shadow: 0px 2px 3px 0px rgba(0, 0, 0, 0.3);
  box-shadow: 0px 2px 3px 0px rgba(0, 0, 0, 0.3);
  max-width: 800px;
  height: 450px;
  margin: auto;
}
.dataProtectionRegulations .dataProtectionRegulationsContainer {
  position: absolute;
  width: 100%;
  z-index: 1;
  color: #fff;
  text-shadow: 0 0 5px rgba(0, 0, 0, 0.5);
  padding: 15px 20px;
  backdrop-filter: blur(4px);
  background-color: rgba(0, 0, 0, 0.6);
  left: 0;
  bottom: 0;
}
.dataProtectionRegulations .dataProtectionRegulationsHeading,
.dataProtectionRegulations .dataProtectionRegulationsContainer > * {
    display: block;
    text-align: center;
}
.dataProtectionRegulations .dataProtectionRegulationsContainer a:not(.btn) {
  color: #b6d8fc;
  text-decoration: underline;
}
.dataProtectionRegulations .dataProtectionVideoBg {
  position: absolute;
  left: 0;
  top: 0;
  object-fit: cover;
  width: 100%;
  height: 100%;
}

.dataProtectionRegulationsHeading {
  font-size: 1.6em;
}

a.dataProtectionRegulationsButton {
  line-height: initial;
}

@media (max-width: 900px) {
  .edusharing_rendering_content_wrapper {
    margin: 5px;
  }

  .edusharing_rendering_content_title {
    margin: 1rem 0 1rem 0;
  }

  a.edusharing_rendering_content {
    margin: 1rem 0 1rem 0;
  }

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
  #edusharing_htmlobject,
  .edusharing_etherpad,
  img.edusharing_rendering_content_preview {
    max-width: 100%;
  }
}

/*to override inline style (brought by video dynamic)*/
.edusharing_rendering_metadata_body .mdsGroup {
  padding: 0 20px 20px 20px !important;
}
.edusharing_rendering_metadata_body .mdsGroup:first-child {
  margin-top: 0 !important;
}

.dataProtectionRegulations,
.dataProtectionRegulationsDialog {
  border-radius: 5px;
}
