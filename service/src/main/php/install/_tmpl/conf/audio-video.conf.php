<?php

const AUDIO_FORMATS = array('mp3');

const VIDEO_FORMATS = array('mp4', 'webm'); // select mp4 & webm OR only mp4
const VIDEO_RESOLUTIONS = array('240', '720', '1080');
const VIDEO_DEFAULT_RESOLUTION = '720';  // chrome/webkit will choose resolution by connection-speed

define('FFMPEG_BINARY', '[[[TOKEN_FFMPEG_EXEC]]]');
define('FFMPEG_EXEC_TIMEOUT', 3600);
define('FFMPEG_THREADS', 1);
