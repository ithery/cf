<?php

/**
 * @implements IteratorAggregate<TopicSubscription>
 */
final class CVendor_Firebase_Messaging_TopicSubscriptions implements Countable, IteratorAggregate {
    /**
     * @var CVendor_Firebase_Messaging_TopicSubscription[]
     */
    private $subscriptions;

    public function __construct(CVendor_Firebase_Messaging_TopicSubscription ...$subscriptions) {
        $this->subscriptions = $subscriptions;
    }

    public function filter(callable $filter) {
        return new self(...\array_filter($this->subscriptions, $filter));
    }

    /**
     * @codeCoverageIgnore
     *
     * @return Traversable<CVendor_Firebase_Messaging_TopicSubscription>|CVendor_Firebase_Messaging_TopicSubscription[]
     */
    public function getIterator() {
        yield from $this->subscriptions;
    }

    /**
     * @return int
     */
    public function count() {
        return \count($this->subscriptions);
    }
}
