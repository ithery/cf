<?php

trait CConsole_Prompt_Concerns_HasSpinner {
    /**
     * The frames of the spinner (single dot moving around the perimeter).
     *
     * @var array<string>
     */
    protected $frames = ['⠂', '⠒', '⠐', '⠰', '⠠', '⠤', '⠄', '⠆'];

    /**
     * The frame to render when the spinner is static.
     *
     * @var string
     */
    protected $staticFrame = '⠶';

    /**
     * The interval between frames.
     *
     * @var int
     */
    protected $interval = 75;

    /**
     * @param int $count
     *
     * @return string
     */
    public function spinnerFrame($count) {
        return $this->frames[$count % count($this->frames)];
    }
}
