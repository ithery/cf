<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Aug 26, 2020 
 * @license Ittron Global Teknologi
 */
trait CRunner_FFMpeg_Trait_HandlesFramesTrait {

    /**
     * @var boolean
     */
    protected $mustBeAccurate = false;

    /**
     * @var boolean
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
