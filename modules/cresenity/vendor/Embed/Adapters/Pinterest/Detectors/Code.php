<?php


namespace Embed\Adapters\Pinterest\Detectors;

use Embed\Detectors\Code as Detector;
use Embed\EmbedCode;
use function Embed\html;
use function Embed\match;

class Code extends Detector {

    public function detect() {
        return parent::detect() ?: $this->fallback();
    }

    private function fallback() {
        $uri = $this->extractor->getUri();

        if (!match('/pin/*', $uri->getPath())) {
            return null;
        }

        $html = [
            html('a', [
                'data-pin-do' => 'embedPin',
                'href' => $uri,
            ]),
            html('script', [
                'async' => true,
                'defer' => true,
                'src' => '//assets.pinterest.com/js/pinit.js',
            ]),
        ];

        return new EmbedCode(implode('', $html), 236, 442);
    }

}
