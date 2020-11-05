<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration;

use IteratorAggregate;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class ExtensionCollection implements IteratorAggregate
{
    /**
     * @var Extension[]
     */
    private $extensions;

    /**
     * @param Extension[] $extensions
     */
    public static function fromArray(array $extensions)
    {
        return new self(...$extensions);
    }

    private function __construct(Extension ...$extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * @return Extension[]
     */
    public function asArray()
    {
        return $this->extensions;
    }

    public function getIterator()
    {
        return new ExtensionCollectionIterator($this);
    }
}
