<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Definition;

use CuyZ\Valinor\Type\Type;

/** @internal */
final class FunctionDefinition
{
    /**
     * @var string non-empty-string
     */
    public $name;

    /**
     * @var string non-empty-string
     */
    public $signature;

    /**
     * @var Attributes
     */
    public $attributes;

    /**
     * @var string|null non-empty-string|null
     */
    public $fileName;

    /**
     * @var string|null class-string|null
     */
    public $class;

    /**
     * @var bool
     */
    public $isStatic;

    /**
     * @var bool
     */
    public $isClosure;

    /**
     * @var Parameters
     */
    public $parameters;

    /**
     * @var Type
     */
    public $returnType;

    /**
     * @param string $name non-empty-string
     * @param string $signature non-empty-string
     * @param Attributes $attributes
     * @param string|null $fileName non-empty-string|null
     * @param string|null $class class-string|null
     * @param bool $isStatic
     * @param bool $isClosure
     * @param Parameters $parameters
     * @param Type $returnType
     */
    public function __construct(
        $name,
        $signature,
        Attributes $attributes,
        $fileName,
        $class,
        $isStatic,
        $isClosure,
        Parameters $parameters,
        Type $returnType
    ) {
        $this->name = $name;
        $this->signature = $signature;
        $this->attributes = $attributes;
        $this->fileName = $fileName;
        $this->class = $class;
        $this->isStatic = $isStatic;
        $this->isClosure = $isClosure;
        $this->parameters = $parameters;
        $this->returnType = $returnType;
    }
}
