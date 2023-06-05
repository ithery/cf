<?php

use Carbon\CarbonInterface;

trait CLogger_Reader_Concern_LogIndex_CanFilterIndex {
    protected ?int $filterFrom = null;

    protected ?int $filterTo = null;

    protected ?array $filterLevels = null;

    protected ?int $limit = null;

    protected ?int $skip = null;

    public function setQuery(string $query = null): self {
        if ($this->query !== $query) {
            $this->query = $query;

            $this->loadMetadata();
        }

        return $this;
    }

    public function getQuery(): ?string {
        return $this->query;
    }

    /**
     * @param null|int|CarbonInterface $from
     * @param null|int|CarbonInterface $to
     *
     * @return self
     */
    public function forDateRange($from = null, $to = null) {
        if ($from instanceof CarbonInterface) {
            $from = $from->timestamp;
        }

        if ($to instanceof CarbonInterface) {
            $to = $to->timestamp;
        }

        $this->filterFrom = $from;
        $this->filterTo = $to;

        return $this;
    }

    /**
     * @param null|string|array $levels
     *
     * @return self
     */
    public function forLevels($levels = null) {
        if (is_string($levels)) {
            $levels = [$levels];
        }

        if (is_array($levels)) {
            $this->filterLevels = array_map('strtolower', array_filter($levels));
        } else {
            $this->filterLevels = null;
        }

        return $this;
    }

    /**
     * @param null|string $level
     *
     * @return self
     */
    public function forLevel($level = null) {
        return $this->forLevels($level);
    }

    public function getSelectedLevels(): ?array {
        return $this->filterLevels ?? CLogger_Level::caseValues();
    }

    public function skip(int $skip = null): self {
        $this->skip = $skip;

        return $this;
    }

    public function getSkip(): ?int {
        return $this->skip;
    }

    public function limit(int $limit = null): self {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit(): ?int {
        return $this->limit;
    }

    protected function hasDateFilters(): bool {
        return isset($this->filterFrom)
            || isset($this->filterTo);
    }

    protected function hasFilters(): bool {
        return $this->hasDateFilters()
            || isset($this->filterLevels);
    }
}
