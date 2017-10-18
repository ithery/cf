<?php

/**
 * Represents a list shape.
 */
class Aws_Api_ListShape extends Aws_Api_Shape
{
    private $member;

    public function __construct(array $definition, Aws_Api_ShapeMap $shapeMap)
    {
        $definition['type'] = 'list';
        parent::__construct($definition, $shapeMap);
    }

    /**
     * @return Shape
     * @throws \RuntimeException if no member is specified
     */
    public function getMember()
    {
        if (!$this->member) {
            if (!isset($this->definition['member'])) {
                throw new \RuntimeException('No member attribute specified');
            }
            $this->member = Aws_Api_Shape::create(
                $this->definition['member'],
                $this->shapeMap
            );
        }

        return $this->member;
    }
}
