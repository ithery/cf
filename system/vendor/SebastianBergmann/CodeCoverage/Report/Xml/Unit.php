<?php
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Report\Xml;

use DOMElement;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Unit
{
    /**
     * @var DOMElement
     */
    private $contextNode;

    public function __construct(DOMElement $context, $name)
    {
        $this->contextNode = $context;

        $this->setName($name);
    }

    public function setLines($start, int $executable, int $executed)
    {
        $this->contextNode->setAttribute('start', (string) $start);
        $this->contextNode->setAttribute('executable', (string) $executable);
        $this->contextNode->setAttribute('executed', (string) $executed);
    }

    public function setCrap(float $crap)
    {
        $this->contextNode->setAttribute('crap', (string) $crap);
    }

    public function setNamespace($namespace)
    {
        $node = $this->contextNode->getElementsByTagNameNS(
            'https://schema.phpunit.de/coverage/1.0',
            'namespace'
        )->item(0);

        if (!$node) {
            $node = $this->contextNode->appendChild(
                $this->contextNode->ownerDocument->createElementNS(
                    'https://schema.phpunit.de/coverage/1.0',
                    'namespace'
                )
            );
        }

        $node->setAttribute('name', $namespace);
    }

    public function addMethod($name)
    {
        $node = $this->contextNode->appendChild(
            $this->contextNode->ownerDocument->createElementNS(
                'https://schema.phpunit.de/coverage/1.0',
                'method'
            )
        );

        return new Method($node, $name);
    }

    private function setName($name)
    {
        $this->contextNode->setAttribute('name', $name);
    }
}
