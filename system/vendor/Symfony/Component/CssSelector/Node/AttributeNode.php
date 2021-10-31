<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Node;

/**
 * Represents a "<selector>[<namespace>|<attribute> <operator> <value>]" node.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class AttributeNode extends AbstractNode {

    private $selector;
    private $namespace;
    private $attribute;
    private $operator;
    private $value;

    public function __construct(NodeInterface $selector, $namespace, $attribute, $operator, $value) {
        $this->selector = $selector;
        $this->namespace = $namespace;
        $this->attribute = $attribute;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function getSelector() {
        return $this->selector;
    }

    public function getNamespace() {
        return $this->namespace;
    }

    public function getAttribute() {
        return $this->attribute;
    }

    public function getOperator() {
        return $this->operator;
    }

    public function getValue() {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecificity() {
        return $this->selector->getSpecificity()->plus(new Specificity(0, 1, 0));
    }

    /**
     * {@inheritdoc}
     */
    public function __toString() {
        $attribute = $this->namespace ? $this->namespace . '|' . $this->attribute : $this->attribute;

        return 'exists' === $this->operator ? sprintf('%s[%s[%s]]', $this->getNodeName(), $this->selector, $attribute) : sprintf("%s[%s[%s %s '%s']]", $this->getNodeName(), $this->selector, $attribute, $this->operator, $this->value);
    }

}
