<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Aug 26, 2020 
 * @license Ittron Global Teknologi
 */
trait CRunner_FFMpeg_Trait_HandlesTimelapseTrait {

    /**
     * @var float
     */
    protected $timelapseFramerate;

    public function asTimelapseWithFramerate($framerate) {
        $this->timelapseFramerate = $framerate;

        return $this;
    }

    protected function addTimelapseParametersToFormat() {
        $this->format->setInitialParameters(array_merge(
                        $this->format->getInitialParameters() ?: [],
                        ['-framerate', $this->timelapseFramerate, '-f', 'image2']
        ));
    }

}
