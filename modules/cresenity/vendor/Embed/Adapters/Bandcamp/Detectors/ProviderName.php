<?php

namespace Embed\Adapters\Bandcamp\Detectors;

use Embed\Detectors\ProviderName as Detector;

class ProviderName extends Detector {
    public function detect() {
        return 'Bandcamp';
    }
}
