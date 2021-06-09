<?php

final class CVendor_Firebase_Messaging_Messages implements Countable, IteratorAggregate {
    /** @var CVendor_Firebase_Messaging_MessageInterface[] */
    private $messages;

    public function __construct(CVendor_Firebase_Messaging_MessageInterface ...$messages) {
        $this->messages = $messages;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return Generator|CVendor_Firebase_Messaging_MessageInterface[]
     */
    public function getIterator() {
        return new ArrayIterator($this->messages);
    }

    public function count() {
        return \count($this->messages);
    }
}
