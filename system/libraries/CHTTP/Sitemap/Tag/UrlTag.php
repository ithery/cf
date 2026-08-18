<?php

use Carbon\Carbon;

class CHTTP_Sitemap_Tag_UrlTag extends CHTTP_Sitemap_TagAbstract {
    const CHANGE_FREQUENCY_ALWAYS = 'always';

    const CHANGE_FREQUENCY_HOURLY = 'hourly';

    const CHANGE_FREQUENCY_DAILY = 'daily';

    const CHANGE_FREQUENCY_WEEKLY = 'weekly';

    const CHANGE_FREQUENCY_MONTHLY = 'monthly';

    const CHANGE_FREQUENCY_YEARLY = 'yearly';

    const CHANGE_FREQUENCY_NEVER = 'never';

    public string $url;

    public Carbon $lastModificationDate;

    public string $changeFrequency;

    public float $priority = 0.8;

    /**
     * @var \CHTTP_Sitemap_Tag_AlternateTag[]
     */
    public array $alternates = [];

    /**
     * @var \CHTTP_Sitemap_Tag_ImageTag[]
     */
    public array $images = [];

    /**
     * @var \CHTTP_Sitemap_Tag_VideoTag[]
     */
    public array $videos = [];

    public static function create(string $url) {
        return new static($url);
    }

    public function __construct(string $url) {
        $this->url = $url;

        $this->changeFrequency = static::CHANGE_FREQUENCY_DAILY;
    }

    public function setUrl(string $url = '') {
        $this->url = $url;

        return $this;
    }

    public function setLastModificationDate(DateTimeInterface $lastModificationDate) {
        $this->lastModificationDate = Carbon::instance($lastModificationDate);

        return $this;
    }

    public function setChangeFrequency(string $changeFrequency) {
        $this->changeFrequency = $changeFrequency;

        return $this;
    }

    public function setPriority(float $priority) {
        $this->priority = max(0, min($priority, 1));

        return $this;
    }

    public function addAlternate(string $url, string $locale = '') {
        $this->alternates[] = new CHTTP_Sitemap_Tag_AlternateTag($url, $locale);

        return $this;
    }

    public function addImage(string $url, string $caption = '', string $geo_location = '', string $title = '', string $license = '') {
        $this->images[] = new CHTTP_Sitemap_Tag_ImageTag($url, $caption, $geo_location, $title, $license);

        return $this;
    }

    public function addVideo(string $thumbnailLoc, string $title, string $description, $contentLoc = null, $playerLoc = null, array $options = [], array $allow = [], array $deny = []) {
        $this->videos[] = new CHTTP_Sitemap_Tag_VideoTag($thumbnailLoc, $title, $description, $contentLoc, $playerLoc, $options, $allow, $deny);

        return $this;
    }

    public function path(): string {
        return parse_url($this->url, PHP_URL_PATH) ?? '';
    }

    /**
     * @param null|int $index
     *
     * @return null|array|string
     */
    public function segments(?int $index = null) {
        $segments = c::collect(explode('/', $this->path()))
            ->filter(function ($value) {
                return $value !== '';
            })
            ->values()
            ->toArray();

        if (!is_null($index)) {
            return $this->segment($index);
        }

        return $segments;
    }

    public function segment(int $index): ?string {
        return $this->segments()[$index - 1] ?? null;
    }
}
