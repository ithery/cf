<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 3:07:42 PM
 */

/**
 * Type that maps an SQL DECIMAL to a PHP string.
 */
class CDatabase_Type_EnumType extends CDatabase_Type {
    /**
     * @var string
     */
    protected $enumClass = Enum::class;

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName() {
        return CDatabase_Type::ENUM;
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array            $fieldDeclaration The field declaration.
     * @param AbstractPlatform $platform         The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform) {
        return $platform->getVarcharTypeDeclarationSQL([]);
    }

    /**
     * @param string|null      $value
     * @param AbstractPlatform $platform
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        if ($value === null) {
            return null;
        }
        // If the enumeration provides a casting method, apply it
        if (\method_exists($this->enumClass, 'castValueIn')) {
            /** @var callable $castValueIn */
            $castValueIn = [$this->enumClass, 'castValueIn'];
            $value = $castValueIn($value);
        }
        // Check if the value is valid for this enumeration
        /** @var callable $isValidCallable */
        $isValidCallable = [$this->enumClass, 'isValid'];
        $isValid = $isValidCallable($value);
        if (!$isValid) {
            /** @var callable $toArray */
            $toArray = [$this->enumClass, 'toArray'];
            throw new InvalidArgumentException(\sprintf(
                'The value "%s" is not valid for the enum "%s". Expected one of ["%s"]',
                $value,
                $this->enumClass,
                \implode('", "', $toArray())
            ));
        }
        return new $this->enumClass($value);
    }

    public function convertToDatabaseValue($value, CDatabase_Platform $platform) {
        if ($value === null) {
            return null;
        }
        // If the enumeration provides a casting method, apply it
        if (\method_exists($this->enumClass, 'castValueOut')) {
            /** @var callable $castValueOut */
            $castValueOut = [$this->enumClass, 'castValueOut'];
            return $castValueOut($value);
        }
        // Otherwise, cast to string
        return (string) $value;
    }

    /**
     * @param string      $typeNameOrEnumClass
     * @param string|null $enumClass
     *
     * @throws InvalidArgumentException
     * @throws DBALException
     */
    public static function registerEnumType($typeNameOrEnumClass, $enumClass = null) {
        $typeName = $typeNameOrEnumClass;
        $enumClass = $enumClass ?: $typeNameOrEnumClass;
        if (!\is_subclass_of($enumClass, Enum::class)) {
            throw new InvalidArgumentException(\sprintf(
                'Provided enum class "%s" is not valid. Enums must extend "%s"',
                $enumClass,
                Enum::class
            ));
        }
        // Register and customize the type
        self::addType($typeName, static::class);
        /** @var PhpEnumType $type */
        $type = self::getType($typeName);
        $type->name = $typeName;
        $type->enumClass = $enumClass;
    }

    /**
     * @param array $types
     *
     * @throws InvalidArgumentException
     * @throws DBALException
     */
    public static function registerEnumTypes(array $types) {
        foreach ($types as $typeName => $enumClass) {
            $typeName = \is_string($typeName) ? $typeName : $enumClass;
            static::registerEnumType($typeName, $enumClass);
        }
    }

    /**
     * @param AbstractPlatform $platform
     *
     * @return bool
     */
    public function requiresSQLCommentHint(CDatabase_Platform $platform) {
        return true;
    }
}
