<?php
use DebugBar\DataCollector\MessagesCollector;

trait CDebug_DebugBar_DebugBarTrait_MessagesCollectorTrait {
    /**
     * Create and setup MemoryCollector.
     *
     * @return null|\DebugBar\DataCollector\MessagesCollector
     */
    public function setupMessagesCollector() {
        /** @var CDebug_DebugBar $this */
        if ($this->shouldCollect('messages', true)) {
            $messagesCollector = new MessagesCollector();
            $this->addCollector($messagesCollector);

            return $messagesCollector;
        }

        return null;
    }
}
