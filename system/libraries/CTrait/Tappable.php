<?php

trait CTrait_Tappable {
    /**
     * Call the given Closure with this instance then return the instance.
     *
     * @param callable|null $callback
     *
     * @return mixed
     */
    public function tap($callback = null) {
        return c::tap($this, $callback);
    }
}
