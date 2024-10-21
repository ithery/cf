<?php
/**
 * @see CCollection
 */
class CReport_Jasper_Report_DataIterator implements ArrayAccess, Iterator, Countable {
    protected $data;

    protected $currentIndex = 0;

    public function __construct(CCollection $data) {
        $this->data = $data;
        $this->currentIndex = 0;
    }

    /**
     * Countable: count.
     */
    #[\ReturnTypeWillChange]
    public function count() {
        return $this->data->count();
    }

    /**
     * ArrayAccess: offsetExists.
     *
     * @param mixed $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset) {
        return $this->data->has($offset);
    }

    /**
     * ArrayAccess: offsetGet.
     *
     * @param mixed $offset
     */
    public function offsetGet($offset) {
        $row = $this->data->get($offset);
        if ($row) {
            return new CReport_Jasper_Report_DataRow($row);
        }

        return null;
    }

    /**
     * ArrayAccess: offsetSet.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @throws Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value) {
        throw new Exception('CReport_Jasper_Report_DataIterator data is read only');
    }

    /**
     * ArrayAccess: offsetUnset.
     *
     * @param mixed $offset
     *
     * @throws Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset) {
        throw new Exception('CReport_Jasper_Report_DataIterator data is read only');
    }

    /**
     * Iterator: current.
     */
    public function current() {
        return $this->offsetGet($this->currentIndex);
    }

    /**
     * Iterator: key.
     */
    public function key() {
        return $this->currentIndex;
    }

    /**
     * Iterator: next.
     */
    #[\ReturnTypeWillChange]
    public function next() {
        ++$this->currentIndex;

        return $this;
    }

    /**
     * Iterator: prev.
     */
    public function prev() {
        --$this->currentIndex;

        return $this;
    }

    /**
     * Iterator: rewind.
     */
    #[\ReturnTypeWillChange]
    public function rewind() {
        $this->currentIndex = 0;

        return $this;
    }

    /**
     * Iterator: valid.
     */
    #[\ReturnTypeWillChange]
    public function valid() {
        return $this->offsetExists($this->currentIndex);
    }
}
