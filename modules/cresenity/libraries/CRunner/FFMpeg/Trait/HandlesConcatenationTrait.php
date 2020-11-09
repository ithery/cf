<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Aug 26, 2020 
 * @license Ittron Global Teknologi
 */
trait CRunner_FFMpeg_Trait_HandlesConcatenationTrait {

    /**
     * @var boolean
     */
    protected $concatWithTranscoding = false;

    /**
     * @var boolean
     */
    protected $concatWithVideo = false;

    /**
     * @var boolean
     */
    protected $concatWithAudio = false;

    public function concatWithTranscoding($hasVideo = true, $hasAudio = true) {
        $this->concatWithTranscoding = true;
        $this->concatWithVideo = $hasVideo;
        $this->concatWithAudio = $hasAudio;

        return $this;
    }

    private function addConcatFilterAndMapping(CRunner_FFMpeg_Media $outputMedia) {
        $sources = $this->driver->getMediaCollection()->map(function ($media, $key) {
            return "[{$key}]";
        });

        $concatWithVideo = $this->concatWithVideo ? 1 : 0;
        $concatWithAudio = $this->concatWithAudio ? 1 : 0;

        $this->addFilter(
                $sources->implode(''),
                "concat=n={$sources->count()}:v={$concatWithVideo}:a={$concatWithAudio}",
                '[concat]'
        )->addFormatOutputMapping($this->format, $outputMedia, ['[concat]']);
    }

}
