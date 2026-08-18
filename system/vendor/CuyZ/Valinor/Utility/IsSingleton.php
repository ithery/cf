<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Utility;

/** @internal */
trait IsSingleton
{
    private static self $instance;

    /**
     * @return static
     */
    public static function get()
    {
        return self::$instance ??= new static();
    }
}
