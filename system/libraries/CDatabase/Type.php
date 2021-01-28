<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 10:57:55 AM
 */

/**
 * The base class for mapping types.
 *
 * A Type object is obtained by calling the static {@link getType()} method.
 */
abstract class CDatabase_Type {
    const TARRAY = 'array';

    const SIMPLE_ARRAY = 'simple_array';

    const JSON_ARRAY = 'json_array';

    const JSON = 'json';

    const BIGINT = 'bigint';

    const BOOLEAN = 'boolean';

    const DATETIME = 'datetime';

    const DATETIME_IMMUTABLE = 'datetime_immutable';

    const DATETIMETZ = 'datetimetz';

    const DATETIMETZ_IMMUTABLE = 'datetimetz_immutable';

    const DATE = 'date';

    const DATE_IMMUTABLE = 'date_immutable';

    const TIME = 'time';

    const TIME_IMMUTABLE = 'time_immutable';

    const DECIMAL = 'decimal';

    const INTEGER = 'integer';

    const OBJECT = 'object';

    const SMALLINT = 'smallint';

    const STRING = 'string';

    const TEXT = 'text';

    const BINARY = 'binary';

    const BLOB = 'blob';

    const FLOAT = 'float';

    const GUID = 'guid';

    const DATEINTERVAL = 'dateinterval';

    const ENUM = 'enum';

    /**
     * Map of already instantiated type objects. One instance per type (flyweight).
     *
     * @var array
     */
    private static $_typeObjects = [];

    /**
     * The map of supported doctrine mapping types.
     *
     * @var array
     */
    private static $_typesMap = [
        self::TARRAY => CDatabase_Type_ArrayType::class,
        self::SIMPLE_ARRAY => CDatabase_Type_SimpleArrayType::class,
        self::JSON_ARRAY => CDatabase_Type_JsonArrayType::class,
        self::JSON => CDatabase_Type_JsonType::class,
        self::OBJECT => CDatabase_Type_ObjectType::class,
        self::BOOLEAN => CDatabase_Type_BooleanType::class,
        self::INTEGER => CDatabase_Type_IntegerType::class,
        self::SMALLINT => CDatabase_Type_SmallIntType::class,
        self::BIGINT => CDatabase_Type_BigIntType::class,
        self::STRING => CDatabase_Type_StringType::class,
        self::TEXT => CDatabase_Type_TextType::class,
        self::DATETIME => CDatabase_Type_DateTimeType::class,
        self::DATETIME_IMMUTABLE => CDatabase_Type_DateTimeImmutableType::class,
        self::DATETIMETZ => CDatabase_Type_DateTimeTzType::class,
        self::DATETIMETZ_IMMUTABLE => CDatabase_Type_DateTimeTzImmutableType::class,
        self::DATE => CDatabase_Type_DateType::class,
        self::DATE_IMMUTABLE => CDatabase_Type_DateImmutableType::class,
        self::TIME => CDatabase_Type_TimeType::class,
        self::TIME_IMMUTABLE => CDatabase_Type_TimeImmutableType::class,
        self::DECIMAL => CDatabase_Type_DecimalType::class,
        self::FLOAT => CDatabase_Type_FloatType::class,
        self::BINARY => CDatabase_Type_BinaryType::class,
        self::BLOB => CDatabase_Type_BlobType::class,
        self::GUID => CDatabase_Type_GuidType::class,
        self::DATEINTERVAL => CDatabase_Type_DateIntervalType::class,
        self::ENUM => CDatabase_Type_EnumType::class,
    ];

