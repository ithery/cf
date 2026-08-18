<?php

use Illuminate\Contracts\Filesystem\Filesystem;

class CManager_GoogleFonts {
    /**
     * @var CStorage_Adapter
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var bool
     */
    protected $inline;

    /**
     * @var bool
     */
    protected $fallback;

    /**
     * @var string
     */
    protected $userAgent;

    /**
     * @var array
     */
    protected $fonts;

    /**
     * @var bool
     */
    protected $preload;

    public function __construct(
        CStorage_Adapter $filesystem,
        string $path,
        bool $inline,
        bool $fallback,
        string $userAgent,
        array $fonts,
        bool $preload
    ) {
        $this->filesystem = $filesystem;
        $this->path = $path;
        $this->inline = $inline;
        $this->fallback = $fallback;
        $this->userAgent = $userAgent;
        $this->fonts = $fonts;
        $this->preload = $preload;
    }

    /**
     * Loads a font configuration and returns a Fonts object.
     *
     * This method attempts to load the specified font either from a local
     * source or by fetching it from a remote URL. If the font is not found
     * locally and `forceDownload` is true, it will always fetch the font
     * from the remote URL.
     *
     * @param string|array $options       an array or string specifying the font options,
     *                                    including the font name and an optional nonce
     * @param bool         $forceDownload whether to force downloading the font from the
     *                                    remote URL even if it is available locally
     *
     * @throws RuntimeException if the specified font does not exist
     * @throws Exception        if an error occurs while fetching the font
     *                          and fallback is disabled
     *
     * @return CManager_GoogleFonts_Fonts
     */
    public function load($options = [], bool $forceDownload = false): CManager_GoogleFonts_Fonts {
        list('font' => $font, 'nonce' => $nonce) = $this->parseOptions($options);

        if (!isset($this->fonts[$font])) {
            throw new RuntimeException("Font `{$font}` doesn't exist");
        }

        $url = $this->fonts[$font];

        try {
            if ($forceDownload) {
                return $this->fetch($url, $nonce);
            }

            $fonts = $this->loadLocal($url, $nonce);

            if (!$fonts) {
                return $this->fetch($url, $nonce);
            }

            return $fonts;
        } catch (Exception $exception) {
            if (!$this->fallback) {
                throw $exception;
            }

            return new CManager_GoogleFonts_Fonts($url, null, null, $nonce);
        }
    }

    protected function loadLocal(string $url, ?string $nonce): ?CManager_GoogleFonts_Fonts {
        if (!$this->filesystem->exists($this->path($url, 'fonts.css'))) {
            return null;
        }

        $localizedCss = $this->filesystem->get($this->path($url, 'fonts.css'));

        $preloadMeta = null;
        if ($this->filesystem->exists($this->path($url, 'preload.html'))) {
            $preloadMeta = $this->filesystem->get($this->path($url, 'preload.html'));
        }

        return new CManager_GoogleFonts_Fonts(
            $url,
            $this->filesystem->url($this->path($url, 'fonts.css')),
            $localizedCss,
            $nonce,
            $this->inline,
            $preloadMeta,
            $this->preload
        );
    }

    protected function fetch(string $url, ?string $nonce): CManager_GoogleFonts_Fonts {
        $css = CHTTP::client()->withHeaders(['User-Agent' => $this->userAgent])
            ->get($url)
            ->body();

        $localizedCss = $css;
        $preloadMeta = '';

        foreach ($this->extractFontUrls($css) as $fontUrl) {
            $localizedFontUrl = $this->localizeFontUrl($fontUrl);

            $this->filesystem->put(
                $this->path($url, $localizedFontUrl),
                CHTTP::client()->get($fontUrl)->body(),
            );

            $localizedUrl = $this->filesystem->url($this->path($url, $localizedFontUrl));
            $preloadMeta .= $this->getPreload($url) . "\n";
            $localizedCss = str_replace(
                $fontUrl,
                $localizedUrl,
                $localizedCss,
            );
        }

        $this->filesystem->put($this->path($url, 'fonts.css'), $localizedCss);
        $this->filesystem->put($this->path($url, 'preload.html'), $preloadMeta);

        return new CManager_GoogleFonts_Fonts(
            $url,
            $this->filesystem->url($this->path($url, 'fonts.css')),
            $localizedCss,
            $nonce,
            $this->inline,
            $preloadMeta,
            $this->preload
        );
    }

    protected function extractFontUrls(string $css): array {
        $matches = [];
        preg_match_all('/url\((https:\/\/fonts.gstatic.com\/[^)]+)\)/', $css, $matches);

        return array_unique($matches[1] ?? []);
    }

    protected function localizeFontUrl(string $path): string {
        // Google Fonts seem to have recently changed their URL structure to one that no longer contains a file
        // extension (see https://github.com/spatie/laravel-google-fonts/issues/40). We account for that by falling back
        // to 'woff2' in that case.
        $pathComponents = explode('.', str_replace('https://fonts.gstatic.com/', '', $path));
        $path = $pathComponents[0];
        $extension = $pathComponents[1] ?? 'woff2';

        return implode('.', [cstr::slug($path), $extension]);
    }

    protected function path(string $url, string $path = ''): string {
        $segments = c::collect([
            $this->path,
            substr(md5($url), 0, 10),
            $path,
        ]);

        return $segments->filter()->join('/');
    }

    /**
     * Parses the given options, normalizing it to an array.
     *
     * When given a string, it is assumed to be the font name and the nonce value is set to null.
     *
     * When given an array, the method expects the array to have the following keys:
     * - "font": the name of the font to load. Defaults to "default".
     * - "nonce": the nonce value to use. Defaults to null.
     *
     * @param string|array $options the options to parse
     *
     * @return array the parsed options
     */
    protected function parseOptions($options): array {
        if (is_string($options)) {
            $options = ['font' => $options, 'nonce' => null];
        }

        return [
            'font' => $options['font'] ?? 'default',
            'nonce' => $options['nonce'] ?? null,
        ];
    }

    public function getPreload(string $url) {
        return sprintf('<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>', $url);
    }
}
