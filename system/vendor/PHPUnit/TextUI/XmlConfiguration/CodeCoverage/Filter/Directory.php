<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Filter;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class Directory
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $suffix;

    /**
     * @var string
     */
    private $group;

    public function __construct($path, $prefix, $suffix, $group)
    {
        $this->path   = $path;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->group  = $group;
    }

    public function path()
    {
        return $this->path;
    }

    public function prefix()
    {
        return $this->prefix;
    }

    public function suffix()
    {
        return $this->suffix;
    }

    public function group()
    {
        return $this->group;
    }
}
