<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class CVendor_Firebase_Messaging_Messages implements Countable, IteratorAggregate {

    /** @var Message[] */
    private $messages;

    public function __construct(CVendor_Firebase_Messaging_MessageInterface ...$messages) {
        $this->messages = $messages;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return Generator|Message[]
     */
    public function getIterator() {
        return new ArrayIterator($this->messages);
    }

    public function count() {
        return \count($this->messages);
    }

}
