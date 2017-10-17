<?php

/**
 * Base class representing a modeled shape.
 */
class Aws_Api_Shape extends Aws_Api_AbstractModel
{
    /**
     * Get a concrete shape for the given definition.
     *
     * @param array    $definition
     * @param ShapeMap $shapeMap
     *
     * @return mixed
     * @throws \RuntimeException if the type is invalid
     */
    public static function create(array $definition, Aws_Api_ShapeMap $shapeMap)
    {
        static $map = [
            'structure' => 'Aws_Api_StructureShape',
            'map'       => 'Aws_Api_MapShape',
            'list'      => 'Aws_Api_ListShape',
            'timestamp' => 'Aws_Api_TimestampShape',
            'integer'   => 'Aws_Api_Shape',
            'double'    => 'Aws_Api_Shape',
            'float'     => 'Aws_Api_Shape',
            'long'      => 'Aws_Api_Shape',
            'string'    => 'Aws_Api_Shape',
            'byte'      => 'Aws_Api_Shape',
            'character' => 'Aws_Api_Shape',
            'blob'      => 'Aws_Api_Shape',
            'boolean'   => 'Aws_Api_Shape'
        ];

        if (isset($definition['shape'])) {
            return $shapeMap->resolve($definition);
        }

        if (!isset($map[$definition['type']])) {
            throw new \RuntimeException('Invalid type: '
                . print_r($definition, true));
        }

        $type = $map[$definition['type']];

        return new $type($definition, $shapeMap);
    }

    /**
     * Get the type of the shape
     *
     * @return string
     */
    public function getType()
    {
        return $this->definition['type'];
    }

    /**
     * Get the name of the shape
     *
     * @return string
     */
    public function getName()
    {
        return $this->definition['name'];
    }
}
