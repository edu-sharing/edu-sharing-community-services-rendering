<?php

require_once __DIR__ . '/CacheCleanerClass.php';

$cleaner = new CacheCleanerClass(isset($argc) && isset($argv[1]) ? $argv[1] : null);
$cleaner->renderPath = $CC_RENDER_PATH;
if (!empty($CC_RENDER_PATH_SAFE)) {
    $cleaner->renderPathSave = $CC_RENDER_PATH_SAFE;
}
$cleaner->cleanUp();
