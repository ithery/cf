<?php

final class CCarbon_Context {
    /**
     * @param mixed $context
     * @param mixed $mixin
     *
     * @return bool
     */
    public static function isNotMixin($context, $mixin) {
        return $context !== $mixin && !$context instanceof CCarbon_Mixin;
    }
}
