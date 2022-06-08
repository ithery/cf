<?php

trait CPeriod_Trait_FactoryTrait {
    /**
     * @param DateTimeInterface|string $start
     * @param DateTimeInterface|string $end
     * @param null|CPeriod_Precision   $precision
     * @param null|CPeriod_Boundaries  $boundaries
     * @param null|string              $format
     *
     * @return static
     */
    public static function make(
        $start,
        $end,
        $precision = null,
        $boundaries = null,
        $format = null
    ) {
        return CPeriod_Factory::make(
            $start,
            $end,
            $precision,
            $boundaries,
            $format,
        );
    }

    /**
     * @param string $string
     *
     * @return static
     */
    public static function fromString($string) {
        return CPeriod_Factory::fromString($string);
    }
}
