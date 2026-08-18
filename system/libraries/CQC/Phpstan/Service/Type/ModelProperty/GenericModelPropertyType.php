<?php

use PHPStan\Type\Type;
use PHPStan\TrinaryLogic;
use PHPStan\Type\UnionType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\CompoundType;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\ClassStringType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\IsSuperTypeOfResult;
use PHPStan\Type\Generic\TemplateType;
use PHPStan\Type\ObjectWithoutClassType;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\Generic\TemplateTypeVariance;

class CQC_Phpstan_Service_Type_ModelProperty_GenericModelPropertyType extends CQC_Phpstan_Service_Type_ModelProperty_ModelPropertyType {
    /**
     * @var Type
     */
    private $type;

    public function __construct(Type $type) {
        parent::__construct();

        $this->type = $type;
    }

    public function getReferencedClasses(): array {
        return $this->getGenericType()->getReferencedClasses();
    }

    public function getGenericType(): Type {
        return $this->type;
    }

    /**
     * PHPStan 2.x mengembalikan IsSuperTypeOfResult, bukan TrinaryLogic lagi -
     * pembungkus yang membawa alasan penolakan. Tanda tangan yang tertinggal
     * di sini bukan sekadar salah tipe: PHP menolak deklarasinya, sehingga
     * setiap berkas yang memuat kelas ini mati sebelum dianalisis.
     */
    public function isSuperTypeOf(Type $type): IsSuperTypeOfResult {
        if ($type instanceof ConstantStringType) {
            return new IsSuperTypeOfResult($this->getGenericType()->hasProperty($type->getValue()), []);
        }

        if ($type instanceof self) {
            return IsSuperTypeOfResult::createYes();
        }

        if ($type instanceof parent) {
            return IsSuperTypeOfResult::createMaybe();
        }

        if ($type instanceof CompoundType) {
            return $type->isSubTypeOf($this);
        }

        return IsSuperTypeOfResult::createNo();
    }

    public function traverse(callable $cb): Type {
        $newType = $cb($this->getGenericType());

        if ($newType === $this->getGenericType()) {
            return $this;
        }

        return new self($newType);
    }

    public function inferTemplateTypes(Type $receivedType): TemplateTypeMap {
        if ($receivedType instanceof UnionType || $receivedType instanceof IntersectionType) {
            return $receivedType->inferTemplateTypesOn($this);
        }

        if ($receivedType instanceof ConstantStringType) {
            $typeToInfer = new ObjectType($receivedType->getValue());
        } elseif ($receivedType instanceof self) {
            $typeToInfer = $receivedType->type;
        } elseif ($receivedType instanceof ClassStringType) {
            $typeToInfer = $this->getGenericType();

            if ($typeToInfer instanceof TemplateType) {
                $typeToInfer = $typeToInfer->getBound();
            }

            $typeToInfer = TypeCombinator::intersect($typeToInfer, new ObjectWithoutClassType());
        } else {
            return TemplateTypeMap::createEmpty();
        }

        if (!$this->getGenericType()->isSuperTypeOf($typeToInfer)->no()) {
            return $this->getGenericType()->inferTemplateTypes($typeToInfer);
        }

        return TemplateTypeMap::createEmpty();
    }

    public function getReferencedTemplateTypes(TemplateTypeVariance $positionVariance): array {
        $variance = $positionVariance->compose(TemplateTypeVariance::createCovariant());

        return $this->getGenericType()->getReferencedTemplateTypes($variance);
    }

    /**
     * @param mixed[] $properties
     */
    public static function __set_state(array $properties): Type {
        return new self($properties['type']);
    }
}
