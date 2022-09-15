<?php
abstract class CEmail_Client_AbstractCollection {
    /**
     * @var array
     */
    protected $aItems;

    protected function __construct() {
        $this->aItems = [];
    }

    /**
     * @param mixed $mItem
     * @param bool  $bToTop = false
     *
     * @return self
     */
    public function add($mItem, $bToTop = false) {
        if ($bToTop) {
            \array_unshift($this->aItems, $mItem);
        } else {
            \array_push($this->aItems, $mItem);
        }

        return $this;
    }

    /**
     * @param array $aItems
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     *
     * @return self
     */
    public function addArray($aItems) {
        if (!\is_array($aItems)) {
            throw new \CEmail_Client_Exception_InvalidArgumentException();
        }

        foreach ($aItems as $mItem) {
            $this->Add($mItem);
        }

        return $this;
    }

    /**
     * @return self
     */
    public function clear() {
        $this->aItems = [];

        return $this;
    }

    /**
     * @return array
     */
    public function cloneAsArray() {
        return $this->aItems;
    }

    /**
     * @return int
     */
    public function count() {
        return \count($this->aItems);
    }

    /**
     * @return array
     */
    public function &getAsArray() {
        return $this->aItems;
    }

    /**
     * @param mixed $mCallback
     */
    public function mapList($mCallback) {
        $aResult = [];
        if (\is_callable($mCallback)) {
            foreach ($this->aItems as $oItem) {
                $aResult[] = \call_user_func($mCallback, $oItem);
            }
        }

        return $aResult;
    }

    /**
     * @param mixed $mCallback
     *
     * @return array
     */
    public function filterList($mCallback) {
        $aResult = [];
        if (\is_callable($mCallback)) {
            foreach ($this->aItems as $oItem) {
                if (\call_user_func($mCallback, $oItem)) {
                    $aResult[] = $oItem;
                }
            }
        }

        return $aResult;
    }

    /**
     * @param mixed $mCallback
     *
     * @return void
     */
    public function foreachList($mCallback) {
        if (\is_callable($mCallback)) {
            foreach ($this->aItems as $oItem) {
                \call_user_func($mCallback, $oItem);
            }
        }
    }

    /**
     * @param mixed $mIndex
     *
     * @return null|mixed
     */
    public function &getByIndex($mIndex) {
        $mResult = null;
        if (\key_exists($mIndex, $this->aItems)) {
            $mResult = $this->aItems[$mIndex];
        }

        return $mResult;
    }

    /**
     * @param array $aItems
     *
     * @throws \CEmail_Client_Exception_InvalidArgumentException
     *
     * @return self
     */
    public function setAsArray($aItems) {
        if (!\is_array($aItems)) {
            throw new \CEmail_Client_Exception_InvalidArgumentException();
        }

        $this->aItems = $aItems;

        return $this;
    }
}
