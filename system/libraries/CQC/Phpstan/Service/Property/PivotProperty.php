<?php

use PHPStan\Type\Type;
use PHPStan\TrinaryLogic;
use PHPStan\Type\ObjectType;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertyReflection;

class CQC_Phpstan_Service_Property_PivotProperty implements PropertyReflection {
    /**
     * @var ClassReflection
     */
    private $declaringClass;

    /**
     * @var bool
     */
    private $writeable;

    public function __construct(ClassReflection $declaringClass) {
        $this->declaringClass = $declaringClass;
    }

    public function getDeclaringClass(): ClassReflection {
        return $this->declaringClass;
    }

    public function isStatic(): bool {
        return false;
    }

    public function isPrivate(): bool {
        return false;
    }

    public function isPublic(): bool {
        return true;
    }

    public function isReadable(): bool {
        return true;
    }

    public function isWritable(): bool {
        return false;
    }

    public function getDocComment(): ?string {
        return null;
    }

    public function getReadableType(): Type {
        return new ObjectType(CModel_Relation_Pivot::class);
    }

    public function getWritableType(): Type {
        return new ObjectType(CModel_Relation_Pivot::class);
    }

    public function canChangeTypeAfterAssignment(): bool {
        return false;
    }

    public function isDeprecated(): TrinaryLogic {
        return TrinaryLogic::createNo();
    }

    public function getDeprecatedDescription(): ?string {
        return null;
    }

    public function isInternal(): TrinaryLogic {
        return TrinaryLogic::createNo();
    }
}
