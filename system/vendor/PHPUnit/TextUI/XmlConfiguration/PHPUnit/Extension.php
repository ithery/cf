<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\TextUI\XmlConfiguration\PHPUnit;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class Extension {
    /**
     * @var string
     * @psalm-var class-string
     */
    private $className;

    /**
     * @var string
     */
    private $sourceFile;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @psalm-param class-string $className
     *
     * @param mixed $className
     * @param mixed $sourceFile
     */
    public function __construct($className, $sourceFile, array $arguments) {
        $this->className = $className;
        $this->sourceFile = $sourceFile;
        $this->arguments = $arguments;
    }

    /**
     * @psalm-return class-string
     */
    public function className() {
        return $this->className;
    }

    public function hasSourceFile() {
        return $this->sourceFile !== '';
    }

    public function sourceFile() {
        return $this->sourceFile;
    }

    public function hasArguments() {
        return !empty($this->arguments);
    }

    public function arguments() {
        return $this->arguments;
    }
}
