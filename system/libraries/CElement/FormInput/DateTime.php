<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_DateTime extends CElement_FormInput {
    /**
     * Date/time format string used by the underlying picker plugin, set by
     * concrete subclasses (eg. moment.js-style `YYYY-MM-DD`).
     *
     * @var null|string
     */
    protected $dateTimeFormat;
}
