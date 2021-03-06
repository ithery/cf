<?php

//declare(strict_types = 1);

namespace Embed\Detectors;

use Psr\Http\Message\UriInterface;

class Image extends Detector {
    public function detect() {
        $oembed = $this->extractor->getOEmbed();
        $document = $this->extractor->getDocument();
        $metas = $this->extractor->getMetas();
        $ld = $this->extractor->getLinkedData();

        return $oembed->url('image')
            ?: $oembed->url('thumbnail')
            ?: $oembed->url('thumbnail_url')
            ?: $metas->url('og:image', 'og:image:url', 'og:image:secure_url', 'twitter:image', 'twitter:image:src', 'lp:image')
            ?: $document->link('image_src')
            ?: $ld->url('image.url');
    }
}
