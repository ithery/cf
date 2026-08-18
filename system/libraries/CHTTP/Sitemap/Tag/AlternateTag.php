<?php

class CHTTP_Sitemap_Tag_AlternateTag {
    public string $locale;

    public string $url;

    public static function create(string $url, string $locale = ''): self {
        return new static($url, $locale);
    }

    public function __construct(string $url, $locale = '') {
        $this->setUrl($url);

        $this->setLocale($locale);
    }

    public function setLocale(string $locale = ''): self {
        $this->locale = $locale;

        return $this;
    }

    public function setUrl(string $url = ''): self {
        $this->url = $url;

        return $this;
    }
}
