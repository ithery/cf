<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\TextUI\XmlConfiguration\Group;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class Groups {
    /**
     * @var GroupCollection
     */
    private $include;

    /**
     * @var GroupCollection
     */
    private $exclude;

    public function __construct(GroupCollection $include, GroupCollection $exclude) {
        $this->include = $include;
        $this->exclude = $exclude;
    }

    public function hasInclude() {
        return !$this->include->isEmpty();
    }

    public function getInclude() {
        return $this->include;
    }

    public function hasExclude() {
        return !$this->exclude->isEmpty();
    }

    public function exclude() {
        return $this->exclude;
    }
}
