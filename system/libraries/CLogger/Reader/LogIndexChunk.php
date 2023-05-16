<?php

class CLogger_Reader_LogIndexChunk {
    /**
     * @var array
     */
    public $data;

    /**
     * @var int
     */
    public $index;

    /**
     * @var int
     */
    public $size;

    /**
     * @var int
     */
    protected $earliestTimestamp;

    /**
     * @var int
     */
    protected $latestTimestamp;

    /**
     * @var array
     */
    protected $levelCounts = [];

    /**
     * @param array $data
     * @param int   $index
     * @param int   $size
     */
    public function __construct(array $data, $index, $size) {
        $this->data = $data;
        $this->index = $index;
        $this->size = $size;
    }

    /**
     * @param array $definition
     *
     * @return CLogger_Reader_LogIndexChunk
     */
    public static function fromDefinitionArray(array $definition) {
        $chunk = new self([], $definition['index'] ?? 0, $definition['size'] ?? 0);

        if (isset($definition['earliest_timestamp'])) {
            $chunk->earliestTimestamp = $definition['earliest_timestamp'];
        }

        if (isset($definition['latest_timestamp'])) {
            $chunk->latestTimestamp = $definition['latest_timestamp'];
        }

        if (isset($definition['level_counts'])) {
            $chunk->levelCounts = $definition['level_counts'];
        }

        return $chunk;
    }

    /**
     * @param int    $logIndex
     * @param int    $filePosition
     * @param int    $timestamp
     * @param string $severity
     *
     * @return void
     */
    public function addToIndex($logIndex, $filePosition, $timestamp, $severity) {
        if (!isset($this->data[$timestamp])) {
            $this->data[$timestamp] = [];
        }

        if (!isset($this->data[$timestamp][$severity])) {
            $this->data[$timestamp][$severity] = [];
        }

        if (!isset($this->levelCounts[$severity])) {
            $this->levelCounts[$severity] = 0;
        }

        $this->levelCounts[$severity]++;

        $this->data[$timestamp][$severity][$logIndex] = $filePosition;
        $this->size++;
        $this->earliestTimestamp = min($this->earliestTimestamp ?? $timestamp, $timestamp);
        $this->latestTimestamp = max($this->latestTimestamp ?? $timestamp, $timestamp);
    }

    /**
     * @return array
     */
    public function toArray() {
        return [
            'index' => $this->index,
            'size' => $this->size,
            'earliest_timestamp' => $this->earliestTimestamp ?? null,
            'latest_timestamp' => $this->latestTimestamp ?? null,
            'level_counts' => $this->levelCounts ?? [],
        ];
    }
}
