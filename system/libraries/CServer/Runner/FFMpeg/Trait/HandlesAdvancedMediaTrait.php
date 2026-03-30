<?php

defined('SYSPATH') or die('No direct access allowed.');

use FFMpeg\Format\FormatInterface;
use ProtoneMedia\LaravelFFMpeg\Filesystem\Media;
use ProtoneMedia\LaravelFFMpeg\FFMpeg\AdvancedOutputMapping;

trait CRunner_FFMpeg_Trait_HandlesAdvancedMediaTrait {
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $maps;

    public function addFormatOutputMapping(FormatInterface $format, Media $output, array $outs, $forceDisableAudio = false, $forceDisableVideo = false) {
        $this->maps->push(
            new CRunner_FFMpeg_AdvancedOutputMapping($outs, $format, $output, $forceDisableAudio, $forceDisableVideo)
        );

        return $this;
    }
}
