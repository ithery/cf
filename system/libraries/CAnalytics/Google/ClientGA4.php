<?php

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;

class CAnalytics_Google_ClientGA4 {
    /**
     * @var BetaAnalyticsDataClient
     */
    protected $service;

    /**
     * @var \CCache_RepositoryInterface
     */
    protected $cache;

    /**
     * Dalam detik, bukan menit — setter-nya yang mengalikan 60.
     *
     * @var int
     */
    protected $cacheLifeTimeInSeconds = 0;

    /**
     * Dalam detik, bukan menit — setter-nya yang mengalikan 60.
     *
     * @var int
     */
    protected $cacheRealtimeLifeTimeInSeconds = 0;

    public function __construct(BetaAnalyticsDataClient $service, CCache_RepositoryInterface $cache) {
        $this->service = $service;

        $this->cache = $cache;
    }

    /**
     * Set the cache time.
     *
     * @param int $cacheLifeTimeInMinutes
     *
     * @return self
     */
    public function setCacheLifeTimeInMinutes($cacheLifeTimeInMinutes) {
        $this->cacheLifeTimeInSeconds = $cacheLifeTimeInMinutes * 60;

        return $this;
    }

    /**
     * Set the cache time.
     *
     * @param int $cacheLifeTimeInMinutes
     *
     * @return self
     */
    public function setCacheRealtimeLifeTimeInMinutes($cacheLifeTimeInMinutes) {
        $this->cacheRealtimeLifeTimeInSeconds = $cacheLifeTimeInMinutes * 60;

        return $this;
    }

    /**
     * Query the Google Analytics Service with given parameters.
     *
     * @param array    $reportData
     * @param mixed    $realtime
     * @param null|int $cacheInMinutes
     *
     * @return null|array|\Google\Analytics\Data\V1beta\RunReportResponse
     */
    public function runReport($reportData, $realtime = false, $cacheInMinutes = null) {
        //kuncinya disusun dari laporan yang diminta saja, bukan dari
        //func_get_args(). Lama waktu cache bukan bagian dari jati diri sebuah
        //laporan: dua pemanggil yang meminta laporan yang sama dengan lama
        //cache berbeda dulu mendapat dua entri berbeda, jadi dua panggilan ke
        //Google. func_get_args() juga berubah panjang mengikuti berapa argumen
        //yang benar-benar dituliskan pemanggil, sehingga runReport($data) dan
        //runReport($data, false) menghasilkan kunci yang berlainan.
        $cacheName = $this->determineCacheName([$reportData, $realtime]);

        //remember() menghitung detik. Properti di atas sudah disimpan dalam
        //detik oleh setter-nya, tetapi argumen $cacheInMinutes dulu diteruskan
        //apa adanya — sehingga setiap pemanggil yang meminta "cache 1 menit"
        //sebenarnya hanya mendapat 1 detik, dan praktis tidak pernah kena cache.
        $cacheInSeconds = $cacheInMinutes === null
            ? ($realtime ? $this->cacheRealtimeLifeTimeInSeconds : $this->cacheLifeTimeInSeconds)
            : (int) $cacheInMinutes * 60;

        if ($cacheInSeconds === 0) {
            $this->cache->forget($cacheName);
        }

        return $this->cache->remember(
            $cacheName,
            $cacheInSeconds,
            function () use ($reportData, $realtime) {
                return $realtime ? $this->service->runRealtimeReport($reportData) : $this->service->runReport($reportData);
            }
        );
    }

    /**
     * @return BetaAnalyticsDataClient
     */
    public function getAnalyticsService() {
        return $this->service;
    }

    /**
     * Determine the cache name for the set of query properties given.
     *
     * @param mixed $properties
     */
    protected function determineCacheName($properties) {
        return 'capp.analytics.google.ga4.' . md5(serialize($properties));
    }
}
