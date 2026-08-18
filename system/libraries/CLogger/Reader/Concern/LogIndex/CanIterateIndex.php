<?php

trait CLogger_Reader_Concern_LogIndex_CanIterateIndex {
    protected array $cachedFlatIndex;

    protected ArrayIterator $cachedFlatIndexIterator;

    /**
     * @var string
     */
    protected string $direction = CLogger_Reader_Direction::FORWARD;

    /**
     * @param string $direction
     *
     * @return self
     */
    public function setDirection($direction) {
        $this->direction = $direction === CLogger_Reader_Direction::BACKWARD
            ? CLogger_Reader_Direction::BACKWARD
            : CLogger_Reader_Direction::FORWARD;

        return $this->reset();
    }

    public function isForward(): bool {
        return $this->direction === CLogger_Reader_Direction::FORWARD;
    }

    public function isBackward(): bool {
        return $this->direction === CLogger_Reader_Direction::BACKWARD;
    }

    /**
     * @alias backward
     */
    public function reverse(): self {
        return $this->backward();
    }

    public function backward(): self {
        return $this->setDirection(CLogger_Reader_Direction::BACKWARD);
    }

    public function forward(): self {
        return $this->setDirection(CLogger_Reader_Direction::FORWARD);
    }

    public function next(): ?array {
        if (!isset($this->cachedFlatIndex)) {
            $this->cachedFlatIndex = $this->getFlatIndex();
        }

        if (!isset($this->cachedFlatIndexIterator)) {
            $this->cachedFlatIndexIterator = new ArrayIterator($this->cachedFlatIndex);
        } else {
            $this->cachedFlatIndexIterator->next();
        }

        if (!$this->cachedFlatIndexIterator->valid()) {
            return null;
        }

        return [
            $this->cachedFlatIndexIterator->key(),
            $this->cachedFlatIndexIterator->current(),
        ];
    }

    public function reset(): self {
        unset($this->cachedFlatIndexIterator, $this->cachedFlatIndex);

        return $this;
    }
}
