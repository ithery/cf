<?php

namespace Embed\Adapters\Slides\Detectors;

use function Embed\cleanPath;
use Embed\Detectors\Code as Detector;
use Embed\EmbedCode;
use function Embed\html;

class Code extends Detector {

    public function detect() {
        return parent::detect() ?: $this->fallback();
    }

    private function fallback() {
        $uri = $this->extractor->getUri();

        $path = cleanPath($uri->getPath() . '/embed');
        $src = $uri->withPath($path);
        $width = 576;
        $height = 420;

        $html = html('iframe', [
            'src' => $src,
            'width' => $width,
            'height' => $height,
            'style' => 'border:none',
            'frameborder' => 0,
            'allowTransparency' => 'true',
        ]);

        return new EmbedCode($html, $width, $height);
    }

}
