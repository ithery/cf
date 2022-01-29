<?php
class CMiddleware_LiteSpeed_Cache {
    protected $staleKey;

    public function __construct() {
        $this->staleKey = '';
    }

    public function purge($items, $stale = true) {
        if ($stale === true) {
            $this->staleKey = 'stale,';
        }

        return header('X-LiteSpeed-Purge: ' . $this->staleKey . $items);
    }

    public function purgeAll($stale = true) {
        return $this->purge('*', $stale);
    }

    public function purgeTags(array $tags, $stale = true) {
        if (count($tags)) {
            return $this->purge(implode(',', array_map(function ($tag) {
                return 'tag=' . $tag;
            }, $tags)), $stale);
        }
    }

    public function purgeItems(array $items, $stale = true) {
        if (count($items)) {
            return $this->purge(implode(',', $items), $stale);
        }
    }
}
