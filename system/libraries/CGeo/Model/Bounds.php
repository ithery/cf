<?php

final class CGeo_Model_Bounds {
    /**
     * @var float
     */
    private $south;

    /**
     * @var float
     */
    private $west;

    /**
     * @var float
     */
    private $north;

    /**
     * @var float
     */
    private $east;

    /**
     * @param float $south South bound, also min latitude
     * @param float $west  West bound, also min longitude
     * @param float $north North bound, also max latitude
     * @param float $east  East bound, also max longitude
     */
    public function __construct($south, $west, $north, $east) {
        CGeo_Assert::notNull($south);
        CGeo_Assert::notNull($west);
        CGeo_Assert::notNull($north);
        CGeo_Assert::notNull($east);

        $south = (float) $south;
        $north = (float) $north;
        $west = (float) $west;
        $east = (float) $east;

        CGeo_Assert::latitude($south);
        CGeo_Assert::latitude($north);
        CGeo_Assert::longitude($west);
        CGeo_Assert::longitude($east);

        $this->south = $south;
        $this->west = $west;
        $this->north = $north;
        $this->east = $east;
    }

    /**
     * Returns the south bound.
     *
     * @return float
     */
    public function getSouth() {
        return $this->south;
    }

    /**
     * Returns the west bound.
     *
     * @return float
     */
    public function getWest() {
        return $this->west;
    }

    /**
     * Returns the north bound.
     *
     * @return float
     */
    public function getNorth() {
        return $this->north;
    }

    /**
     * Returns the east bound.
     *
     * @return float
     */
    public function getEast() {
        return $this->east;
    }

    /**
     * Returns an array with bounds.
     *
     * @return array
     */
    public function toArray() {
        return [
            'south' => $this->getSouth(),
            'west' => $this->getWest(),
            'north' => $this->getNorth(),
            'east' => $this->getEast(),
        ];
    }
}
