<?php

/**
 * Represents a map shape.
 */
class Aws_Api_MapShape extends Aws_Api_Shape
{
    /** @var Shape */
    private $value;

    /** @var Shape */
    private $key;

    public function __construct(array $definition, Aws_Api_ShapeMap $shapeMap)
    {
        $definition['type'] = 'map';
        parent::__construct($definition, $shapeMap);
    }

    /**
     * @return Shape
     * @throws \RuntimeException if no value is specified
     */
    public function getValue()
    {
        if (!$this->value) {
            if (!isset($this->definition['value'])) {
                throw new \RuntimeException('No value specified');
            }

            $this->value = Shape::create(
                $this->definition['value'],
                $this->shapeMap
            );
        }

        return $this->value;
    }

    /**
     * @return Shape
     */
    public function getKey()
    {
        if (!$this->key) {
            $this->key = isset($this->definition['key'])
                ? Aws_Api_Shape::create($this->definition['key'], $this->shapeMap)
                : new Aws_Api_Shape(['type' => 'string'], $this->shapeMap);
        }

        return $this->key;
    }
}
