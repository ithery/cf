<?php
trait CExporter_Trait_ExtendedQueueableTrait {
    use CQueue_Trait_QueueableTrait {
        chain as originalChain;
    }

    /**
     * @param $chain
     *
     * @return $this
     */
    public function chain($chain) {
        c::collect($chain)->each(function ($job) {
            $serialized = method_exists($this, 'serializeJob') ? $this->serializeJob($job) : serialize($job);
            $this->chained[] = $serialized;
        });

        return $this;
    }
}
