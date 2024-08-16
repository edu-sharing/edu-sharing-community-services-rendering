<?php
require_once(__DIR__ . '/../../conf.inc.php');
session_id($_GET["PHPSESSID"]);
session_start();
$data       = $_SESSION["mod_audio"][$_GET["ID"]];
$ajaxUrl    = $data["ajax_url"];
$authString = $data["authString"];
header('Content-Type: text/javascript');

$script = <<<JS
    function get_resource(authString) {
        const url = "$ajaxUrl" + "&callback=get_resource&" + authString
        fetch(url).then(response => response.text()).then(result => {
            const contentContainer = document.getElementById("edusharing_rendering_content")
            contentContainer.innerHTML = result
            const isLockScreen = document.querySelector('[data-view="lock"]') !== null
            if (isLockScreen) {
                const lockData = contentContainer.querySelector("data")
                const decodedLockData = JSON.parse(lockData.value)
                setTimeout(() => get_resource(decodedLockData.authString), 2000)
                return
            }
            const dataTags = document.querySelectorAll(".edu_audio_data")
            const timeStamps = window.location.hash.substring(1)
            for (let dataTag of dataTags) {
                const data = dataTag.getAttribute("value")
                const decoded = JSON.parse(data)
                const wrapper = dataTag.parentNode
                const video = document.createElement("video")
                video.poster = decoded.preview
                video.style.objectFit = "cover"
                video.style.maxWidth = "100%"
                video.style.boxShadow = "none"
                video.src = decoded.resource + (timeStamps === "" ? "" : ("#" + timeStamps))
                video.type = "audio/mp3"
                video.controls = true
                video.setAttribute("controlsList", "nodownload")
                video.oncontextmenu = () => {}
                wrapper.append(video)
                if (wrapper.classList.contains("edu_wrapper")) {
                    const licenseElements = wrapper.querySelectorAll(".license")
                    for (let licenseElement of licenseElements) {
                        licenseElement.style.width = video.clientWidth + "px"
                        licenseElement.style.height = "auto"
                    }
                }
                dataTag.remove()
            }
        })
    }
    
    function replaceDownloadUrl() {
        let downloadUrlData = document.getElementById("audioDownloadUrl")
        if (downloadUrlData !== null) {
            let url = downloadUrlData.value
            try {
                window.ngRender.setDownloadUrl(url);
            } catch(err) {}
            downloadUrlData.remove()
        }
    }
    
    replaceDownloadUrl()
    get_resource("$authString");
JS;

echo $script;
