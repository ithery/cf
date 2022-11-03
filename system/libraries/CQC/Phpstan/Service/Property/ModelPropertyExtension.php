<?php

use PHPStan\Type\ObjectType;
use PHPStan\ShouldNotHappenException;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\PropertiesClassReflectionExtension;

/**
 * @internal
 */
final class CQC_Phpstan_Service_Property_ModelPropertyExtension implements PropertiesClassReflectionExtension {
    /**
     * @var array<string, SchemaTable>
     */
    private $tables = [];

    /**
     * @var string
     */
    private $dateClass;

    /**
     * @var TypeStringResolver
     */
    private $stringResolver;

    /**
     * @var MigrationHelper
     */
    private $migrationHelper;

    /**
     * @var SquashedMigrationHelper
     */
    private $squashedMigrationHelper;

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    public function __construct(TypeStringResolver $stringResolver, MigrationHelper $migrationHelper, SquashedMigrationHelper $squashedMigrationHelper, ReflectionProvider $reflectionProvider) {
        $this->stringResolver = $stringResolver;
        $this->migrationHelper = $migrationHelper;
        $this->squashedMigrationHelper = $squashedMigrationHelper;
        $this->reflectionProvider = $reflectionProvider;
    }

    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool {
        if (!$classReflection->isSubclassOf(CModel::class)) {
            return false;
        }

        if ($classReflection->isAbstract()) {
            return false;
        }

        if ($this->hasAttribute($classReflection, $propertyName)) {
            return false;
        }

        if (CQC_Phpstan_Reflection_ReflectionHelper::hasPropertyTag($classReflection, $propertyName)) {
            return false;
        }

        if (count($this->tables) === 0) {
            // First try to create tables from squashed migrations, if there are any
            // Then scan the normal migration files for further changes to tables.
            $tables = $this->squashedMigrationHelper->initializeTables();

            $this->tables = $this->migrationHelper->initializeTables($tables);
        }

        if ($propertyName === 'id') {
            return true;
        }

        $modelName = $classReflection->getNativeReflection()->getName();

        try {
            $reflect = $this->reflectionProvider->getClass($modelName);

            /** @var CModel $modelInstance */
            $modelInstance = $reflect->getNativeReflection()->newInstanceWithoutConstructor();

            $tableName = $modelInstance->getTable();
        } catch (ClassNotFoundException|\ReflectionException $e) {
            return false;
        }

        if (!array_key_exists($tableName, $this->tables)) {
            return false;
        }

        if (!array_key_exists($propertyName, $this->tables[$tableName]->columns)) {
            return false;
        }

        $this->castPropertiesType($modelInstance);

        $column = $this->tables[$tableName]->columns[$propertyName];

        list($readableType, $writableType) = $this->getReadableAndWritableTypes($column, $modelInstance);

        $column->readableType = $readableType;
        $column->writeableType = $writableType;

        $this->tables[$tableName]->columns[$propertyName] = $column;

        return true;
    }

    public function getProperty(
        ClassReflection $classReflection,
        string $propertyName
    ): PropertyReflection {
        $modelName = $classReflection->getNativeReflection()->getName();

        try {
            $reflect = $this->reflectionProvider->getClass($modelName);

            /** @var CModel $modelInstance */
            $modelInstance = $reflect->getNativeReflection()->newInstanceWithoutConstructor();

            $tableName = $modelInstance->getTable();
        } catch (ClassNotFoundException|\ReflectionException $e) {
            // `hasProperty` should return false if there was a reflection exception.
            // so this should never happen
            throw new ShouldNotHappenException();
        }

        if ((!array_key_exists($tableName, $this->tables) || !array_key_exists($propertyName, $this->tables[$tableName]->columns))
            && $propertyName === 'id'
        ) {
            return new CQC_Phpstan_Service_Property_ModelProperty(
                $classReflection,
                $this->stringResolver->resolve($modelInstance->getKeyType()),
                $this->stringResolver->resolve($modelInstance->getKeyType())
            );
        }

        $column = $this->tables[$tableName]->columns[$propertyName];

        return new CQC_Phpstan_Service_Property_ModelProperty(
            $classReflection,
            $this->stringResolver->resolve($column->readableType),
            $this->stringResolver->resolve($column->writeableType)
        );
    }