    /**
     * Prevents instantiation and forces use of the factory method.
     */
    final private function __construct() {
    }

    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param mixed              $value    the value to convert
     * @param CDatabase_Platform $platform the currently used database platform
     *
     * @return mixed the database representation of the value
     */
    public function convertToDatabaseValue($value, CDatabase_Platform $platform) {
        return $value;
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed              $value    the value to convert
     * @param CDatabase_Platform $platform the currently used database platform
     *
     * @return mixed the PHP representation of the value
     */
    public function convertToPHPValue($value, CDatabase_Platform $platform) {
        return $value;
    }

    /**
     * Gets the default length of this type.
     *
     * @param CDatabase_Platform $platform
     *
     * @return int|null
     *
     * @todo Needed?
     */
    public function getDefaultLength(CDatabase_Platform $platform) {
        return null;
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array               $fieldDeclaration the field declaration
     * @param \CDatabase_Platform $platform         the currently used database platform
     *
     * @return string
     */
    abstract public function getSQLDeclaration(array $fieldDeclaration, CDatabase_Platform $platform);

    /**
     * Gets the name of this type.
     *
     * @return string
     *
     * @todo Needed?
     */
    abstract public function getName();

    /**
     * Factory method to create type instances.
     * Type instances are implemented as flyweights.
     *
     * @param string $name the name of the type (as returned by getName())
     *
     * @return CDatabase_Type
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function getType($name) {
        if (!isset(self::$_typeObjects[$name])) {
            if (!isset(self::$_typesMap[$name])) {
                throw CDatabase_Exception::unknownColumnType($name);
            }
            self::$_typeObjects[$name] = new self::$_typesMap[$name]();
        }

        return self::$_typeObjects[$name];
    }

    /**
     * Adds a custom type to the type map.
     *
     * @param string $name      The name of the type. This should correspond to what getName() returns.
     * @param string $className the class name of the custom type
     *
     * @return void
     *
     * @throws CDatabase_Exception
     */
    public static function addType($name, $className) {
        if (isset(self::$_typesMap[$name])) {
            throw CDatabase_Exception::typeExists($name);
        }

        self::$_typesMap[$name] = $className;
    }

    /**
     * Checks if exists support for a type.
     *
     * @param string $name the name of the type
     *
     * @return bool TRUE if type is supported; FALSE otherwise
     */
    public static function hasType($name) {
        return isset(self::$_typesMap[$name]);
    }

    /**
     * Overrides an already defined type to use a different implementation.
     *
     * @param string $name
     * @param string $className
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function overrideType($name, $className) {
        if (!isset(self::$_typesMap[$name])) {
            throw CDatabase_Exception::typeNotFound($name);
        }

        if (isset(self::$_typeObjects[$name])) {
            unset(self::$_typeObjects[$name]);
        }

        self::$_typesMap[$name] = $className;
    }

    /**
     * Gets the (preferred) binding type for values of this type that
     * can be used when binding parameters to prepared statements.
     *
     * This method should return one of the {@link \Doctrine\DBAL\ParameterType} constants.
     *
     * @return int
     */
    public function getBindingType() {
        return CDatabase_ParameterType::STRING;
    }

    /**
     * Gets the types array map which holds all registered types and the corresponding
     * type class
     *
     * @return array
     */
    public static function getTypesMap() {
        return self::$_typesMap;
    }

    /**
     * @return string
     */
    public function __toString() {
        $e = explode('\\', get_class($this));

        return str_replace('Type', '', end($e));
    }

    /**
     * Does working with this column require SQL conversion functions?
     *
     * This is a metadata function that is required for example in the ORM.
     * Usage of {@link convertToDatabaseValueSQL} and
     * {@link convertToPHPValueSQL} works for any type and mostly
     * does nothing. This method can additionally be used for optimization purposes.
     *
     * @return bool
     */
    public function canRequireSQLConversion() {
        return false;
    }

    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a database value.
     *
     * @param string             $sqlExpr
     * @param CDatabase_Platform $platform
     *
     * @return string
     */
    public function convertToDatabaseValueSQL($sqlExpr, CDatabase_Platform $platform) {
        return $sqlExpr;
    }

    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a PHP value.
     *
     * @param string             $sqlExpr
     * @param CDatabase_Platform $platform
     *
     * @return string
     */
    public function convertToPHPValueSQL($sqlExpr, $platform) {
        return $sqlExpr;
    }

    /**
     * Gets an array of database types that map to this Doctrine type.
     *
     * @param CDatabase_Platform $platform
     *
     * @return array
     */
    public function getMappedDatabaseTypes(CDatabase_Platform $platform) {
        return [];
    }

    /**
     * If this Doctrine Type maps to an already mapped database type,
     * reverse schema engineering can't tell them apart. You need to mark
     * one of those types as commented, which will have Doctrine use an SQL
     * comment to typehint the actual Doctrine Type.
     *
     * @param CDatabase_Platform $platform
     *
     * @return bool
     */
    public function requiresSQLCommentHint(CDatabase_Platform $platform) {
        return false;
    }
}
