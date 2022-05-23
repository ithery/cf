<?php
declare(strict_types=1);

namespace Embed\Adapters\Twitter\Detectors;

use Psr\Http\Message\UriInterface;
use Embed\Detectors\Image as Detector;

class Image extends Detector {
    /**
     * @return null|UriInterface
     */
    public function detect() {
        $extractor = $this->extractor;
        /** @var \Embed\Adapters\Twitter\Extractor $extractor */
        $api = $extractor->getApi();
        $preview = $api->url('includes', 'media', '0', 'preview_image_url');

        if ($preview) {
            return $preview;
        }

        $regular = $api->url('includes', 'media', '0', 'url');

        if ($regular) {
            return $regular;
        }

        return parent::detect();
    }
}
