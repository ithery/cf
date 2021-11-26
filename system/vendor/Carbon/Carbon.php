<?php

namespace Carbon;

if (version_compare(PHP_VERSION, '6.0.0') >= 0) {
    class Carbon extends \CarbonV3\Carbon {
    }
} else {
    class Carbon extends \CarbonLegacy\Carbon {
    }
}