    private function getDateClass(): string {
        if (!$this->dateClass) {
            $this->dateClass = '\CCarbon|\Carbon\Carbon';
        }

        return $this->dateClass;
    }

    /**
     * @param CModel $modelInstance
     *
     * @return string[]
     *
     * @phpstan-return array<int, string>
     */
    private function getModelDateColumns(CModel $modelInstance): array {
        $dateColumns = $modelInstance->getDates();

        if (method_exists($modelInstance, 'getDeletedAtColumn')) {
            $dateColumns[] = $modelInstance->getDeletedAtColumn();
        }

        return $dateColumns;
    }

    /**
     * @param CQC_Phpstan_Service_Property_SchemaColumn $column
     * @param CModel                                    $modelInstance
     *
     * @return string[]
     *
     * @phpstan-return array<int, string>
     */
    private function getReadableAndWritableTypes(CQC_Phpstan_Service_Property_SchemaColumn $column, CModel $modelInstance): array {
        $readableType = $column->readableType;
        $writableType = $column->writeableType;

        if (in_array($column->name, $this->getModelDateColumns($modelInstance), true)) {
            return [$this->getDateClass() . ($column->nullable ? '|null' : ''), $this->getDateClass() . '|string' . ($column->nullable ? '|null' : '')];
        }

        switch ($column->readableType) {
            case 'string':
            case 'int':
            case 'float':
                $readableType = $writableType = $column->readableType . ($column->nullable ? '|null' : '');

                break;

            case 'boolean':
            case 'bool':
                switch ((string) CF::config('database.default')) {
                    case 'sqlite':
                    case 'mysql':
                        $writableType = '0|1|bool';
                        $readableType = 'bool';

                        break;
                    default:
                        $readableType = $writableType = 'bool';

                        break;
                }

                break;
            case 'enum':
            case 'set':
                if (!$column->options) {
                    $readableType = $writableType = 'string';
                } else {
                    $readableType = $writableType = '\'' . implode('\'|\'', $column->options) . '\'';
                }

                break;

            default:
                break;
        }

        return [$readableType, $writableType];
    }

    private function castPropertiesType(CModel $modelInstance): void {
        $casts = $modelInstance->getCasts();
        foreach ($casts as $name => $type) {
            if (!array_key_exists($name, $this->tables[$modelInstance->getTable()]->columns)) {
                continue;
            }

            switch ($type) {
                case 'boolean':
                case 'bool':
                    $realType = 'boolean';

                    break;
                case 'string':
                    $realType = 'string';

                    break;
                case 'array':
                case 'json':
                    $realType = 'array';

                    break;
                case 'object':
                    $realType = 'object';

                    break;
                case 'int':
                case 'integer':
                case 'timestamp':
                    $realType = 'integer';

                    break;
                case 'real':
                case 'double':
                case 'float':
                    $realType = 'float';

                    break;
                case 'date':
                case 'datetime':
                    $realType = $this->getDateClass();

                    break;
                case 'collection':
                    $realType = CCollection::class;

                    break;
                case CModel_Casts_AsArrayObject::class:
                    $realType = ArrayObject::class;

                    break;
                case CModel_Casts_AsCollection::class:
                    $realType = '\CCollection<array-key, mixed>';

                    break;
                default:
                    $realType = class_exists($type) ? ('\\' . $type) : 'mixed';

                    break;
            }

            if ($this->tables[$modelInstance->getTable()]->columns[$name]->nullable) {
                $realType .= '|null';
            }

            $this->tables[$modelInstance->getTable()]->columns[$name]->readableType = $realType;
            $this->tables[$modelInstance->getTable()]->columns[$name]->writeableType = $realType;
        }
    }

    private function hasAttribute(ClassReflection $classReflection, string $propertyName): bool {
        if ($classReflection->hasNativeMethod('get' . cstr::studly($propertyName) . 'Attribute')) {
            return true;
        }

        $camelCase = cstr::camel($propertyName);

        if ($classReflection->hasNativeMethod($camelCase)) {
            $methodReflection = $classReflection->getNativeMethod($camelCase);

            if ($methodReflection->isPublic() || $methodReflection->isPrivate()) {
                return false;
            }

            $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();

            if (!(new ObjectType(CModel_Casts_Attribute::class))->isSuperTypeOf($returnType)->yes()) {
                return false;
            }

            return true;
        }

        return false;
    }
}
