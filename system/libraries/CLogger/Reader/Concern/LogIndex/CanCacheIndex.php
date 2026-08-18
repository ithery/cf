<?php

use Carbon\CarbonInterface;

trait CLogger_Reader_Concern_LogIndex_CanCacheIndex {
    /**
     * @return void
     */
    public function clearCache() {
        $this->clearChunksFromCache();

        CLogger_Reader::cache()->forget($this->metaCacheKey());
        CLogger_Reader::cache()->forget($this->cacheKey());

        // this will reset all properties to default, because it won't find any cached settings for this index
        $this->loadMetadata();
    }

    /**
     * @return void
     */
    protected function saveMetadataToCache() {
        CLogger_Reader::cache()->put($this->metaCacheKey(), $this->getMetadata(), $this->cacheTtl());
    }

    /**
     * @return array
     */
    protected function getMetadataFromCache() {
        return CLogger_Reader::cache()->get($this->metaCacheKey(), []);
    }

    /**
     * @param CLogger_Reader_LogIndexChunk $chunk
     *
     * @return void
     */
    protected function saveChunkToCache(CLogger_Reader_LogIndexChunk $chunk) {
        CLogger_Reader::cache()->put(
            $this->chunkCacheKey($chunk->index),
            $chunk->data,
            $this->cacheTtl()
        );
    }

    /**
     * @param int $index
     * @param [type] $default
     *
     * @return null|array
     */
    protected function getChunkDataFromCache(int $index, $default = null): ?array {
        return CLogger_Reader::cache()->get($this->chunkCacheKey($index), $default);
    }

    /**
     * @return void
     */
    protected function clearChunksFromCache(): void {
        foreach ($this->getChunkDefinitions() as $chunkDefinition) {
            CLogger_Reader::cache()->forget($this->chunkCacheKey($chunkDefinition['index']));
        }
    }

    protected function cacheKey(): string {
        return CLogger_Reader_Util_GenerateCacheKey::for($this);
    }

    protected function metaCacheKey(): string {
        return CLogger_Reader_Util_GenerateCacheKey::for($this, 'metadata');
    }

    protected function chunkCacheKey(int $index): string {
        return CLogger_Reader_Util_GenerateCacheKey::for($this, "chunk:$index");
    }

    protected function cacheTtl(): CarbonInterface {
        if (!empty($this->query)) {
            // There will be a lot more search queries, and they're usually just one-off searches.
            // We don't want these to take up too much of Redis/File-cache space for too long.
            return c::now()->addDay();
        }

        return c::now()->addWeek();
    }
}
