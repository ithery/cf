<?php

use CarbonV3\CarbonInterface;
use Opcodes\LogViewer\Utils\Utils;
use Opcodes\LogViewer\Facades\Cache;
use Opcodes\LogViewer\Utils\GenerateCacheKey;

trait CLogger_Reader_Concern_LogFile_CanCacheData {
    protected function indexCacheKeyForQuery(string $query = ''): string {
        return CLogger_Reader_Util_GenerateCacheKey::for($this, CLogger_Reader_Util_Utils::shortMd5($query) . ':index');
    }

    public function clearCache(): void {
        foreach ($this->getMetadata('related_indices', []) as $indexIdentifier => $indexMetadata) {
            $this->index($indexMetadata['query'])->clearCache();
        }

        foreach ($this->getRelatedCacheKeys() as $relatedCacheKey) {
            c::cache()->forget($relatedCacheKey);
        }

        c::cache()->forget($this->metadataCacheKey());
        c::cache()->forget($this->relatedCacheKeysKey());

        $this->index()->clearCache();
    }

    protected function cacheTtl(): CarbonInterface {
        return c::now()->addWeek();
    }

    protected function cacheKey() {
        return CLogger_Reader_Util_GenerateCacheKey::for($this);
    }

    protected function relatedCacheKeysKey() {
        return CLogger_Reader_Util_GenerateCacheKey::for($this, 'related-cache-keys');
    }

    /**
     * @param string $key
     *
     * @return void
     */
    public function addRelatedCacheKey($key) {
        $keys = $this->getRelatedCacheKeys();
        $keys[] = $key;
        c::cache()->put(
            $this->relatedCacheKeysKey(),
            array_unique($keys),
            $this->cacheTtl()
        );
    }

    /**
     * @return array
     */
    protected function getRelatedCacheKeys() {
        return array_merge(
            c::cache()->get($this->relatedCacheKeysKey(), []),
            [
                $this->indexCacheKeyForQuery(),
                $this->indexCacheKeyForQuery() . ':last-scan',
            ]
        );
    }

    /**
     * @return string
     */
    protected function metadataCacheKey() {
        return CLogger_Reader_Util_GenerateCacheKey::for($this, 'metadata');
    }

    /**
     * @return array
     */
    protected function loadMetadataFromCache() {
        return c::cache()->get($this->metadataCacheKey(), []);
    }

    /**
     * @param array $data
     *
     * @return void
     */
    protected function saveMetadataToCache($data) {
        c::cache()->put($this->metadataCacheKey(), $data, $this->cacheTtl());
    }
}
