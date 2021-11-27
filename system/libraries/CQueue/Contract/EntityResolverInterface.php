<?php

interface CQueue_Contract_EntityResolverInterface {
    /**
     * Resolve the entity for the given ID.
     *
     * @param string $type
     * @param mixed  $id
     *
     * @return mixed
     */
    public function resolve($type, $id);
}
