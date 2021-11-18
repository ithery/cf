<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 9:12:51 PM
 */
final class CGeo_Model_AdminLevelCollection implements \IteratorAggregate, \Countable {
    const MAX_LEVEL_DEPTH = 5;

    /**
     * @var CGeo_Model_AdminLevel[]
     */
    private $adminLevels;

    /**
     * @param CGeo_Model_AdminLevel[] $adminLevels
     */
    public function __construct(array $adminLevels = []) {
        $this->adminLevels = [];
        foreach ($adminLevels as $adminLevel) {
            $level = $adminLevel->getLevel();
            $this->checkLevel($level);
            if ($this->has($level)) {
                throw new CGeo_Exception_InvalidArgument(sprintf('Administrative level %d is defined twice', $level));
            }
            $this->adminLevels[$level] = $adminLevel;
        }
        ksort($this->adminLevels, SORT_NUMERIC);
    }

    /**
     * @inheritdoc
     */
    public function getIterator() {
        return new \ArrayIterator($this->all());
    }

    /**
     * @inheritdoc
     */
    public function count() {
        return count($this->adminLevels);
    }

    /**
     * @throws CollectionIsEmpty
     *
     * @return AdminLevel
     */
    public function first() {
        if (empty($this->adminLevels)) {
            throw new CGeo_Exception_CollectionIsEmpty();
        }

        return reset($this->adminLevels);
    }

    /**
     * @param int      $offset
     * @param null|int $length
     *
     * @return AdminLevel[]
     */
    public function slice($offset, $length = null) {
        return array_slice($this->adminLevels, $offset, $length, true);
    }

    /**
     * @param mixed $level
     *
     * @return bool
     */
    public function has($level) {
        return isset($this->adminLevels[$level]);
    }

    /**
     * @param mixed $level
     *
     * @throws \OutOfBoundsException
     * @throws CGeo_Exception_InvalidArgument
     *
     * @return CGeo_Model_AdminLevel
     */
    public function get($level) {
        $this->checkLevel($level);
        if (!isset($this->adminLevels[$level])) {
            throw new CGeo_Exception_InvalidArgument(sprintf('Administrative level %d is not set for this address', $level));
        }

        return $this->adminLevels[$level];
    }

    /**
     * @return CGeo_Model_AdminLevel[]
     */
    public function all() {
        return $this->adminLevels;
    }

    /**
     * @param int $level
     *
     * @throws CGeo_Exception_OutOfBounds
     */
    private function checkLevel($level) {
        if ($level <= 0 || $level > self::MAX_LEVEL_DEPTH) {
            throw new CGeo_Exception_OutOfBounds(
                sprintf('Administrative level should be an integer in [1,%d], %d given', self::MAX_LEVEL_DEPTH, $level)
            );
        }
    }
}
