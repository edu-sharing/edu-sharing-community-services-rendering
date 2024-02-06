<?php

class ESRender_Module_AudioVideo_Helper {

    public static function getConversionProgress($toolkitOutput) {
        $log = @file_get_contents($toolkitOutput);
        if ($log) {

            //get duration of source
            preg_match("/Duration: (.*?), start:/", $log, $matches);
            $rawDuration = $matches[1];

            //rawDuration is in 00:00:00.00 format. This converts it to seconds.
            $ar = array_reverse(explode(":", $rawDuration));
            $duration = floatval($ar[0]);
            if (!empty($ar[1]))
                $duration += intval($ar[1]) * 60;
            if (!empty($ar[2]))
                $duration += intval($ar[2]) * 60 * 60;

            //get the time in the file that is already encoded
            preg_match_all("/time=(.*?) bitrate/", $log, $matches);

            $rawTime = array_pop($matches);

            //this is needed if there is more than one match
            if (is_array($rawTime)) {
                $rawTime = array_pop($rawTime);
            }

            //rawTime is in 00:00:00.00 format. This converts it to seconds.
            $ar = array_reverse(explode(":", $rawTime));
            $time = floatval($ar[0]);
            if (!empty($ar[1]))
                $time += intval($ar[1]) * 60;
            if (!empty($ar[2]))
                $time += intval($ar[2]) * 60 * 60;

            //calculate the progress
            if (empty($time) || empty($duration))
                return;

            $progress = round(($time / $duration) * 100);
            //should not happen, just ensures not to show nonsense to the user
            if($progress > 100)
                $progress = 100;

            return $progress;
        }
    }

    public static function checkResolution($source_video, $conversion_resolution): bool {
        $getVideoData = FFMPEG_BINARY . ' -i ' . $source_video . ' -vstats 2>&1';
        $output = shell_exec($getVideoData);
        $regex_sizes = "/Video: ([^\r\n]*), ([^,]*), ([0-9]{1,4})x([0-9]{1,4})/";
        if (preg_match($regex_sizes, $output, $regs)) {
            $source_height = $regs [4] ?: null;
            if ($source_height >= $conversion_resolution){
                return true;
            }else {
                foreach (VIDEO_RESOLUTIONS as $resolution){
                    if ($resolution < $source_height){
                        continue;
                    }
                    return $resolution == $conversion_resolution;
                }
                return false;
            }
        }
        return false;
    }
}
