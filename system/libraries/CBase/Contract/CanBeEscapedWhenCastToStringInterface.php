<?php
interface CBase_Contract_CanBeEscapedWhenCastToStringInterface {
    /**
     * Indicate that the object's string representation should be escaped when __toString is invoked.
     *
     * @param bool $escape
     *
     * @return $this
     */
    public function escapeWhenCastingToString($escape = true);
}
