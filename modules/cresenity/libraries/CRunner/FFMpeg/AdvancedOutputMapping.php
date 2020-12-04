<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Aug 26, 2020 
 * @license Ittron Global Teknologi
 */
use FFMpeg\Format\FormatInterface;
use FFMpeg\Format\Video\DefaultVideo;
use FFMpeg\Media\AdvancedMedia;

class CRunner_FFMpeg_AdvancedOutputMapping {

    /**
     * @var array
     */
    private $outs;

    /**
     * @var \FFMpeg\Format\FormatInterface
     */
    private $format;

    /**
     * @var \ProtoneMedia\LaravelFFMpeg\Filesystem\Media
     */
    private $output;

    /**
     * @var boolean
     */
    private $forceDisableAudio = false;

    /**
     * @var boolean
     */
    private $forceDisableVideo = false;

    public function __construct(array $outs, FormatInterface $format, CRunner_FFMpeg_Media $output, $forceDisableAudio = false, $forceDisableVideo = false) {
        $this->outs = $outs;
        $this->format = $format;
        $this->output = $output;
        $this->forceDisableAudio = $forceDisableAudio;
        $this->forceDisableVideo = $forceDisableVideo;
    }

    /**
     * Applies the attributes to the format and specifies the video
     * bitrate if it's missing.
     */
    public function apply(AdvancedMedia $advancedMedia) {
        if ($this->format instanceof DefaultVideo) {
            $parameters = $this->format->getAdditionalParameters() ?: [];

            if (!in_array('-b:v', $parameters)) {
                $parameters = ['-b:v', $this->format->getKiloBitrate() . 'k'] + $parameters;
            }

            $this->format->setAdditionalParameters($parameters);
        }

        $advancedMedia->map($this->outs, $this->format, $this->output->getLocalPath(), $this->forceDisableAudio, $this->forceDisableVideo);
    }

    public function getFormat() {
        return $this->format;
    }

    public function getOutputMedia() {
        return $this->output;
    }

    public function hasOut($out) {
        return in_array($out, $this->outs);
    }

}
