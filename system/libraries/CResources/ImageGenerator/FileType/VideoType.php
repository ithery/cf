<?php
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Media\Video as FFMpegVideo;

class CResources_ImageGenerator_FileType_VideoType extends CResources_ImageGenerator_FileTypeAbstract {
    /**
     * @param string                $file
     * @param CResources_Conversion $conversion
     *
     * @return string
     */
    public function convert($file, CResources_Conversion $conversion = null) {
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => CF::config('resource.ffmpeg_path'),
            'ffprobe.binaries' => CF::config('resource.ffprobe_path'),
        ]);

        $video = $ffmpeg->open($file);

        if (!($video instanceof FFMpegVideo)) {
            return null;
        }

        $duration = $ffmpeg->getFFProbe()->format($file)->get('duration');

        $seconds = $conversion ? $conversion->getExtractVideoFrameAtSecond() : 0;
        $seconds = $duration <= $seconds ? 0 : $seconds;

        $imageFile = pathinfo($file, PATHINFO_DIRNAME) . '/' . pathinfo($file, PATHINFO_FILENAME) . '.jpg';

        $frame = $video->frame(TimeCode::fromSeconds($seconds));
        $frame->save($imageFile);

        return $imageFile;
    }

    public function requirementsAreInstalled(): bool {
        return class_exists('\\FFMpeg\\FFMpeg');
    }

    public function supportedExtensions(): CCollection {
        return c::collect(['webm', 'mov', 'mp4']);
    }

    public function supportedMimeTypes(): CCollection {
        return c::collect(['video/webm', 'video/mpeg', 'video/mp4', 'video/quicktime']);
    }
}
