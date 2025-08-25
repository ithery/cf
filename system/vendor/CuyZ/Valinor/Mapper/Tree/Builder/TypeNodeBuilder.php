<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Mapper\Tree\Builder;

use CuyZ\Valinor\Mapper\Tree\Shell;
use CuyZ\Valinor\Type\Types\ArrayType;
use CuyZ\Valinor\Type\Types\EnumType;
use CuyZ\Valinor\Type\Types\InterfaceType;
use CuyZ\Valinor\Type\Types\IterableType;
use CuyZ\Valinor\Type\Types\ListType;
use CuyZ\Valinor\Type\Types\MixedType;
use CuyZ\Valinor\Type\Types\NativeClassType;
use CuyZ\Valinor\Type\Types\NonEmptyArrayType;
use CuyZ\Valinor\Type\Types\NonEmptyListType;
use CuyZ\Valinor\Type\Types\NullType;
use CuyZ\Valinor\Type\Types\ShapedArrayType;
use CuyZ\Valinor\Type\Types\UndefinedObjectType;
use CuyZ\Valinor\Type\Types\UnionType;

/** @internal */
final class TypeNodeBuilder implements NodeBuilder
{
    private ArrayNodeBuilder $arrayNodeBuilder;

    private ListNodeBuilder $listNodeBuilder;

    private ShapedArrayNodeBuilder $shapedArrayNodeBuilder;

    private ScalarNodeBuilder $scalarNodeBuilder;

    private UnionNodeBuilder $unionNodeBuilder;

    private NullNodeBuilder $nullNodeBuilder;

    private MixedNodeBuilder $mixedNodeBuilder;

    private UndefinedObjectNodeBuilder $undefinedObjectNodeBuilder;

    private ObjectNodeBuilder $objectNodeBuilder;


    public function __construct(
        ArrayNodeBuilder $arrayNodeBuilder,
        ListNodeBuilder $listNodeBuilder,
        ShapedArrayNodeBuilder $shapedArrayNodeBuilder,
        ScalarNodeBuilder $scalarNodeBuilder,
        UnionNodeBuilder $unionNodeBuilder,
        NullNodeBuilder $nullNodeBuilder,
        MixedNodeBuilder $mixedNodeBuilder,
        UndefinedObjectNodeBuilder $undefinedObjectNodeBuilder,
        ObjectNodeBuilder $objectNodeBuilder
    ) {
        $this->arrayNodeBuilder = $arrayNodeBuilder;
        $this->listNodeBuilder = $listNodeBuilder;
        $this->shapedArrayNodeBuilder = $shapedArrayNodeBuilder;
        $this->scalarNodeBuilder = $scalarNodeBuilder;
        $this->unionNodeBuilder = $unionNodeBuilder;
        $this->nullNodeBuilder = $nullNodeBuilder;
        $this->mixedNodeBuilder = $mixedNodeBuilder;
        $this->undefinedObjectNodeBuilder = $undefinedObjectNodeBuilder;
        $this->objectNodeBuilder = $objectNodeBuilder;
    }

    public function build(Shell $shell, RootNodeBuilder $rootBuilder): Node
    {
        $typeClass = get_class($shell->type());

        switch ($typeClass) {
            // List
            case ListType::class:
            case NonEmptyListType::class:
                $builder = $this->listNodeBuilder;
                break;

            // Array
            case ArrayType::class:
            case NonEmptyArrayType::class:
            case IterableType::class:
                $builder = $this->arrayNodeBuilder;
                break;

            // ShapedArray
            case ShapedArrayType::class:
                $builder = $this->shapedArrayNodeBuilder;
                break;

            // Union
            case UnionType::class:
                $builder = $this->unionNodeBuilder;
                break;

            // Null
            case NullType::class:
                $builder = $this->nullNodeBuilder;
                break;

            // Mixed
            case MixedType::class:
                $builder = $this->mixedNodeBuilder;
                break;

            // Undefined object
            case UndefinedObjectType::class:
                $builder = $this->undefinedObjectNodeBuilder;
                break;

            // Object
            case NativeClassType::class:
            case EnumType::class:
            case InterfaceType::class:
                $builder = $this->objectNodeBuilder;
                break;

            // Scalar
            default:
                $builder = $this->scalarNodeBuilder;
                break;
        }

        return $builder->build($shell, $rootBuilder);
    }
}
