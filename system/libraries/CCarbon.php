<?php

defined('SYSPATH') or die('No direct access allowed.');

use Ramsey\Uuid\Uuid;
use Symfony\Component\Uid\Ulid;

use Carbon\Carbon as BaseCarbon;
use Carbon\CarbonImmutable as BaseCarbonImmutable;

class CCarbon extends BaseCarbon {
    use CTrait_Conditionable;

    /**
     * @inheritdoc
     */
    public static function setTestNow($testNow = null) {
        BaseCarbon::setTestNow($testNow);
        BaseCarbonImmutable::setTestNow($testNow);
    }

    /**
     * Create a Carbon instance from a given ordered UUID or ULID.
     *
     * @param \Ramsey\Uuid\Uuid|\Symfony\Component\Uid\Ulid|string $id
     *
     * @return \CCarbon
     */
    public static function createFromId($id) {
        return Ulid::isValid($id)
            ? static::createFromInterface(Ulid::fromString($id)->getDateTime())
            : static::createFromInterface(Uuid::fromString($id)->getDateTime());
    }

    /**
     * Dump the instance and end the script.
     *
     * @param mixed ...$args
     *
     * @return void
     */
    public function dd(...$args) {
        cdbg::dd($this, ...$args);
    }

    /**
     * Dump the instance.
     *
     * @return $this
     */
    public function dump() {
        c::dump($this);

        return $this;
    }
}
