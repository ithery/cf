<?php

namespace Embed\Adapters\Archive\Detectors;

use Embed\Detectors\ProviderName as Detector;

class ProviderName extends Detector {

    public function detect() {
        return 'Internet Archive';
    }

}
