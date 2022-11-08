<?php

use PHPStan\Type\Type;
use PHPStan\Type\ObjectType;
use PHPStan\Analyser\NameScope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\Type\Generic\GenericObjectType;
use PHPStan\PhpDoc\TypeNodeResolverExtension;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;

class CQC_Phpstan_Service_Type_GenericModelQueryTypeNodeResolverExtension implements TypeNodeResolverExtension {
    /**
     * @var ReflectionProvider
     */
    private $provider;

    /**
     * @param ReflectionProvider $provider
     */
    public function __construct(ReflectionProvider $provider) {
        $this->provider = $provider;
    }

    public function resolve(TypeNode $typeNode, NameScope $nameScope): ?Type {
        if (!$typeNode instanceof UnionTypeNode || count($typeNode->types) !== 2) {
            return null;
        }

        $modelTypeNode = null;
        $builderTypeNode = null;
        foreach ($typeNode->types as $innerTypeNode) {
            if ($innerTypeNode instanceof IdentifierTypeNode
                && $this->provider->hasClass($nameScope->resolveStringName($innerTypeNode->name))
                && (new ObjectType(CModel::class))->isSuperTypeOf(new ObjectType($nameScope->resolveStringName($innerTypeNode->name)))->yes()
            ) {
                $modelTypeNode = $innerTypeNode;

                continue;
            }

            if ($innerTypeNode instanceof IdentifierTypeNode
                && $this->provider->hasClass($nameScope->resolveStringName($innerTypeNode->name))
                && ($nameScope->resolveStringName($innerTypeNode->name) === CModel_Query::class || (new ObjectType(CModel_Query::class))->isSuperTypeOf(new ObjectType($nameScope->resolveStringName($innerTypeNode->name)))->yes())
            ) {
                $builderTypeNode = $innerTypeNode;
            }
        }

        if ($modelTypeNode === null || $builderTypeNode === null) {
            return null;
        }

        $builderTypeName = $nameScope->resolveStringName($builderTypeNode->name);
        $modelTypeName = $nameScope->resolveStringName($modelTypeNode->name);

        return new GenericObjectType($builderTypeName, [
            new ObjectType($modelTypeName),
        ]);
    }
}
