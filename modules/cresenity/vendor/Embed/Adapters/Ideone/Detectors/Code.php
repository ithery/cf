<?php
declare(strict_types=1);

namespace Embed\Adapters\Ideone\Detectors;

use Embed\EmbedCode;
use function Embed\html;
use Embed\Detectors\Code as Detector;

class Code extends Detector {
    /**
     * Undocumented function.
     *
     * @return null|EmbedCode
     */
    public function detect() {
        return parent::detect()
            ?: $this->fallback();
    }

    /**
     * @return null|EmbedCode
     */
    private function fallback() {
        $uri = $this->extractor->getUri();
        $id = explode('/', $uri->getPath())[1];

        if (empty($id)) {
            return null;
        }

        return new EmbedCode(
            html('script', ['src' => "https://ideone.com/e.js/{$id}"])
        );
    }
}
