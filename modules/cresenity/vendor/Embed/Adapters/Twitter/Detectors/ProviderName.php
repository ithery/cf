<?php

namespace Embed\Adapters\Twitter\Detectors;

use Embed\Detectors\ProviderName as Detector;

class ProviderName extends Detector {
    public function detect(): string {
        return 'Twitter';
    }
}
