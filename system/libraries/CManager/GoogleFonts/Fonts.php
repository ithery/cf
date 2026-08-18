<?php

use Illuminate\Contracts\Support\Htmlable;

class CManager_GoogleFonts_Fonts implements Htmlable {
    /**
     * @var string
     */
    protected $googleFontsUrl;

    /**
     * @var null|string
     */
    protected $localizedUrl;

    /**
     * @var null|string
     */
    protected $localizedCss;

    /**
     * @var null|string
     */
    protected $nonce;

    /**
     * @var bool
     */
    protected $preferInline;

    /**
     * @var null|string
     */
    protected $preloadMeta;

    /**
     * @var bool
     */
    protected $preload;

    public function __construct(
        $googleFontsUrl,
        $localizedUrl = null,
        $localizedCss = null,
        $nonce = null,
        $preferInline = false,
        $preloadMeta = null,
        $preload = false
    ) {
        $this->googleFontsUrl = $googleFontsUrl;
        $this->localizedUrl = $localizedUrl;
        $this->localizedCss = $localizedCss;
        $this->nonce = $nonce;
        $this->preferInline = $preferInline;
        $this->preloadMeta = $preloadMeta;
        $this->preload = $preload;
    }

    /**
     * @return CBase_HtmlString
     */
    public function inline() {
        if (!$this->localizedCss) {
            return $this->fallback();
        }

        $attributes = $this->parseAttributes([
            'nonce' => $this->nonce ?? false,
        ]);

        $preloadMeta = '';
        if ($this->preload) {
            $preloadMeta = $this->preloadMeta;
        }

        return new CBase_HtmlString(<<<HTML
            {$preloadMeta}
            <style {$attributes->implode(' ')}>{$this->localizedCss}</style>
        HTML);
    }

    /**
     * Generates a <link> tag that links to the localized URL.
     *
     * If the localized URL is not available, this method will return the fallback
     * HTML, which links to the Google Fonts URL.
     *
     * The <link> tag is generated with the following attributes:
     *
     * - href: The localized URL.
     * - rel: "stylesheet"
     * - type: "text/css"
     * - nonce: The nonce value passed to the constructor, if any.
     *
     * If the preload flag is set, the method will also generate a <link> tag
     * with a rel attribute set to "preload" and the as attribute set to "style".
     * The href attribute of this tag will be set to the localized URL.
     *
     * @return CBase_HtmlString
     */
    public function link(): CBase_HtmlString {
        if (!$this->localizedUrl) {
            return $this->fallback();
        }

        $attributes = $this->parseAttributes([
            'href' => $this->localizedUrl,
            'rel' => 'stylesheet',
            'type' => 'text/css',
            'nonce' => $this->nonce ?? false,
        ]);

        $preloadMeta = '';
        if ($this->preload) {
            $preloadMeta = $this->preloadMeta;
        }

        return new CBase_HtmlString(<<<HTML
            {$preloadMeta}
            <link {$attributes->implode(' ')}>
        HTML);
    }

    public function fallback(): CBase_HtmlString {
        $attributes = $this->parseAttributes([
            'href' => $this->googleFontsUrl,
            'rel' => 'stylesheet',
            'type' => 'text/css',
            'nonce' => $this->nonce ?? false,
        ]);

        if ($this->preload) {
            return new CBase_HtmlString(<<<HTML
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link {$attributes->implode(' ')}>
            HTML);
        }

        return new CBase_HtmlString(<<<HTML
            <link {$attributes->implode(' ')}>
        HTML);
    }

    public function url(): string {
        if (!$this->localizedUrl) {
            return $this->googleFontsUrl;
        }

        return $this->localizedUrl;
    }

    public function toHtml(): CBase_HtmlString {
        return $this->preferInline ? $this->inline() : $this->link();
    }

    protected function parseAttributes($attributes): CCollection {
        return CCollection::make($attributes)
            ->reject(fn ($value, $key) => in_array($value, [false, null], true))
            ->flatMap(fn ($value, $key) => $value === true ? [$key] : [$key => $value])
            ->map(fn ($value, $key) => is_int($key) ? $value : $key . '="' . $value . '"')
            ->values();
    }
}
