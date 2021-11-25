<?php

namespace Embed\Adapters\Twitch\Detectors;

use Embed\Detectors\Code as Detector;
use Embed\EmbedCode;
use function Embed\html;

class Code extends Detector {

    public function detect() {
        return parent::detect() ?: $this->fallback();
    }

    private function fallback() {
        $path = $this->extractor->getUri()->getPath();

        if ($id = self::getVideoId($path)) {
            $code = self::generateCode(['video' => "v{$id}"]);
            return new EmbedCode($code, 620, 378);
        }

        if ($id = self::getChannelId($path)) {
            $code = self::generateCode(['channel' => $id]);
            return new EmbedCode($code, 620, 378);
        }

        return null;
    }

    private static function getVideoId($path) {
        if (preg_match('#^/videos/(\d+)$#', $path, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private static function getChannelId($path) {
        if (preg_match('#^/(\w+)$#', $path, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private static function generateCode(array $params) {
        $query = http_build_query(['autoplay' => 'false'] + $params);

        return html('iframe', [
            'src' => "https://player.twitch.tv/?{$query}",
            'frameborder' => 0,
            'allowfullscreen' => 'true',
            'scrolling' => 'no',
            'height' => 378,
            'width' => 620,
        ]);
    }

}
