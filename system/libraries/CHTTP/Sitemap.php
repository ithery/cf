<?php

/**
 * @see https://github.com/spatie/laravel-sitemap/
 */
class CHTTP_Sitemap {
    /**
     * @var \CHTTP_Sitemap_Tag_UrlTag[]
     */
    protected array $tags = [];

    public static function create() {
        return new static();
    }

    /**
     * @param string $path
     *
     * @return CHTTP_Sitemap_Tag_UrlTag
     */
    public function addUrl(string $path) {
        $url = CHTTP_Sitemap_Tag_UrlTag::create($path);
        $this->add($url);

        return $url;
    }

    /**
     * Undocumented function.
     *
     * @param string|CHTTP_Sitemap_Tag_UrlTag|CHTTP_Sitemap_Contract_SitemapableInterface|iterable $tag
     *
     * @return static
     */
    public function add($tag): self {
        if (is_object($tag) && array_key_exists(Sitemapable::class, class_implements($tag))) {
            $tag = $tag->toSitemapTag();
        }

        if (is_iterable($tag)) {
            foreach ($tag as $item) {
                $this->add($item);
            }

            return $this;
        }

        if (is_string($tag)) {
            $tag = CHTTP_Sitemap_Tag_UrlTag::create($tag);
        }

        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function getTags(): array {
        return $this->tags;
    }

    public function getUrl(string $url): ?CHTTP_Sitemap_Tag_UrlTag {
        return c::collect($this->tags)->first(function (CHTTP_Sitemap_Tag_UrlTag $tag) use ($url) {
            return $tag->getType() === 'url' && $tag->url === $url;
        });
    }

    public function hasUrl(string $url): bool {
        return (bool) $this->getUrl($url);
    }

    public function render(): string {
        $tags = c::collect($this->tags)->unique('url')->filter();

        return c::view('cresenity.http.sitemap.sitemap')
            ->with(compact('tags'))
            ->render();
    }

    public function writeToFile(string $path): self {
        file_put_contents($path, $this->render());

        return $this;
    }

    public function writeToDisk(string $disk, string $path): self {
        CStorage::instance()->disk($disk)->put($path, $this->render());

        return $this;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse() {
        return CHTTP_ResponseFactory::instance()->make($this->render(), 200, [
            'Content-Type' => 'text/xml',
        ]);
    }
}
