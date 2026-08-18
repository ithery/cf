<?php
class CEmail_Checker_BlacklistedDomain implements \IteratorAggregate, \Countable {
    /**
     * @var array
     */
    protected $domains;

    public function __construct() {
        $this->domains = CEmail_Helper::parseLines(file_get_contents(
            DOCROOT . 'system' . DS . 'data' . 'email' . DS . 'blacklist.txt'
        ));
    }

    public function toArray() {
        return $this->domains;
    }

    public function getIterator(): Traversable {
        return new \ArrayIterator($this->toArray());
    }

    public function count(): int {
        return count($this->domains);
    }
}
