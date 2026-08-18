<?php

defined('SYSPATH') or die('No direct access allowed.');

use FFMpeg\Format\FormatInterface;

trait CRunner_FFMpeg_Trait_HandlesAdvancedMediaTrait {
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $maps;

    public function addFormatOutputMapping(FormatInterface $format, CRunner_FFMpeg_Media $output, array $outs, $forceDisableAudio = false, $forceDisableVideo = false) {
        $this->maps->push(
            new CRunner_FFMpeg_AdvancedOutputMapping($outs, $format, $output, $forceDisableAudio, $forceDisableVideo)
        );

        return $this;
    }
}
