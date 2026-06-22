<?php
require_once(__DIR__ . '/../../conf.inc.php');
session_id($_GET["PHPSESSID"]);
session_start();
$data       = $_SESSION["mod_video"][$_GET["ID"]];
$ajaxUrl    = $data["ajax_url"];
$authString = $data["authString"];
header('Content-Type: text/javascript');

$id = $_GET['ID'];
$script = <<<JS
    var videoFormat = 'webm';
    var probeVideo = document.createElement('video');
    if (probeVideo.canPlayType && probeVideo.canPlayType('video/mp4').replace(/no/, '')) {
        videoFormat = 'mp4';
    }

    function get_resource(authString) {
        const url = "$ajaxUrl" + "&callback=get_resource&" + authString + "&videoFormat=" + videoFormat;
        fetch(url).then(response => response.text()).then(result => {
            const id = "edusharing_rendering_content_{$id}";
            let contentContainer = document.getElementById(id);
            if (!contentContainer) {
                const allElements = document.querySelectorAll('*');
                Array.from(allElements).filter(el => el.shadowRoot).forEach((shadow) => {
                    const el = shadow.shadowRoot.getElementById(id);
                    if (el) {
                        contentContainer = el;
                    }
                });
            }
            if (!contentContainer) {
                return;
            }

            contentContainer.innerHTML = result;

            const isLockScreen = contentContainer.querySelector('[data-view="lock"]') !== null;
            if (isLockScreen) {
                const lockData = contentContainer.querySelector("data");
                const decodedLockData = JSON.parse(lockData.value);
                setTimeout(() => get_resource(decodedLockData.authString), 2000);
                return;
            }

            initVideoPlayer(contentContainer);
        });
    }

    function initVideoPlayer(container) {
        const dataTag = container.querySelector('.edu_video_data');
        if (!dataTag) {
            return;
        }
        let config;
        try {
            config = JSON.parse(dataTag.getAttribute('value'));
        } catch (e) {
            return;
        }
        dataTag.remove();

        const videoId = config.id;
        const resolutions = config.resolutions || [];

        const video = container.querySelector('#' + videoId);
        if (!video) {
            return;
        }

        video.oncontextmenu = () => false;

        const optionsButton = container.querySelector('#edusharing_rendering_content_video_options_button' + videoId);
        const optionsContainer = container.querySelector('#edusharing_rendering_content_video_options_container_' + videoId);
        const options = container.querySelectorAll('.option_' + videoId);

        let removeOutsideClickHandler = null;

        function setChecked(element) {
            const list = element.parentNode;
            const selected = list.querySelector('[aria-checked="true"]');
            if (selected) {
                const icon = selected.querySelector('i.material-icons');
                if (icon) {
                    icon.remove();
                }
                selected.setAttribute('aria-checked', 'false');
            }
            const newIcon = document.createElement('i');
            newIcon.className = 'material-icons';
            newIcon.setAttribute('aria-hidden', 'true');
            newIcon.textContent = 'done';
            element.insertBefore(newIcon, element.firstChild);
            element.setAttribute('aria-checked', 'true');
        }

        function closeMenu() {
            if (optionsButton) {
                optionsButton.removeAttribute('aria-expanded');
                optionsButton.removeAttribute('aria-controls');
            }
            if (optionsContainer) {
                optionsContainer.classList.remove('edusharing_rendering_content_video_options_container_expanded');
            }
            if (removeOutsideClickHandler) {
                removeOutsideClickHandler();
            }
        }

        function onOptionClick(element) {
            const url = element.getAttribute('data-url');
            if (url == undefined) {
                return;
            }
            setChecked(element);
            video.pause();
            video.setAttribute('src', url + '#t=' + Math.ceil(video.currentTime)); /* ie not working */
            video.load();
            video.play();
            closeMenu();
            setTimeout(() => { /* Don't trigger another button click */
                if (optionsButton) {
                    optionsButton.focus();
                }
            });
        }

        const timeStamps = window.location.hash.substring(1);
        if (timeStamps !== "") {
            video.src = video.src + '#' + timeStamps;
        }

        options.forEach((option) => {
            option.addEventListener('click', () => onOptionClick(option));
            option.addEventListener('keydown', (event) => {
                switch (event.code) {
                    case 'Enter':
                        onOptionClick(event.target);
                        event.preventDefault();
                        break;
                    case 'ArrowUp':
                        if (event.target.previousElementSibling) {
                            event.target.previousElementSibling.focus();
                        }
                        event.preventDefault();
                        break;
                    case 'ArrowDown':
                        if (event.target.nextElementSibling) {
                            event.target.nextElementSibling.focus();
                        }
                        event.preventDefault();
                        break;
                    case 'Escape':
                        closeMenu();
                        if (optionsButton) {
                            optionsButton.focus();
                        }
                        event.preventDefault();
                        break;
                    case 'Tab':
                        closeMenu();
                        if (optionsButton) {
                            optionsButton.focus();
                        }
                        /* Don't call preventDefault, so the tab action is executed as if the menu
                         button had the focus at the time of the key press and the menu was collapsed. */
                        break;
                }
            });
        });

        if (optionsButton) {
            optionsButton.addEventListener('click', () => {
                if (!optionsContainer) {
                    return;
                }
                const content = optionsContainer.querySelector('.edusharing_rendering_content_video_options_content');
                if (content) {
                    const hidden = content.style.display === 'none' || content.style.display === '';
                    content.style.display = hidden ? 'block' : 'none';
                }
            });
        }

        function setResByConnection() {
            /* navigator.connection not yet supported by all browsers, so check it first */
            if (!navigator.connection) {
                return;
            }

            const downLink = navigator.connection.downlink;

            let resolution = resolutions[0];
            if (downLink >= 5) {
                resolution = resolutions[resolutions.length - 1];
            } else if (downLink >= 2) {
                /* VIDEO_DEFAULT_RESOLUTION is already set, so we can skip the rest */
                return;
            }

            const option = container.querySelector('#option_' + videoId + '-' + resolution);
            if (!option) {
                return;
            }
            const url = option.getAttribute('data-url');
            if (url != undefined) {
                setChecked(option);
                video.pause();
                video.setAttribute('src', url + '#t=' + Math.ceil(video.currentTime)); /* ie not working */
                video.load();
            }
        }

        setResByConnection();

        video.addEventListener('error', function(e) {
            switch (e.target.error.code) {
                /* MEDIA_ERR_ABORTED is intentionally not handled: switching
                   resolution aborts the previous load and would otherwise
                   surface the "cannot play back" message on a working video. */
                case e.target.error.MEDIA_ERR_NETWORK:
                case e.target.error.MEDIA_ERR_DECODE:
                case e.target.error.MEDIA_ERR_SRC_NOT_SUPPORTED:
                    video.style.display = 'none';
                    const errorEl = container.querySelector('#e_' + videoId);
                    if (errorEl) {
                        errorEl.style.display = 'block';
                    }
                    break;
                default:
                    return;
            }
        }, true);
    }

    get_resource("$authString");
JS;

echo $script;