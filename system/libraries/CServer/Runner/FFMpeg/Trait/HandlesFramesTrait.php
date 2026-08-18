<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CRunner_FFMpeg_Trait_HandlesFramesTrait {
    /**
     * @var bool
     */
    protected $mustBeAccurate = false;

    /**
     * @var bool
     */
    protected $returnFrameContents = false;

    public function accurate() {
        $this->mustBeAccurate = true;

        return $this;
    }

    public function unaccurate() {
        $this->mustBeAccurate = false;

        return $this;
    }

    public function getAccuracy() {
        return $this->mustBeAccurate;
    }

    public function getFrameContents() {
        $this->returnFrameContents = true;

        return $this->save();
    }
}
