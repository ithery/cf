<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Aug 26, 2020 
 * @license Ittron Global Teknologi
 */

use Evenement\EventEmitterInterface;

trait CRunner_FFMpeg_Trait_HasProgressListenerTrait
{
    /**
     * @var \Closure
     */
    protected $onProgressCallback;

    /**
     * @var float
     */
    protected $lastPercentage;

    /**
     * @var float
     */
    protected $lastRemaining = 0;

    public function onProgress(Closure $callback)
    {
        $this->onProgressCallback = $callback;

        return $this;
    }

    private function applyProgressListenerToFormat(EventEmitterInterface $format)
    {
        $format->on('progress', function ($media, $format, $percentage, $remaining = null, $rate = null) {
            if ($percentage !== $this->lastPercentage && $percentage < 100) {
                $this->lastPercentage = $percentage;
                $this->lastRemaining = $remaining ?: $this->lastRemaining;

                call_user_func($this->onProgressCallback, $this->lastPercentage, $this->lastRemaining, $rate);
            }
        });
    }
}