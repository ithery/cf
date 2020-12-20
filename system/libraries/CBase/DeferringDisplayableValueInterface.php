<?php

interface CBase_DeferringDisplayableValueInterface {
    /**
     * Resolve the displayable value that the class is deferring.
     *
     * @return CInterface_Htmlable|string
     */
    public function resolveDisplayableValue();
}
