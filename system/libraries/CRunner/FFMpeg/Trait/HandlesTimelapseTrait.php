<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Aug 26, 2020
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
