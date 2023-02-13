<?php

use PHPStan\Type\Type;
use PHPStan\Type\ErrorType;
use PHPStan\Type\NeverType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Analyser\NameScope;
use PHPStan\PhpDoc\TypeNodeResolver;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDoc\TypeNodeResolverExtension;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;

/**
 * Ensures a 'model-property' type in PHPDoc is recognised to be of type ModelPropertyType.
 */
class CQC_Phpstan_Service_Type_ModelProperty_ModelPropertyTypeNodeResolverExtension implements TypeNodeResolverExtension {
    /**
     * @var bool
     */
    protected $active;

    /**
     * @var TypeNodeResolver
     */
    protected $baseResolver;

    public function __construct(TypeNodeResolver $baseResolver, bool $active) {
        $this->baseResolver = $baseResolver;
        $this->active = $active;
    }

    public function resolve(TypeNode $typeNode, NameScope $nameScope): ?Type {
        if ($typeNode instanceof IdentifierTypeNode && $typeNode->name === 'model-property') {
            return $this->active ? new CQC_Phpstan_Service_Type_ModelProperty_ModelPropertyType() : new StringType();
        }

        if ($typeNode instanceof GenericTypeNode && $typeNode->type->name === 'model-property') {
            if (!$this->active) {
                return new StringType();
            }

            if (count($typeNode->genericTypes) !== 1) {
                return new ErrorType();
            }

            $genericType = $this->baseResolver->resolve($typeNode->genericTypes[0], $nameScope);

            if ((new ObjectType(CModel::class))->isSuperTypeOf($genericType)->no()) {
                return new ErrorType();
            }

            if ($genericType instanceof NeverType) {
                return new ErrorType();
            }

            return new CQC_Phpstan_Service_Type_ModelProperty_GenericModelPropertyType($genericType);
        }

        return null;
    }
}
