<?php

namespace Embed\Adapters\Sassmeister\Detectors;

use Embed\Detectors\Code as Detector;
use Embed\EmbedCode;
use function Embed\html;
use function Embed\matchPath;

class Code extends Detector {
    public function detect() {
        return parent::detect() ?: $this->fallback();
    }

    private function fallback() {
        $uri = $this->extractor->getUri();

        if (!matchPath('/gist/*', $uri->getPath())) {
            return null;
        }

        $id = explode('/', $uri->getPath())[2];
        $height = 480;

        $html = [
            html('p', [
                'class' => 'sassmeister',
                'data-gist-id' => $id,
                'data-height' => $height,
                'data-theme' => 'tomorrow',
            ], '<a href="http://sassmeister.com/gist/' . $id . '">Play with this gist on SassMeister.</a>'),
            html('script', [
                'src' => 'http://cdn.sassmeister.com/js/embed.js',
                'async' => true,
            ]),
        ];

        return new EmbedCode(implode('', $html), null, $height);
    }
}
