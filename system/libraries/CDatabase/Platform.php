<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 8:57:18 AM
 */
abstract class CDatabase_Platform {
    /**
     * @var int
     */
    const CREATE_INDEXES = 1;

    /**
     * @var int
     */
    const CREATE_FOREIGNKEYS = 2;

    /**
     * @deprecated use CDatabase_Platform_Helper_DateIntervalUnit::INTERVAL_UNIT_SECOND
     */
    const DATE_INTERVAL_UNIT_SECOND = CDatabase_Platform_Helper_DateIntervalUnit::SECOND;

    /**
     * @deprecated use CDatabase_Platform_Helper_DateIntervalUnit::MINUTE
     */
    const DATE_INTERVAL_UNIT_MINUTE = CDatabase_Platform_Helper_DateIntervalUnit::MINUTE;

    /**
     * @deprecated use CDatabase_Platform_Helper_DateIntervalUnit::HOUR
     */
    const DATE_INTERVAL_UNIT_HOUR = CDatabase_Platform_Helper_DateIntervalUnit::HOUR;

    /**
     * @deprecated use CDatabase_Platform_Helper_DateIntervalUnit::DAY
     */
    const DATE_INTERVAL_UNIT_DAY = CDatabase_Platform_Helper_DateIntervalUnit::DAY;

    /**
     * @deprecated use CDatabase_Platform_Helper_DateIntervalUnit::WEEK
     */
    const DATE_INTERVAL_UNIT_WEEK = CDatabase_Platform_Helper_DateIntervalUnit::WEEK;

    /**
     * @deprecated use CDatabase_Platform_Helper_DateIntervalUnit::MONTH
     */
    const DATE_INTERVAL_UNIT_MONTH = CDatabase_Platform_Helper_DateIntervalUnit::MONTH;

    /**
     * @deprecated use CDatabase_Platform_Helper_DateIntervalUnit::QUARTER
     */
    const DATE_INTERVAL_UNIT_QUARTER = CDatabase_Platform_Helper_DateIntervalUnit::QUARTER;

    /**
     * @deprecated use CDatabase_Platform_Helper_DateIntervalUnit::QUARTER
     */
    const DATE_INTERVAL_UNIT_YEAR = CDatabase_Platform_Helper_DateIntervalUnit::YEAR;

    /**
     * @var int
     *
     * @deprecated use CDatabase_Platform_Helper_TrimMode::UNSPECIFIED
     */
    const TRIM_UNSPECIFIED = CDatabase_Platform_Helper_TrimMode::UNSPECIFIED;

    /**
     * @var int
     *
     * @deprecated use CDatabase_Platform_Helper_TrimMode::LEADING
     */
    const TRIM_LEADING = CDatabase_Platform_Helper_TrimMode::LEADING;

    /**
     * @var int
     *
     * @deprecated use CDatabase_Platform_Helper_TrimMode::TRAILING
     */
    const TRIM_TRAILING = CDatabase_Platform_Helper_TrimMode::TRAILING;

    /**
     * @var int
     *
     * @deprecated use CDatabase_Platform_Helper_TrimMode::BOTH
     */
    const TRIM_BOTH = CDatabase_Platform_Helper_TrimMode::BOTH;

    /**
     * @var null|array
     */
    protected $doctrineTypeMapping = null;

    /**
     * Contains a list of all columns that should generate parseable column comments for type-detection
     * in reverse engineering scenarios.
     *
     * @var null|array
     */
    protected $doctrineTypeComments = null;

    /**
     * @var CEvent_Dispatcher
     */
    protected $eventDispatcher;

    /**
     * Holds the KeywordList instance for the current platform.
     *
     * @var CDatabase_Platform_Keywords
     */
    protected $keywords;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->eventDispatcher = CEvent::dispatcher();
    }

    /**
     * Sets the EventManager used by the Platform.
     *
     * @param CDatabase_Dispatcher $eventDispatcher
     */
    public function setEventManager(CEvent_Dispatcher $eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Gets the EventManager used by the Platform.
     *
     * @return CEvent_Dispatcher
     */
    public function getEventDispatcher() {
        return $this->eventDispatcher;
    }

    /**
     * Returns the SQL snippet that declares a boolean column.
     *
     * @param array $columnDef
     *
     * @return string
     */
    abstract public function getBooleanTypeDeclarationSQL(array $columnDef);

    /**
     * Returns the SQL snippet that declares a 4 byte integer column.
     *
     * @param array $columnDef
     *
     * @return string
     */
    abstract public function getIntegerTypeDeclarationSQL(array $columnDef);

    /**
     * Returns the SQL snippet that declares an 8 byte integer column.
     *
     * @param array $columnDef
     *
     * @return string
     */
    abstract public function getBigIntTypeDeclarationSQL(array $columnDef);

    /**
     * Returns the SQL snippet that declares a 2 byte integer column.
     *
     * @param array $columnDef
     *
     * @return string
     */
    abstract public function getSmallIntTypeDeclarationSQL(array $columnDef);

    /**
     * Returns the SQL snippet that declares common properties of an integer column.
     *
     * @param array $columnDef
     *
     * @return string
     */
    abstract protected function getCommonIntegerTypeDeclarationSQL(array $columnDef);

    /**
     * Lazy load Doctrine Type Mappings.
     *
     * @return void
     */
    abstract protected function initializeDoctrineTypeMappings();

    /**
     * Initializes Doctrine Type Mappings with the platform defaults
     * and with all additional type mappings.
     *
     * @return void
     */
    private function initializeAllDoctrineTypeMappings() {
        $this->initializeDoctrineTypeMappings();

        foreach (CDatabase_Type::getTypesMap() as $typeName => $className) {
            foreach (CDatabase_Type::getType($typeName)->getMappedDatabaseTypes($this) as $dbType) {
                $this->doctrineTypeMapping[$dbType] = $typeName;
            }
        }
    }

    /**
     * Returns the SQL snippet used to declare a VARCHAR column type.
     *
     * @param array $field
     *
     * @return string
     */
    public function getVarcharTypeDeclarationSQL(array $field) {
        if (!isset($field['length'])) {
            $field['length'] = $this->getVarcharDefaultLength();
        }

        $fixed = isset($field['fixed']) ? $field['fixed'] : false;

        $maxLength = $fixed ? $this->getCharMaxLength() : $this->getVarcharMaxLength();

        if ($field['length'] > $maxLength) {
            return $this->getClobTypeDeclarationSQL($field);
        }

        return $this->getVarcharTypeDeclarationSQLSnippet($field['length'], $fixed);
    }

    /**
     * Returns the SQL snippet used to declare a BINARY/VARBINARY column type.
     *
     * @param array $field the column definition
     *
     * @return string
     */
    public function getBinaryTypeDeclarationSQL(array $field) {
        if (!isset($field['length'])) {
            $field['length'] = $this->getBinaryDefaultLength();
        }

        $fixed = isset($field['fixed']) ? $field['fixed'] : false;

        $maxLength = $this->getBinaryMaxLength();

        if ($field['length'] > $maxLength) {
            if ($maxLength > 0) {
                @trigger_error(sprintf(
                    'Binary field length %d is greater than supported by the platform (%d). Reduce the field length or use a BLOB field instead.',
                    $field['length'],
                    $maxLength
                ), E_USER_DEPRECATED);
            }

            return $this->getBlobTypeDeclarationSQL($field);
        }

        return $this->getBinaryTypeDeclarationSQLSnippet($field['length'], $fixed);
    }

    /**
     * Returns the SQL snippet to declare a GUID/UUID field.
     *
     * By default this maps directly to a CHAR(36) and only maps to more
     * special datatypes when the underlying databases support this datatype.
     *
     * @param array $field
     *
     * @return string
     */
    public function getGuidTypeDeclarationSQL(array $field) {
        $field['length'] = 36;
        $field['fixed'] = true;

        return $this->getVarcharTypeDeclarationSQL($field);
    }

    /**
     * Returns the SQL snippet to declare a JSON field.
     *
     * By default this maps directly to a CLOB and only maps to more
     * special datatypes when the underlying databases support this datatype.
     *
     * @param array $field
     *
     * @return string
     */
    public function getJsonTypeDeclarationSQL(array $field) {
        return $this->getClobTypeDeclarationSQL($field);
    }

    /**
     * @param int  $length
     * @param bool $fixed
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    protected function getVarcharTypeDeclarationSQLSnippet($length, $fixed) {
        throw CDatabase_Exception::notSupported('VARCHARs not supported by Platform.');
    }

    /**
     * Returns the SQL snippet used to declare a BINARY/VARBINARY column type.
     *
     * @param int  $length the length of the column
     * @param bool $fixed  whether the column length is fixed
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    protected function getBinaryTypeDeclarationSQLSnippet($length, $fixed) {
        throw CDatabase_Exception::notSupported('BINARY/VARBINARY column types are not supported by this platform.');
    }

    /**
     * Returns the SQL snippet used to declare a CLOB column type.
     *
     * @param array $field
     *
     * @return string
     */
    abstract public function getClobTypeDeclarationSQL(array $field);

    /**
     * Returns the SQL Snippet used to declare a BLOB column type.
     *
     * @param array $field
     *
     * @return string
     */
    abstract public function getBlobTypeDeclarationSQL(array $field);

    /**
     * Gets the name of the platform.
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Registers a doctrine type to be used in conjunction with a column type of this platform.
     *
     * @param string $dbType
     * @param string $doctrineType
     *
     * @throws CDatabase_Exception if the type is not found
     */
    public function registerDoctrineTypeMapping($dbType, $doctrineType) {
        if ($this->doctrineTypeMapping === null) {
            $this->initializeAllDoctrineTypeMappings();
        }

        if (!CDatabase_Type::hasType($doctrineType)) {
            throw CDatabase_Exception::typeNotFound($doctrineType);
        }

        $dbType = strtolower($dbType);
        $this->doctrineTypeMapping[$dbType] = $doctrineType;

        $doctrineType = CDatabase_Type::getType($doctrineType);

        if ($doctrineType->requiresSQLCommentHint($this)) {
            $this->markDoctrineTypeCommented($doctrineType);
        }
    }

    /**
     * Gets the Doctrine type that is mapped for the given database column type.
     *
     * @param string $dbType
     *
     * @throws CDatabase_Exception
     *
     * @return string
     */
    public function getDoctrineTypeMapping($dbType) {
        if ($this->doctrineTypeMapping === null) {
            $this->initializeAllDoctrineTypeMappings();
        }

        $dbType = strtolower($dbType);

        if (!isset($this->doctrineTypeMapping[$dbType])) {
            throw new CDatabase_Exception('Unknown database type ' . $dbType . ' requested, ' . get_class($this) . ' may not support it.');
        }

        return $this->doctrineTypeMapping[$dbType];
    }

    /**
     * Checks if a database type is currently supported by this platform.
     *
     * @param string $dbType
     *
     * @return bool
     */
    public function hasDoctrineTypeMappingFor($dbType) {
        if ($this->doctrineTypeMapping === null) {
            $this->initializeAllDoctrineTypeMappings();
        }

        $dbType = strtolower($dbType);

        return isset($this->doctrineTypeMapping[$dbType]);
    }

    /**
     * Initializes the Doctrine Type comments instance variable for in_array() checks.
     *
     * @return void
     */
    protected function initializeCommentedDoctrineTypes() {
        $this->doctrineTypeComments = [];

        foreach (CDatabase_Type::getTypesMap() as $typeName => $className) {
            $type = CDatabase_Type::getType($typeName);

            if ($type->requiresSQLCommentHint($this)) {
                $this->doctrineTypeComments[] = $typeName;
            }
        }
    }

    /**
     * Is it necessary for the platform to add a parsable type comment to allow reverse engineering the given type?
     *
     * @param CDatabase_Type $doctrineType
     *
     * @return bool
     */
    public function isCommentedDoctrineType(CDatabase_Type $doctrineType) {
        if ($this->doctrineTypeComments === null) {
            $this->initializeCommentedDoctrineTypes();
        }

        return in_array($doctrineType->getName(), $this->doctrineTypeComments);
    }

    /**
     * Marks this type as to be commented in ALTER TABLE and CREATE TABLE statements.
     *
     * @param string|CDatabase_Type $doctrineType
     *
     * @return void
     */
    public function markDoctrineTypeCommented($doctrineType) {
        if ($this->doctrineTypeComments === null) {
            $this->initializeCommentedDoctrineTypes();
        }

        $this->doctrineTypeComments[] = $doctrineType instanceof CDatabase_Type ? $doctrineType->getName() : $doctrineType;
    }

    /**
     * Gets the comment to append to a column comment that helps parsing this type in reverse engineering.
     *
     * @param CDatabase_Type $doctrineType
     *
     * @return string
     */
    public function getDoctrineTypeComment(CDatabase_Type $doctrineType) {
        return '(DC2Type:' . $doctrineType->getName() . ')';
    }

    /**
     * Gets the comment of a passed column modified by potential doctrine type comment hints.
     *
     * @param CDatabase_Schema_Column $column
     *
     * @return string
     */
    protected function getColumnComment(CDatabase_Schema_Column $column) {
        $comment = $column->getComment();

        if ($this->isCommentedDoctrineType($column->getType())) {
            $comment .= $this->getDoctrineTypeComment($column->getType());
        }

        return $comment;
    }

    /**
     * Gets the character used for identifier quoting.
     *
     * @return string
     */
    public function getIdentifierQuoteCharacter() {
        return '"';
    }

    /**
     * Gets the string portion that starts an SQL comment.
     *
     * @return string
     */
    public function getSqlCommentStartString() {
        return '--';
    }

    /**
     * Gets the string portion that ends an SQL comment.
     *
     * @return string
     */
    public function getSqlCommentEndString() {
        return "\n";
    }

    /**
     * Gets the maximum length of a char field.
     */
    public function getCharMaxLength() {
        return $this->getVarcharMaxLength();
    }

    /**
     * Gets the maximum length of a varchar field.
     *
     * @return int
     */
    public function getVarcharMaxLength() {
        return 4000;
    }

    /**
     * Gets the default length of a varchar field.
     *
     * @return int
     */
    public function getVarcharDefaultLength() {
        return 255;
    }

    /**
     * Gets the maximum length of a binary field.
     *
     * @return int
     */
    public function getBinaryMaxLength() {
        return 4000;
    }

    /**
     * Gets the default length of a binary field.
     *
     * @return int
     */
    public function getBinaryDefaultLength() {
        return 255;
    }

    /**
     * Gets all SQL wildcard characters of the platform.
     *
     * @return array
     */
    public function getWildcards() {
        return ['%', '_'];
    }

    /**
     * Returns the regular expression operator.
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getRegexpExpression() {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Returns the global unique identifier expression.
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     *
     * @deprecated Use application-generated UUIDs instead
     */
    public function getGuidExpression() {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Returns the SQL snippet to get the average value of a column.
     *
     * @param string $column the column to use
     *
     * @return string generated SQL including an AVG aggregate function
     */
    public function getAvgExpression($column) {
        return 'AVG(' . $column . ')';
    }

    /**
     * Returns the SQL snippet to get the number of rows (without a NULL value) of a column.
     *
     * If a '*' is used instead of a column the number of selected rows is returned.
     *
     * @param string|int $column the column to use
     *
     * @return string generated SQL including a COUNT aggregate function
     */
    public function getCountExpression($column) {
        return 'COUNT(' . $column . ')';
    }

    /**
     * Returns the SQL snippet to get the highest value of a column.
     *
     * @param string $column the column to use
     *
     * @return string generated SQL including a MAX aggregate function
     */
    public function getMaxExpression($column) {
        return 'MAX(' . $column . ')';
    }

    /**
     * Returns the SQL snippet to get the lowest value of a column.
     *
     * @param string $column the column to use
     *
     * @return string generated SQL including a MIN aggregate function
     */
    public function getMinExpression($column) {
        return 'MIN(' . $column . ')';
    }

    /**
     * Returns the SQL snippet to get the total sum of a column.
     *
     * @param string $column the column to use
     *
     * @return string generated SQL including a SUM aggregate function
     */
    public function getSumExpression($column) {
        return 'SUM(' . $column . ')';
    }

    // scalar functions

    /**
     * Returns the SQL snippet to get the md5 sum of a field.
     *
     * Note: Not SQL92, but common functionality.
     *
     * @param string $column
     *
     * @return string
     */
    public function getMd5Expression($column) {
        return 'MD5(' . $column . ')';
    }

    /**
     * Returns the SQL snippet to get the length of a text field.
     *
     * @param string $column
     *
     * @return string
     */
    public function getLengthExpression($column) {
        return 'LENGTH(' . $column . ')';
    }

    /**
     * Returns the SQL snippet to get the squared value of a column.
     *
     * @param string $column the column to use
     *
     * @return string generated SQL including an SQRT aggregate function
     */
    public function getSqrtExpression($column) {
        return 'SQRT(' . $column . ')';
    }

    /**
     * Returns the SQL snippet to round a numeric field to the number of decimals specified.
     *
     * @param string $column
     * @param int    $decimals
     *
     * @return string
     */
    public function getRoundExpression($column, $decimals = 0) {
        return 'ROUND(' . $column . ', ' . $decimals . ')';
    }

    /**
     * Returns the SQL snippet to get the remainder of the division operation $expression1 / $expression2.
     *
     * @param string $expression1
     * @param string $expression2
     *
     * @return string
     */
    public function getModExpression($expression1, $expression2) {
        return 'MOD(' . $expression1 . ', ' . $expression2 . ')';
    }

    /**
     * Returns the SQL snippet to trim a string.
     *
     * @param string      $str  the expression to apply the trim to
     * @param int         $mode the position of the trim (leading/trailing/both)
     * @param string|bool $char The char to trim, has to be quoted already. Defaults to space.
     *
     * @return string
     */
    public function getTrimExpression($str, $mode = CDatabase_Platform_TrimMode::UNSPECIFIED, $char = false) {
        $expression = '';

        switch ($mode) {
            case CDatabase_Platform_TrimMode::LEADING:
                $expression = 'LEADING ';

                break;

            case CDatabase_Platform_TrimMode::TRAILING:
                $expression = 'TRAILING ';

                break;

            case CDatabase_Platform_TrimMode::BOTH:
                $expression = 'BOTH ';

                break;
        }

        if ($char !== false) {
            $expression .= $char . ' ';
        }

        if ($mode || $char !== false) {
            $expression .= 'FROM ';
        }

        return 'TRIM(' . $expression . $str . ')';
    }

    /**
     * Returns the SQL snippet to trim trailing space characters from the expression.
     *
     * @param string $str literal string or column name
     *
     * @return string
     */
    public function getRtrimExpression($str) {
        return 'RTRIM(' . $str . ')';
    }

    /**
     * Returns the SQL snippet to trim leading space characters from the expression.
     *
     * @param string $str literal string or column name
     *
     * @return string
     */
    public function getLtrimExpression($str) {
        return 'LTRIM(' . $str . ')';
    }

    /**
     * Returns the SQL snippet to change all characters from the expression to uppercase,
     * according to the current character set mapping.
     *
     * @param string $str literal string or column name
     *
     * @return string
     */
    public function getUpperExpression($str) {
        return 'UPPER(' . $str . ')';
    }

    /**
     * Returns the SQL snippet to change all characters from the expression to lowercase,
     * according to the current character set mapping.
     *
     * @param string $str literal string or column name
     *
     * @return string
     */
    public function getLowerExpression($str) {
        return 'LOWER(' . $str . ')';
    }

    /**
     * Returns the SQL snippet to get the position of the first occurrence of substring $substr in string $str.
     *
     * @param string   $str      literal string
     * @param string   $substr   literal string to find
     * @param int|bool $startPos position to start at, beginning of string by default
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getLocateExpression($str, $substr, $startPos = false) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Returns the SQL snippet to get the current system date.
     *
     * @return string
     */
    public function getNowExpression() {
        return 'NOW()';
    }

    /**
     * Returns a SQL snippet to get a substring inside an SQL statement.
     *
     * Note: Not SQL92, but common functionality.
     *
     * SQLite only supports the 2 parameter variant of this function.
     *
     * @param string   $value  an sql string literal or column name/alias
     * @param int      $from   where to start the substring portion
     * @param null|int $length the substring portion length
     *
     * @return string
     */
    public function getSubstringExpression($value, $from, $length = null) {
        if ($length === null) {
            return 'SUBSTRING(' . $value . ' FROM ' . $from . ')';
        }

        return 'SUBSTRING(' . $value . ' FROM ' . $from . ' FOR ' . $length . ')';
    }

    /**
     * Returns a SQL snippet to concatenate the given expressions.
     *
     * Accepts an arbitrary number of string parameters. Each parameter must contain an expression.
     *
     * @return string
     */
    public function getConcatExpression() {
        return join(' || ', func_get_args());
    }

    /**
     * Returns the SQL for a logical not.
     *
     * Example:
     * <code>
     * $q = new Doctrine_Query();
     * $e = $q->expr;
     * $q->select('*')->from('table')
     *   ->where($e->eq('id', $e->not('null'));
     * </code>
     *
     * @param string $expression
     *
     * @return string the logical expression
     */
    public function getNotExpression($expression) {
        return 'NOT(' . $expression . ')';
    }

    /**
     * Returns the SQL that checks if an expression is null.
     *
     * @param string $expression the expression that should be compared to null
     *
     * @return string the logical expression
     */
    public function getIsNullExpression($expression) {
        return $expression . ' IS NULL';
    }

    /**
     * Returns the SQL that checks if an expression is not null.
     *
     * @param string $expression the expression that should be compared to null
     *
     * @return string the logical expression
     */
    public function getIsNotNullExpression($expression) {
        return $expression . ' IS NOT NULL';
    }

    /**
     * Returns the SQL that checks if an expression evaluates to a value between two values.
     *
     * The parameter $expression is checked if it is between $value1 and $value2.
     *
     * Note: There is a slight difference in the way BETWEEN works on some databases.
     * http://www.w3schools.com/sql/sql_between.asp. If you want complete database
     * independence you should avoid using between().
     *
     * @param string $expression the value to compare to
     * @param string $value1     the lower value to compare with
     * @param string $value2     the higher value to compare with
     *
     * @return string the logical expression
     */
    public function getBetweenExpression($expression, $value1, $value2) {
        return $expression . ' BETWEEN ' . $value1 . ' AND ' . $value2;
    }

    /**
     * Returns the SQL to get the arccosine of a value.
     *
     * @param string $value
     *
     * @return string
     */
    public function getAcosExpression($value) {
        return 'ACOS(' . $value . ')';
    }

    /**
     * Returns the SQL to get the sine of a value.
     *
     * @param string $value
     *
     * @return string
     */
    public function getSinExpression($value) {
        return 'SIN(' . $value . ')';
    }

    /**
     * Returns the SQL to get the PI value.
     *
     * @return string
     */
    public function getPiExpression() {
        return 'PI()';
    }

    /**
     * Returns the SQL to get the cosine of a value.
     *
     * @param string $value
     *
     * @return string
     */
    public function getCosExpression($value) {
        return 'COS(' . $value . ')';
    }

    /**
     * Returns the SQL to calculate the difference in days between the two passed dates.
     *
     * Computes diff = date1 - date2.
     *
     * @param string $date1
     * @param string $date2
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateDiffExpression($date1, $date2) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Returns the SQL to add the number of given seconds to a date.
     *
     * @param string $date
     * @param int    $seconds
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateAddSecondsExpression($date, $seconds) {
        return $this->getDateArithmeticIntervalExpression($date, '+', $seconds, CDatabase_Platform_DateIntervalUnit::SECOND);
    }

    /**
     * Returns the SQL to subtract the number of given seconds from a date.
     *
     * @param string $date
     * @param int    $seconds
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateSubSecondsExpression($date, $seconds) {
        return $this->getDateArithmeticIntervalExpression($date, '-', $seconds, CDatabase_Platform_DateIntervalUnit::SECOND);
    }

    /**
     * Returns the SQL to add the number of given minutes to a date.
     *
     * @param string $date
     * @param int    $minutes
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateAddMinutesExpression($date, $minutes) {
        return $this->getDateArithmeticIntervalExpression($date, '+', $minutes, CDatabase_Platform_DateIntervalUnit::MINUTE);
    }

    /**
     * Returns the SQL to subtract the number of given minutes from a date.
     *
     * @param string $date
     * @param int    $minutes
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateSubMinutesExpression($date, $minutes) {
        return $this->getDateArithmeticIntervalExpression($date, '-', $minutes, CDatabase_Platform_DateIntervalUnit::MINUTE);
    }

    /**
     * Returns the SQL to add the number of given hours to a date.
     *
     * @param string $date
     * @param int    $hours
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateAddHourExpression($date, $hours) {
        return $this->getDateArithmeticIntervalExpression($date, '+', $hours, CDatabase_Platform_DateIntervalUnit::HOUR);
    }

    /**
     * Returns the SQL to subtract the number of given hours to a date.
     *
     * @param string $date
     * @param int    $hours
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateSubHourExpression($date, $hours) {
        return $this->getDateArithmeticIntervalExpression($date, '-', $hours, CDatabase_Platform_DateIntervalUnit::HOUR);
    }

    /**
     * Returns the SQL to add the number of given days to a date.
     *
     * @param string $date
     * @param int    $days
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateAddDaysExpression($date, $days) {
        return $this->getDateArithmeticIntervalExpression($date, '+', $days, CDatabase_Platform_DateIntervalUnit::DAY);
    }

    /**
     * Returns the SQL to subtract the number of given days to a date.
     *
     * @param string $date
     * @param int    $days
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateSubDaysExpression($date, $days) {
        return $this->getDateArithmeticIntervalExpression($date, '-', $days, CDatabase_Platform_DateIntervalUnit::DAY);
    }

    /**
     * Returns the SQL to add the number of given weeks to a date.
     *
     * @param string $date
     * @param int    $weeks
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateAddWeeksExpression($date, $weeks) {
        return $this->getDateArithmeticIntervalExpression($date, '+', $weeks, CDatabase_Platform_DateIntervalUnit::WEEK);
    }

    /**
     * Returns the SQL to subtract the number of given weeks from a date.
     *
     * @param string $date
     * @param int    $weeks
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateSubWeeksExpression($date, $weeks) {
        return $this->getDateArithmeticIntervalExpression($date, '-', $weeks, CDatabase_Platform_DateIntervalUnit::WEEK);
    }

    /**
     * Returns the SQL to add the number of given months to a date.
     *
     * @param string $date
     * @param int    $months
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateAddMonthExpression($date, $months) {
        return $this->getDateArithmeticIntervalExpression($date, '+', $months, CDatabase_Platform_DateIntervalUnit::MONTH);
    }

    /**
     * Returns the SQL to subtract the number of given months to a date.
     *
     * @param string $date
     * @param int    $months
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateSubMonthExpression($date, $months) {
        return $this->getDateArithmeticIntervalExpression($date, '-', $months, CDatabase_Platform_DateIntervalUnit::MONTH);
    }

    /**
     * Returns the SQL to add the number of given quarters to a date.
     *
     * @param string $date
     * @param int    $quarters
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateAddQuartersExpression($date, $quarters) {
        return $this->getDateArithmeticIntervalExpression($date, '+', $quarters, CDatabase_Platform_DateIntervalUnit::QUARTER);
    }

    /**
     * Returns the SQL to subtract the number of given quarters from a date.
     *
     * @param string $date
     * @param int    $quarters
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateSubQuartersExpression($date, $quarters) {
        return $this->getDateArithmeticIntervalExpression($date, '-', $quarters, CDatabase_Platform_DateIntervalUnit::QUARTER);
    }

    /**
     * Returns the SQL to add the number of given years to a date.
     *
     * @param string $date
     * @param int    $years
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateAddYearsExpression($date, $years) {
        return $this->getDateArithmeticIntervalExpression($date, '+', $years, CDatabase_Platform_DateIntervalUnit::YEAR);
    }

    /**
     * Returns the SQL to subtract the number of given years from a date.
     *
     * @param string $date
     * @param int    $years
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateSubYearsExpression($date, $years) {
        return $this->getDateArithmeticIntervalExpression($date, '-', $years, CDatabase_Platform_DateIntervalUnit::YEAR);
    }

    /**
     * Returns the SQL for a date arithmetic expression.
     *
     * @param string $date     the column or literal representing a date to perform the arithmetic operation on
     * @param string $operator the arithmetic operator (+ or -)
     * @param int    $interval the interval that shall be calculated into the date
     * @param string $unit     The unit of the interval that shall be calculated into the date.
     *                         One of the DATE_INTERVAL_UNIT_* constants.
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    protected function getDateArithmeticIntervalExpression($date, $operator, $interval, $unit) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Returns the SQL bit AND comparison expression.
     *
     * @param string $value1
     * @param string $value2
     *
     * @return string
     */
    public function getBitAndComparisonExpression($value1, $value2) {
        return '(' . $value1 . ' & ' . $value2 . ')';
    }

    /**
     * Returns the SQL bit OR comparison expression.
     *
     * @param string $value1
     * @param string $value2
     *
     * @return string
     */
    public function getBitOrComparisonExpression($value1, $value2) {
        return '(' . $value1 . ' | ' . $value2 . ')';
    }

    /**
     * Returns the FOR UPDATE expression.
     *
     * @return string
     */
    public function getForUpdateSQL() {
        return 'FOR UPDATE';
    }

    /**
     * Honors that some SQL vendors such as MsSql use table hints for locking instead of the ANSI SQL FOR UPDATE specification.
     *
     * @param string   $fromClause the FROM clause to append the hint for the given lock mode to
     * @param null|int $lockMode   One of the Doctrine\DBAL\LockMode::* constants. If null is given, nothing will
     *                             be appended to the FROM clause.
     *
     * @return string
     */
    public function appendLockHint($fromClause, $lockMode) {
        return $fromClause;
    }

    /**
     * Returns the SQL snippet to append to any SELECT statement which locks rows in shared read lock.
     *
     * This defaults to the ANSI SQL "FOR UPDATE", which is an exclusive lock (Write). Some database
     * vendors allow to lighten this constraint up to be a real read lock.
     *
     * @return string
     */
    public function getReadLockSQL() {
        return $this->getForUpdateSQL();
    }

    /**
     * Returns the SQL snippet to append to any SELECT statement which obtains an exclusive lock on the rows.
     *
     * The semantics of this lock mode should equal the SELECT .. FOR UPDATE of the ANSI SQL standard.
     *
     * @return string
     */
    public function getWriteLockSQL() {
        return $this->getForUpdateSQL();
    }

    /**
     * Returns the SQL snippet to drop an existing database.
     *
     * @param string $database the name of the database that should be dropped
     *
     * @return string
     */
    public function getDropDatabaseSQL($database) {
        return 'DROP DATABASE ' . $database;
    }

    /**
     * Returns the SQL snippet to drop an existing table.
     *
     * @param CDatabase_Schema_Table|string $table
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getDropTableSQL($table) {
        $tableArg = $table;

        if ($table instanceof CDatabase_Schema_Table) {
            $table = $table->getQuotedName($this);
        } elseif (!is_string($table)) {
            throw new \InvalidArgumentException('getDropTableSQL() expects $table parameter to be string or CDatabase_Schema_Table.');
        }

        if (null !== $this->eventDispatcher) {
            $eventArgs = new CDatabase_Event_Schema_OnDropTable($tableArg, $this);
            $this->eventDispatcher->dispatch($eventArgs);

            if ($eventArgs->isDefaultPrevented()) {
                return $eventArgs->getSql();
            }
        }

        return 'DROP TABLE ' . $table;
    }

    /**
     * Returns the SQL to safely drop a temporary table WITHOUT implicitly committing an open transaction.
     *
     * @param CDatabase_Schema_Table|string $table
     *
     * @return string
     */
    public function getDropTemporaryTableSQL($table) {
        return $this->getDropTableSQL($table);
    }

    /**
     * Returns the SQL to drop an index from a table.
     *
     * @param CDatabase_Schema_Index|string $index
     * @param CDatabase_Schema_Table|string $table
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getDropIndexSQL($index, $table = null) {
        if ($index instanceof CDatabase_Schema_Index) {
            $index = $index->getQuotedName($this);
        } elseif (!is_string($index)) {
            throw new \InvalidArgumentException('AbstractPlatform::getDropIndexSQL() expects $index parameter to be string or CDatabase_Schema_Index.');
        }

        return 'DROP INDEX ' . $index;
    }

    /**
     * Returns the SQL to drop a constraint.
     *
     * @param CDatabase_Schema_Constraint|string $constraint
     * @param CDatabase_Schema_Table|string      $table
     *
     * @return string
     */
    public function getDropConstraintSQL($constraint, $table) {
        if (!$constraint instanceof CDatabase_Schema_Constraint) {
            $constraint = new CDatabase_Schema_Identifier($constraint);
        }

        if (!$table instanceof CDatabase_Schema_Table) {
            $table = new CDatabase_Schema_Identifier($table);
        }

        $constraint = $constraint->getQuotedName($this);
        $table = $table->getQuotedName($this);

        return 'ALTER TABLE ' . $table . ' DROP CONSTRAINT ' . $constraint;
    }

    /**
     * Returns the SQL to drop a foreign key.
     *
     * @param CDatabase_Schema_ForeignKeyConstraint|string $foreignKey
     * @param CDatabase_Schema_Table|string                $table
     *
     * @return string
     */
    public function getDropForeignKeySQL($foreignKey, $table) {
        if (!$foreignKey instanceof CDatabase_Schema_ForeignKeyConstraint) {
            $foreignKey = new CDatabase_Schema_Identifier($foreignKey);
        }

        if (!$table instanceof CDatabase_Schema_Table) {
            $table = new CDatabase_Schema_Identifier($table);
        }

        $foreignKey = $foreignKey->getQuotedName($this);
        $table = $table->getQuotedName($this);

        return 'ALTER TABLE ' . $table . ' DROP FOREIGN KEY ' . $foreignKey;
    }

    /**
     * Returns the SQL statement(s) to create a table with the specified name, columns and constraints
     * on this platform.
     *
     * @param CDatabase_Schema_Table $table
     * @param int                    $createFlags
     *
     * @throws CDatabase_Exception
     * @throws \InvalidArgumentException
     *
     * @return array the sequence of SQL statements
     */
    public function getCreateTableSQL(CDatabase_Schema_Table $table, $createFlags = self::CREATE_INDEXES) {
        if (!is_int($createFlags)) {
            throw new \InvalidArgumentException('Second argument of AbstractPlatform::getCreateTableSQL() has to be integer.');
        }

        if (count($table->getColumns()) === 0) {
            throw CDatabase_Exception::noColumnsSpecifiedForTable($table->getName());
        }

        $tableName = $table->getQuotedName($this);
        $options = $table->getOptions();
        $options['uniqueConstraints'] = [];
        $options['indexes'] = [];
        $options['primary'] = [];

        if (($createFlags & self::CREATE_INDEXES) > 0) {
            foreach ($table->getIndexes() as $index) {
                /* @var $index Index */
                if ($index->isPrimary()) {
                    $options['primary'] = $index->getQuotedColumns($this);
                    $options['primary_index'] = $index;
                } else {
                    $options['indexes'][$index->getQuotedName($this)] = $index;
                }
            }
        }

        $columnSql = [];
        $columns = [];

        foreach ($table->getColumns() as $column) {
            /** @var CDatabase_Schema_Column $column */
            if (null !== $this->eventDispatcher) {
                $eventArgs = new CDatabase_Event_Schema_OnCreateTableColumn($column, $table, $this);
                $this->eventDispatcher->dispatch($eventArgs);

                $columnSql = array_merge($columnSql, $eventArgs->getSql());

                if ($eventArgs->isDefaultPrevented()) {
                    continue;
                }
            }

            $columnData = $column->toArray();
            $columnData['name'] = $column->getQuotedName($this);
            $columnData['version'] = $column->hasPlatformOption('version') ? $column->getPlatformOption('version') : false;
            $columnData['comment'] = $this->getColumnComment($column);

            if ($columnData['type'] instanceof CDatabase_Type_StringType && $columnData['length'] === null) {
                $columnData['length'] = 255;
            }

            if (in_array($column->getName(), $options['primary'])) {
                $columnData['primary'] = true;
            }

            $columns[$columnData['name']] = $columnData;
        }

        if (($createFlags & self::CREATE_FOREIGNKEYS) > 0) {
            $options['foreignKeys'] = [];
            foreach ($table->getForeignKeys() as $fkConstraint) {
                $options['foreignKeys'][] = $fkConstraint;
            }
        }

        if (null !== $this->eventDispatcher) {
            $eventArgs = new CDatabase_Event_Schema_OnCreateTable($table, $columns, $options, $this);
            $this->eventDispatcher->dispatch($eventArgs);

            if ($eventArgs->isDefaultPrevented()) {
                return array_merge($eventArgs->getSql(), $columnSql);
            }
        }
        $sql = $this->protectedGetCreateTableSQL($tableName, $columns, $options);
        if ($this->supportsCommentOnStatement()) {
            foreach ($table->getColumns() as $column) {
                $comment = $this->getColumnComment($column);

                if (null !== $comment && '' !== $comment) {
                    $sql[] = $this->getCommentOnColumnSQL($tableName, $column->getQuotedName($this), $comment);
                }
            }
        }

        return array_merge($sql, $columnSql);
    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @param string $comment
     *
     * @return string
     */
    public function getCommentOnColumnSQL($tableName, $columnName, $comment) {
        $tableName = new CDatabase_Schema_Identifier($tableName);
        $columnName = new CDatabase_Schema_Identifier($columnName);
        $comment = $this->quoteStringLiteral($comment);

        return 'COMMENT ON COLUMN ' . $tableName->getQuotedName($this) . '.' . $columnName->getQuotedName($this)
                . ' IS ' . $comment;
    }

    /**
     * Returns the SQL to create inline comment on a column.
     *
     * @param string $comment
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getInlineColumnCommentSQL($comment) {
        if (!$this->supportsInlineColumnComments()) {
            throw CDatabase_Exception::notSupported(__METHOD__);
        }

        return 'COMMENT ' . $this->quoteStringLiteral($comment);
    }

    /**
     * Returns the SQL used to create a table.
     *
     * @param string $tableName
     * @param array  $columns
     * @param array  $options
     *
     * @return array
     */
    protected function protectedGetCreateTableSQL($tableName, array $columns, array $options = []) {
        $columnListSql = $this->getColumnDeclarationListSQL($columns);

        if (isset($options['uniqueConstraints']) && !empty($options['uniqueConstraints'])) {
            foreach ($options['uniqueConstraints'] as $name => $definition) {
                $columnListSql .= ', ' . $this->getUniqueConstraintDeclarationSQL($name, $definition);
            }
        }

        if (isset($options['primary']) && !empty($options['primary'])) {
            $columnListSql .= ', PRIMARY KEY(' . implode(', ', array_unique(array_values($options['primary']))) . ')';
        }

        if (isset($options['indexes']) && !empty($options['indexes'])) {
            foreach ($options['indexes'] as $index => $definition) {
                $columnListSql .= ', ' . $this->getIndexDeclarationSQL($index, $definition);
            }
        }

        $query = 'CREATE TABLE ' . $tableName . ' (' . $columnListSql;

        $check = $this->getCheckDeclarationSQL($columns);
        if (!empty($check)) {
            $query .= ', ' . $check;
        }
        $query .= ')';

        $sql[] = $query;

        if (isset($options['foreignKeys'])) {
            foreach ((array) $options['foreignKeys'] as $definition) {
                $sql[] = $this->getCreateForeignKeySQL($definition, $tableName);
            }
        }

        return $sql;
    }

    /**
     * @return string
     */
    public function getCreateTemporaryTableSnippetSQL() {
        return 'CREATE TEMPORARY TABLE';
    }

    /**
     * Returns the SQL to create a sequence on this platform.
     *
     * @param CDatabase_Schema_Sequence $sequence
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getCreateSequenceSQL(CDatabase_Schema_Sequence $sequence) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Returns the SQL to change a sequence on this platform.
     *
     * @param CDatabase_Schema_Sequence $sequence
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getAlterSequenceSQL(CDatabase_Schema_Sequence $sequence) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Returns the SQL to create a constraint on a table on this platform.
     *
     * @param CDatabase_Schema_Constraint   $constraint
     * @param CDatabase_Schema_Table|string $table
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getCreateConstraintSQL(CDatabase_Schema_Constraint $constraint, $table) {
        if ($table instanceof CDatabase_Schema_Table) {
            $table = $table->getQuotedName($this);
        }

        $query = 'ALTER TABLE ' . $table . ' ADD CONSTRAINT ' . $constraint->getQuotedName($this);

        $columnList = '(' . implode(', ', $constraint->getQuotedColumns($this)) . ')';

        $referencesClause = '';
        if ($constraint instanceof CDatabase_Schema_Index) {
            if ($constraint->isPrimary()) {
                $query .= ' PRIMARY KEY';
            } elseif ($constraint->isUnique()) {
                $query .= ' UNIQUE';
            } else {
                throw new \InvalidArgumentException(
                    'Can only create primary or unique constraints, no common indexes with getCreateConstraintSQL().'
                );
            }
        } elseif ($constraint instanceof CDatabase_Schema_ForeignKeyConstraint) {
            $query .= ' FOREIGN KEY';

            $referencesClause = ' REFERENCES ' . $constraint->getQuotedForeignTableName($this)
                    . ' (' . implode(', ', $constraint->getQuotedForeignColumns($this)) . ')';
        }
        $query .= ' ' . $columnList . $referencesClause;

        return $query;
    }

    /**
     * Returns the SQL to create an index on a table on this platform.
     *
     * @param CDatabase_Schema_Index        $index
     * @param CDatabase_Schema_Table|string $table the name of the table on which the index is to be created
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getCreateIndexSQL(CDatabase_Schema_Index $index, $table) {
        if ($table instanceof CDatabase_Schema_Table) {
            $table = $table->getQuotedName($this);
        }
        $name = $index->getQuotedName($this);
        $columns = $index->getQuotedColumns($this);

        if (count($columns) == 0) {
            throw new \InvalidArgumentException("Incomplete definition. 'columns' required.");
        }

        if ($index->isPrimary()) {
            return $this->getCreatePrimaryKeySQL($index, $table);
        }

        $query = 'CREATE ' . $this->getCreateIndexSQLFlags($index) . 'INDEX ' . $name . ' ON ' . $table;
        $query .= ' (' . $this->getIndexFieldDeclarationListSQL($columns) . ')' . $this->getPartialIndexSQL($index);

        return $query;
    }

    /**
     * Adds condition for partial index.
     *
     * @param CDatabase_Schema_Index $index
     *
     * @return string
     */
    protected function getPartialIndexSQL(CDatabase_Schema_Index $index) {
        if ($this->supportsPartialIndexes() && $index->hasOption('where')) {
            return ' WHERE ' . $index->getOption('where');
        }

        return '';
    }

    /**
     * Adds additional flags for index generation.
     *
     * @param CDatabase_Schema_Index $index
     *
     * @return string
     */
    protected function getCreateIndexSQLFlags(CDatabase_Schema_Index $index) {
        return $index->isUnique() ? 'UNIQUE ' : '';
    }

    /**
     * Returns the SQL to create an unnamed primary key constraint.
     *
     * @param CDatabase_Schema_Index        $index
     * @param CDatabase_Schema_Table|string $table
     *
     * @return string
     */
    public function getCreatePrimaryKeySQL(CDatabase_Schema_Index $index, $table) {
        return 'ALTER TABLE ' . $table . ' ADD PRIMARY KEY (' . $this->getIndexFieldDeclarationListSQL($index->getQuotedColumns($this)) . ')';
    }

    /**
     * Returns the SQL to create a named schema.
     *
     * @param string $schemaName
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getCreateSchemaSQL($schemaName) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Quotes a string so that it can be safely used as a table or column name,
     * even if it is a reserved word of the platform. This also detects identifier
     * chains separated by dot and quotes them independently.
     *
     * NOTE: Just because you CAN use quoted identifiers doesn't mean
     * you SHOULD use them. In general, they end up causing way more
     * problems than they solve.
     *
     * @param string $str the identifier name to be quoted
     *
     * @return string the quoted identifier string
     */
    public function quoteIdentifier($str) {
        if (strpos($str, '.') !== false) {
            $parts = array_map([$this, 'quoteSingleIdentifier'], explode('.', $str));

            return implode('.', $parts);
        }

        return $this->quoteSingleIdentifier($str);
    }

    /**
     * Quotes a single identifier (no dot chain separation).
     *
     * @param string $str the identifier name to be quoted
     *
     * @return string the quoted identifier string
     */
    public function quoteSingleIdentifier($str) {
        $c = $this->getIdentifierQuoteCharacter();

        return $c . str_replace($c, $c . $c, $str) . $c;
    }

    /**
     * Returns the SQL to create a new foreign key.
     *
     * @param CDatabase_Schema_ForeignKeyConstraint $foreignKey the foreign key constraint
     * @param CDatabase_Schema_Table|string         $table      the name of the table on which the foreign key is to be created
     *
     * @return string
     */
    public function getCreateForeignKeySQL(CDatabase_Schema_ForeignKeyConstraint $foreignKey, $table) {
        if ($table instanceof CDatabase_Schema_Table) {
            $table = $table->getQuotedName($this);
        }

        $query = 'ALTER TABLE ' . $table . ' ADD ' . $this->getForeignKeyDeclarationSQL($foreignKey);

        return $query;
    }

    /**
     * Gets the SQL statements for altering an existing table.
     *
     * This method returns an array of SQL statements, since some platforms need several statements.
     *
     * @param CDatabase_Schema_Table_Diff $diff
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return array
     */
    public function getAlterTableSQL(CDatabase_Schema_Table_Diff $diff) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * @param CDatabase_Schema_Column     $column
     * @param CDatabase_Schema_Table_Diff $diff
     * @param array                       $columnSql
     *
     * @return bool
     */
    protected function onSchemaAlterTableAddColumn(CDatabase_Schema_Column $column, CDatabase_Schema_Table_Diff $diff, &$columnSql) {
        if (null === $this->eventDispatcher) {
            return false;
        }

        $eventArgs = new CDatabase_Event_Schema_OnAlterTableAddColumn($column, $diff, $this);
        $this->eventDispatcher->dispatch($eventArgs);

        $columnSql = array_merge($columnSql, $eventArgs->getSql());

        return $eventArgs->isDefaultPrevented();
    }

    /**
     * @param CDatabase_Schema_Column     $column
     * @param CDatabase_Schema_Table_Diff $diff
     * @param array                       $columnSql
     *
     * @return bool
     */
    protected function onSchemaAlterTableRemoveColumn(CDatabase_Schema_Column $column, CDatabase_Schema_Table_Diff $diff, &$columnSql) {
        if (null === $this->eventDispatcher) {
            return false;
        }

        $eventArgs = new CDatabase_Event_Schema_OnAlterTableRemoveColumn($column, $diff, $this);
        $this->eventDispatcher->dispatch($eventArgs);

        $columnSql = array_merge($columnSql, $eventArgs->getSql());

        return $eventArgs->isDefaultPrevented();
    }

    /**
     * @param CDatabase_Schema_Column_Diff $columnDiff
     * @param CDatabase_Schema_Table_Diff  $diff
     * @param array                        $columnSql
     *
     * @return bool
     */
    protected function onSchemaAlterTableChangeColumn(CDatabase_Schema_Column_Diff $columnDiff, CDatabase_Schema_Table_Diff $diff, &$columnSql) {
        if (null === $this->eventDispatcher) {
            return false;
        }

        $eventArgs = new CDatabase_Event_Schema_OnAlterTableChangeColumn($columnDiff, $diff, $this);
        $this->eventDispatcher->dispatch($eventArgs);

        $columnSql = array_merge($columnSql, $eventArgs->getSql());

        return $eventArgs->isDefaultPrevented();
    }

    /**
     * @param string                      $oldColumnName
     * @param CDatabase_Schema_Column     $column
     * @param CDatabase_Schema_Table_Diff $diff
     * @param array                       $columnSql
     *
     * @return bool
     */
    protected function onSchemaAlterTableRenameColumn($oldColumnName, CDatabase_Schema_Column $column, CDatabase_Schema_Table_Diff $diff, &$columnSql) {
        if (null === $this->eventDispatcher) {
            return false;
        }

        $eventArgs = new CDatabase_Event_Schema_OnAlterTableRenameColumn($oldColumnName, $column, $diff, $this);
        $this->eventDispatcher->dispatch($eventArgs);

        $columnSql = array_merge($columnSql, $eventArgs->getSql());

        return $eventArgs->isDefaultPrevented();
    }

    /**
     * @param CDatabase_Schema_Table_Diff $diff
     * @param array                       $sql
     *
     * @return bool
     */
    protected function onSchemaAlterTable(CDatabase_Schema_Table_Diff $diff, &$sql) {
        if (null === $this->eventDispatcher) {
            return false;
        }

        $eventArgs = new CDatabase_Event_Schema_OnAlterTable($diff, $this);
        $this->eventDispatcher->dispatch($eventArgs);

        $sql = array_merge($sql, $eventArgs->getSql());

        return $eventArgs->isDefaultPrevented();
    }

    /**
     * @param CDatabase_Schema_Table_Diff $diff
     *
     * @return array
     */
    protected function getPreAlterTableIndexForeignKeySQL(CDatabase_Schema_Table_Diff $diff) {
        $tableName = $diff->getName($this)->getQuotedName($this);

        $sql = [];
        if ($this->supportsForeignKeyConstraints()) {
            foreach ($diff->removedForeignKeys as $foreignKey) {
                $sql[] = $this->getDropForeignKeySQL($foreignKey, $tableName);
            }
            foreach ($diff->changedForeignKeys as $foreignKey) {
                $sql[] = $this->getDropForeignKeySQL($foreignKey, $tableName);
            }
        }

        foreach ($diff->removedIndexes as $index) {
            $sql[] = $this->getDropIndexSQL($index, $tableName);
        }
        foreach ($diff->changedIndexes as $index) {
            $sql[] = $this->getDropIndexSQL($index, $tableName);
        }

        return $sql;
    }

    /**
     * @param CDatabase_Schema_Table_Diff $diff
     *
     * @return array
     */
    protected function getPostAlterTableIndexForeignKeySQL(CDatabase_Schema_Table_Diff $diff) {
        $tableName = (false !== $diff->newName) ? $diff->getNewName()->getQuotedName($this) : $diff->getName($this)->getQuotedName($this);

        $sql = [];

        if ($this->supportsForeignKeyConstraints()) {
            foreach ($diff->addedForeignKeys as $foreignKey) {
                $sql[] = $this->getCreateForeignKeySQL($foreignKey, $tableName);
            }

            foreach ($diff->changedForeignKeys as $foreignKey) {
                $sql[] = $this->getCreateForeignKeySQL($foreignKey, $tableName);
            }
        }

        foreach ($diff->addedIndexes as $index) {
            $sql[] = $this->getCreateIndexSQL($index, $tableName);
        }

        foreach ($diff->changedIndexes as $index) {
            $sql[] = $this->getCreateIndexSQL($index, $tableName);
        }

        foreach ($diff->renamedIndexes as $oldIndexName => $index) {
            $oldIndexName = new CDatabase_Schema_Identifier($oldIndexName);
            $sql = array_merge(
                $sql,
                $this->getRenameIndexSQL($oldIndexName->getQuotedName($this), $index, $tableName)
            );
        }

        return $sql;
    }

    /**
     * Returns the SQL for renaming an index on a table.
     *
     * @param string                 $oldIndexName the name of the index to rename from
     * @param CDatabase_Schema_Index $index        the definition of the index to rename to
     * @param string                 $tableName    the table to rename the given index on
     *
     * @return array the sequence of SQL statements for renaming the given index
     */
    protected function getRenameIndexSQL($oldIndexName, CDatabase_Schema_Index $index, $tableName) {
        return [
            $this->getDropIndexSQL($oldIndexName, $tableName),
            $this->getCreateIndexSQL($index, $tableName)
        ];
    }

    /**
     * Common code for alter table statement generation that updates the changed Index and Foreign Key definitions.
     *
     * @param CDatabase_Schema_Table_Diff $diff
     *
     * @return array
     */
    protected function getAlterTableIndexForeignKeySQL(CDatabase_Schema_Table_Diff $diff) {
        return array_merge($this->getPreAlterTableIndexForeignKeySQL($diff), $this->getPostAlterTableIndexForeignKeySQL($diff));
    }

    /**
     * Gets declaration of a number of fields in bulk.
     *
     * @param array $fields A multidimensional associative array.
     *                      The first dimension determines the field name, while the second
     *                      dimension is keyed with the name of the properties
     *                      of the field being declared as array indexes. Currently, the types
     *                      of supported field properties are as follows:
     *
     *                      length
     *                      Integer value that determines the maximum length of the text
     *                      field. If this argument is missing the field should be
     *                      declared to have the longest length allowed by the DBMS.
     *
     *                      default
     *                      Text value to be used as default for this field.
     *
     *                      notnull
     *                      Boolean flag that indicates whether this field is constrained
     *                      to not be set to null.
     *
     *                      charset
     *                      Text value with the default CHARACTER SET for this field.
     *
     *                      collation
     *                      Text value with the default COLLATION for this field.
     *
     *                      unique
     *                      unique constraint
     *
     * @return string
     */
    public function getColumnDeclarationListSQL(array $fields) {
        $queryFields = [];

        foreach ($fields as $fieldName => $field) {
            $queryFields[] = $this->getColumnDeclarationSQL($fieldName, $field);
        }

        return implode(', ', $queryFields);
    }

    public function getColumnDeclarationSQL($name, array $field) {
        if (isset($field['columnDefinition'])) {
            $columnDef = $this->getCustomTypeDeclarationSQL($field);
        } else {
            $default = $this->getDefaultValueDeclarationSQL($field);

            $charset = (isset($field['charset']) && $field['charset'])
                    ? ' ' . $this->getColumnCharsetDeclarationSQL($field['charset']) : '';

            $collation = (isset($field['collation']) && $field['collation'])
                    ? ' ' . $this->getColumnCollationDeclarationSQL($field['collation']) : '';

            $notnull = (isset($field['notnull']) && $field['notnull']) ? ' NOT NULL' : '';

            $unique = (isset($field['unique']) && $field['unique'])
                    ? ' ' . $this->getUniqueFieldDeclarationSQL() : '';

            $check = (isset($field['check']) && $field['check'])
                    ? ' ' . $field['check'] : '';

            $typeDecl = $field['type']->getSQLDeclaration($field, $this);
            $columnDef = $typeDecl . $charset . $default . $notnull . $unique . $check . $collation;

            if ($this->supportsInlineColumnComments() && isset($field['comment']) && $field['comment'] !== '') {
                $columnDef .= ' ' . $this->getInlineColumnCommentSQL($field['comment']);
            }
        }

        return $name . ' ' . $columnDef;
    }

    /**
     * Returns the SQL snippet that declares a floating point column of arbitrary precision.
     *
     * @param array $columnDef
     *
     * @return string
     */
    public function getDecimalTypeDeclarationSQL(array $columnDef) {
        $columnDef['precision'] = (!isset($columnDef['precision']) || empty($columnDef['precision'])) ? 10 : $columnDef['precision'];
        $columnDef['scale'] = (!isset($columnDef['scale']) || empty($columnDef['scale'])) ? 0 : $columnDef['scale'];

        return 'NUMERIC(' . $columnDef['precision'] . ', ' . $columnDef['scale'] . ')';
    }

    /**
     * Obtains DBMS specific SQL code portion needed to set a default value
     * declaration to be used in statements like CREATE TABLE.
     *
     * @param array $field the field definition array
     *
     * @return string DBMS specific SQL code portion needed to set a default value
     */
    public function getDefaultValueDeclarationSQL($field) {
        if (!isset($field['default'])) {
            return empty($field['notnull']) ? ' DEFAULT NULL' : '';
        }

        $default = $field['default'];

        if (!isset($field['type'])) {
            return " DEFAULT '" . $default . "'";
        }

        $type = $field['type'];

        if ($type instanceof CDatabase_Type_Interface_PhpIntegerMappingTypeInterface) {
            return ' DEFAULT ' . $default;
        }

        if ($type instanceof CDatabase_Type_Interface_PhpDateTimeMappingTypeInterface && $default === $this->getCurrentTimestampSQL()) {
            return ' DEFAULT ' . $this->getCurrentTimestampSQL();
        }

        if ($type instanceof CDatabase_Type_TimeType && $default === $this->getCurrentTimeSQL()) {
            return ' DEFAULT ' . $this->getCurrentTimeSQL();
        }

        if ($type instanceof CDatabase_Type_DateType && $default === $this->getCurrentDateSQL()) {
            return ' DEFAULT ' . $this->getCurrentDateSQL();
        }

        if ($type instanceof CDatabase_Type_BooleanType) {
            return " DEFAULT '" . $this->convertBooleans($default) . "'";
        }

        return " DEFAULT '" . $default . "'";
    }

    /**
     * Obtains DBMS specific SQL code portion needed to set a CHECK constraint
     * declaration to be used in statements like CREATE TABLE.
     *
     * @param array $definition the check definition
     *
     * @return string DBMS specific SQL code portion needed to set a CHECK constraint
     */
    public function getCheckDeclarationSQL(array $definition) {
        $constraints = [];
        foreach ($definition as $field => $def) {
            if (is_string($def)) {
                $constraints[] = 'CHECK (' . $def . ')';
            } else {
                if (isset($def['min'])) {
                    $constraints[] = 'CHECK (' . $field . ' >= ' . $def['min'] . ')';
                }

                if (isset($def['max'])) {
                    $constraints[] = 'CHECK (' . $field . ' <= ' . $def['max'] . ')';
                }
            }
        }

        return implode(', ', $constraints);
    }

    /**
     * Obtains DBMS specific SQL code portion needed to set a unique
     * constraint declaration to be used in statements like CREATE TABLE.
     *
     * @param string                 $name  the name of the unique constraint
     * @param CDatabase_Schema_Index $index the index definition
     *
     * @throws \InvalidArgumentException
     *
     * @return string DBMS specific SQL code portion needed to set a constraint
     */
    public function getUniqueConstraintDeclarationSQL($name, CDatabase_Schema_Index $index) {
        $columns = $index->getQuotedColumns($this);
        $name = new CDatabase_Schema_Identifier($name);

        if (count($columns) === 0) {
            throw new \InvalidArgumentException("Incomplete definition. 'columns' required.");
        }

        return 'CONSTRAINT ' . $name->getQuotedName($this) . ' UNIQUE ('
                . $this->getIndexFieldDeclarationListSQL($columns)
                . ')' . $this->getPartialIndexSQL($index);
    }

    /**
     * Obtains DBMS specific SQL code portion needed to set an index
     * declaration to be used in statements like CREATE TABLE.
     *
     * @param string                 $name  the name of the index
     * @param CDatabase_Schema_Index $index the index definition
     *
     * @throws \InvalidArgumentException
     *
     * @return string DBMS specific SQL code portion needed to set an index
     */
    public function getIndexDeclarationSQL($name, CDatabase_Schema_Index $index) {
        $columns = $index->getQuotedColumns($this);
        $name = new CDatabase_Schema_Identifier($name);

        if (count($columns) === 0) {
            throw new \InvalidArgumentException("Incomplete definition. 'columns' required.");
        }

        return $this->getCreateIndexSQLFlags($index) . 'INDEX ' . $name->getQuotedName($this) . ' ('
                . $this->getIndexFieldDeclarationListSQL($columns)
                . ')' . $this->getPartialIndexSQL($index);
    }

    /**
     * Obtains SQL code portion needed to create a custom column,
     * e.g. when a field has the "columnDefinition" keyword.
     * Only "AUTOINCREMENT" and "PRIMARY KEY" are added if appropriate.
     *
     * @param array $columnDef
     *
     * @return string
     */
    public function getCustomTypeDeclarationSQL(array $columnDef) {
        return $columnDef['columnDefinition'];
    }

    /**
     * Obtains DBMS specific SQL code portion needed to set an index
     * declaration to be used in statements like CREATE TABLE.
     *
     * @param mixed[]|CDatabase_Schema_Index $columnsOrIndex array declaration is deprecated, prefer passing Index to this method
     *
     * @return string
     */
    public function getIndexFieldDeclarationListSQL($columnsOrIndex) {
        if ($columnsOrIndex instanceof CDatabase_Schema_Index) {
            return implode(', ', $columnsOrIndex->getQuotedColumns($this));
        }

        if (!is_array($columnsOrIndex)) {
            throw new InvalidArgumentException('Fields argument should be an CDatabase_Schema_Index or array.');
        }
        $ret = [];

        foreach ($columnsOrIndex as $column => $definition) {
            if (is_array($definition)) {
                $ret[] = $column;
            } else {
                $ret[] = $definition;
            }
        }

        return implode(', ', $ret);
    }

    /**
     * Returns the required SQL string that fits between CREATE ... TABLE
     * to create the table as a temporary table.
     *
     * Should be overridden in driver classes to return the correct string for the
     * specific database type.
     *
     * The default is to return the string "TEMPORARY" - this will result in a
     * SQL error for any database that does not support temporary tables, or that
     * requires a different SQL command from "CREATE TEMPORARY TABLE".
     *
     * @return string the string required to be placed between "CREATE" and "TABLE"
     *                to generate a temporary table, if possible
     */
    public function getTemporaryTableSQL() {
        return 'TEMPORARY';
    }

    /**
     * Some vendors require temporary table names to be qualified specially.
     *
     * @param string $tableName
     *
     * @return string
     */
    public function getTemporaryTableName($tableName) {
        return $tableName;
    }

    /**
     * Obtain DBMS specific SQL code portion needed to set the FOREIGN KEY constraint
     * of a field declaration to be used in statements like CREATE TABLE.
     *
     * @param CDatabase_Schema_ForeignKeyConstraint $foreignKey
     *
     * @return string DBMS specific SQL code portion needed to set the FOREIGN KEY constraint
     *                of a field declaration
     */
    public function getForeignKeyDeclarationSQL(CDatabase_Schema_ForeignKeyConstraint $foreignKey) {
        $sql = $this->getForeignKeyBaseDeclarationSQL($foreignKey);
        $sql .= $this->getAdvancedForeignKeyOptionsSQL($foreignKey);

        return $sql;
    }

    /**
     * Returns the FOREIGN KEY query section dealing with non-standard options
     * as MATCH, INITIALLY DEFERRED, ON UPDATE, ...
     *
     * @param CDatabase_Schema_ForeignKeyConstraint $foreignKey the foreign key definition
     *
     * @return string
     */
    public function getAdvancedForeignKeyOptionsSQL(CDatabase_Schema_ForeignKeyConstraint $foreignKey) {
        $query = '';
        if ($this->supportsForeignKeyOnUpdate() && $foreignKey->hasOption('onUpdate')) {
            $query .= ' ON UPDATE ' . $this->getForeignKeyReferentialActionSQL($foreignKey->getOption('onUpdate'));
        }
        if ($foreignKey->hasOption('onDelete')) {
            $query .= ' ON DELETE ' . $this->getForeignKeyReferentialActionSQL($foreignKey->getOption('onDelete'));
        }

        return $query;
    }

    /**
     * Returns the given referential action in uppercase if valid, otherwise throws an exception.
     *
     * @param string $action the foreign key referential action
     *
     * @throws \InvalidArgumentException if unknown referential action given
     *
     * @return string
     */
    public function getForeignKeyReferentialActionSQL($action) {
        $upper = strtoupper($action);
        switch ($upper) {
            case 'CASCADE':
            case 'SET NULL':
            case 'NO ACTION':
            case 'RESTRICT':
            case 'SET DEFAULT':
                return $upper;
            default:
                throw new \InvalidArgumentException('Invalid foreign key action: ' . $upper);
        }
    }

    /**
     * Obtains DBMS specific SQL code portion needed to set the FOREIGN KEY constraint
     * of a field declaration to be used in statements like CREATE TABLE.
     *
     * @param CDatabase_Schema_ForeignKeyConstraint $foreignKey
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getForeignKeyBaseDeclarationSQL(CDatabase_Schema_ForeignKeyConstraint $foreignKey) {
        $sql = '';
        if (strlen($foreignKey->getName())) {
            $sql .= 'CONSTRAINT ' . $foreignKey->getQuotedName($this) . ' ';
        }
        $sql .= 'FOREIGN KEY (';

        if (count($foreignKey->getLocalColumns()) === 0) {
            throw new \InvalidArgumentException("Incomplete definition. 'local' required.");
        }
        if (count($foreignKey->getForeignColumns()) === 0) {
            throw new \InvalidArgumentException("Incomplete definition. 'foreign' required.");
        }
        if (strlen($foreignKey->getForeignTableName()) === 0) {
            throw new \InvalidArgumentException("Incomplete definition. 'foreignTable' required.");
        }

        $sql .= implode(', ', $foreignKey->getQuotedLocalColumns($this))
                . ') REFERENCES '
                . $foreignKey->getQuotedForeignTableName($this) . ' ('
                . implode(', ', $foreignKey->getQuotedForeignColumns($this)) . ')';

        return $sql;
    }

    /**
     * Obtains DBMS specific SQL code portion needed to set the UNIQUE constraint
     * of a field declaration to be used in statements like CREATE TABLE.
     *
     * @return string DBMS specific SQL code portion needed to set the UNIQUE constraint
     *                of a field declaration
     */
    public function getUniqueFieldDeclarationSQL() {
        return 'UNIQUE';
    }

    /**
     * Obtains DBMS specific SQL code portion needed to set the CHARACTER SET
     * of a field declaration to be used in statements like CREATE TABLE.
     *
     * @param string $charset the name of the charset
     *
     * @return string DBMS specific SQL code portion needed to set the CHARACTER SET
     *                of a field declaration
     */
    public function getColumnCharsetDeclarationSQL($charset) {
        return '';
    }

    /**
     * Obtains DBMS specific SQL code portion needed to set the COLLATION
     * of a field declaration to be used in statements like CREATE TABLE.
     *
     * @param string $collation the name of the collation
     *
     * @return string DBMS specific SQL code portion needed to set the COLLATION
     *                of a field declaration
     */
    public function getColumnCollationDeclarationSQL($collation) {
        return $this->supportsColumnCollation() ? 'COLLATE ' . $collation : '';
    }

    /**
     * Whether the platform prefers sequences for ID generation.
     * Subclasses should override this method to return TRUE if they prefer sequences.
     *
     * @return bool
     */
    public function prefersSequences() {
        return false;
    }

    /**
     * Whether the platform prefers identity columns (eg. autoincrement) for ID generation.
     * Subclasses should override this method to return TRUE if they prefer identity columns.
     *
     * @return bool
     */
    public function prefersIdentityColumns() {
        return false;
    }

    /**
     * Some platforms need the boolean values to be converted.
     *
     * The default conversion in this implementation converts to integers (false => 0, true => 1).
     *
     * Note: if the input is not a boolean the original input might be returned.
     *
     * There are two contexts when converting booleans: Literals and Prepared Statements.
     * This method should handle the literal case
     *
     * @param mixed $item a boolean or an array of them
     *
     * @return mixed a boolean database value or an array of them
     */
    public function convertBooleans($item) {
        if (is_array($item)) {
            foreach ($item as $k => $value) {
                if (is_bool($value)) {
                    $item[$k] = (int) $value;
                }
            }
        } elseif (is_bool($item)) {
            $item = (int) $item;
        }

        return $item;
    }

    /**
     * Some platforms have boolean literals that needs to be correctly converted.
     *
     * The default conversion tries to convert value into bool "(bool)$item"
     *
     * @param mixed $item
     *
     * @return null|bool
     */
    public function convertFromBoolean($item) {
        return null === $item ? null : (bool) $item;
    }

    /**
     * This method should handle the prepared statements case. When there is no
     * distinction, it's OK to use the same method.
     *
     * Note: if the input is not a boolean the original input might be returned.
     *
     * @param mixed $item a boolean or an array of them
     *
     * @return mixed a boolean database value or an array of them
     */
    public function convertBooleansToDatabaseValue($item) {
        return $this->convertBooleans($item);
    }

    /**
     * Returns the SQL specific for the platform to get the current date.
     *
     * @return string
     */
    public function getCurrentDateSQL() {
        return 'CURRENT_DATE';
    }

    /**
     * Returns the SQL specific for the platform to get the current time.
     *
     * @return string
     */
    public function getCurrentTimeSQL() {
        return 'CURRENT_TIME';
    }

    /**
     * Returns the SQL specific for the platform to get the current timestamp.
     *
     * @return string
     */
    public function getCurrentTimestampSQL() {
        return 'CURRENT_TIMESTAMP';
    }

    /**
     * Returns the SQL for a given transaction isolation level Connection constant.
     *
     * @param int $level
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function getTransactionIsolationLevelSQL($level) {
        switch ($level) {
            case CDatabase_TransactionIsolationLevel::READ_UNCOMMITTED:
                return 'READ UNCOMMITTED';
            case CDatabase_TransactionIsolationLevel::READ_COMMITTED:
                return 'READ COMMITTED';
            case CDatabase_TransactionIsolationLevel::REPEATABLE_READ:
                return 'REPEATABLE READ';
            case CDatabase_TransactionIsolationLevel::SERIALIZABLE:
                return 'SERIALIZABLE';
            default:
                throw new \InvalidArgumentException('Invalid isolation level:' . $level);
        }
    }

    /**
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getListDatabasesSQL() {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Returns the SQL statement for retrieving the namespaces defined in the database.
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getListNamespacesSQL() {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * @param string $database
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getListSequencesSQL($database) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * @param string $table
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getListTableConstraintsSQL($table) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * @param string      $table
     * @param null|string $database
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getListTableColumnsSQL($table, $database = null) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getListTablesSQL() {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getListUsersSQL() {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Returns the SQL to list all views of a database or user.
     *
     * @param string $database
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getListViewsSQL($database) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Returns the list of indexes for the current database.
     *
     * The current database parameter is optional but will always be passed
     * when using the SchemaManager API and is the database the given table is in.
     *
     * Attention: Some platforms only support currentDatabase when they
     * are connected with that database. Cross-database information schema
     * requests may be impossible.
     *
     * @param string $table
     * @param string $currentDatabase
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getListTableIndexesSQL($table, $currentDatabase = null) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * @param string $table
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getListTableForeignKeysSQL($table) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * @param string $name
     * @param string $sql
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getCreateViewSQL($name, $sql) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * @param string $name
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDropViewSQL($name) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Returns the SQL snippet to drop an existing sequence.
     *
     * @param Sequence|string $sequence
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDropSequenceSQL($sequence) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * @param string $sequenceName
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getSequenceNextValSQL($sequenceName) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Returns the SQL to create a new database.
     *
     * @param string $database the name of the database that should be created
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getCreateDatabaseSQL($database) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Returns the SQL to set the transaction isolation level.
     *
     * @param int $level
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getSetTransactionIsolationSQL($level) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Obtains DBMS specific SQL to be used to create datetime fields in
     * statements like CREATE TABLE.
     *
     * @param array $fieldDeclaration
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateTimeTypeDeclarationSQL(array $fieldDeclaration) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Obtains DBMS specific SQL to be used to create datetime with timezone offset fields.
     *
     * @param array $fieldDeclaration
     *
     * @return string
     */
    public function getDateTimeTzTypeDeclarationSQL(array $fieldDeclaration) {
        return $this->getDateTimeTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * Obtains DBMS specific SQL to be used to create date fields in statements
     * like CREATE TABLE.
     *
     * @param array $fieldDeclaration
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDateTypeDeclarationSQL(array $fieldDeclaration) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Obtains DBMS specific SQL to be used to create time fields in statements
     * like CREATE TABLE.
     *
     * @param array $fieldDeclaration
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getTimeTypeDeclarationSQL(array $fieldDeclaration) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * @param array $fieldDeclaration
     *
     * @return string
     */
    public function getFloatDeclarationSQL(array $fieldDeclaration) {
        return 'DOUBLE PRECISION';
    }

    /**
     * Gets the default transaction isolation level of the platform.
     *
     * @return int the default isolation level
     *
     * @see TransactionIsolationLevel
     */
    public function getDefaultTransactionIsolationLevel() {
        return CDatabase_TransactionIsolationLevel::READ_COMMITTED;
    }

    /* supports*() methods */

    /**
     * Whether the platform supports sequences.
     *
     * @return bool
     */
    public function supportsSequences() {
        return false;
    }

    /**
     * Whether the platform supports identity columns.
     *
     * Identity columns are columns that receive an auto-generated value from the
     * database on insert of a row.
     *
     * @return bool
     */
    public function supportsIdentityColumns() {
        return false;
    }

    /**
     * Whether the platform emulates identity columns through sequences.
     *
     * Some platforms that do not support identity columns natively
     * but support sequences can emulate identity columns by using
     * sequences.
     *
     * @return bool
     */
    public function usesSequenceEmulatedIdentityColumns() {
        return false;
    }

    /**
     * Returns the name of the sequence for a particular identity column in a particular table.
     *
     * @param string $tableName  the name of the table to return the sequence name for
     * @param string $columnName the name of the identity column in the table to return the sequence name for
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     *
     * @see    usesSequenceEmulatedIdentityColumns
     */
    public function getIdentitySequenceName($tableName, $columnName) {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Whether the platform supports indexes.
     *
     * @return bool
     */
    public function supportsIndexes() {
        return true;
    }

    /**
     * Whether the platform supports partial indexes.
     *
     * @return bool
     */
    public function supportsPartialIndexes() {
        return false;
    }

    /**
     * Whether the platform supports altering tables.
     *
     * @return bool
     */
    public function supportsAlterTable() {
        return true;
    }

    /**
     * Whether the platform supports transactions.
     *
     * @return bool
     */
    public function supportsTransactions() {
        return true;
    }

    /**
     * Whether the platform supports savepoints.
     *
     * @return bool
     */
    public function supportsSavepoints() {
        return true;
    }

    /**
     * Whether the platform supports releasing savepoints.
     *
     * @return bool
     */
    public function supportsReleaseSavepoints() {
        return $this->supportsSavepoints();
    }

    /**
     * Whether the platform supports primary key constraints.
     *
     * @return bool
     */
    public function supportsPrimaryConstraints() {
        return true;
    }

    /**
     * Whether the platform supports foreign key constraints.
     *
     * @return bool
     */
    public function supportsForeignKeyConstraints() {
        return true;
    }

    /**
     * Whether this platform supports onUpdate in foreign key constraints.
     *
     * @return bool
     */
    public function supportsForeignKeyOnUpdate() {
        return $this->supportsForeignKeyConstraints() && true;
    }

    /**
     * Whether the platform supports database schemas.
     *
     * @return bool
     */
    public function supportsSchemas() {
        return false;
    }

    /**
     * Whether this platform can emulate schemas.
     *
     * Platforms that either support or emulate schemas don't automatically
     * filter a schema for the namespaced elements in {@link * AbstractManager#createSchema}.
     *
     * @return bool
     */
    public function canEmulateSchemas() {
        return false;
    }

    /**
     * Returns the default schema name.
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    public function getDefaultSchemaName() {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Whether this platform supports create database.
     *
     * Some databases don't allow to create and drop databases at all or only with certain tools.
     *
     * @return bool
     */
    public function supportsCreateDropDatabase() {
        return true;
    }

    /**
     * Whether the platform supports getting the affected rows of a recent update/delete type query.
     *
     * @return bool
     */
    public function supportsGettingAffectedRows() {
        return true;
    }

    /**
     * Whether this platform support to add inline column comments as postfix.
     *
     * @return bool
     */
    public function supportsInlineColumnComments() {
        return false;
    }

    /**
     * Whether this platform support the proprietary syntax "COMMENT ON asset".
     *
     * @return bool
     */
    public function supportsCommentOnStatement() {
        return false;
    }

    /**
     * Does this platform have native guid type.
     *
     * @return bool
     */
    public function hasNativeGuidType() {
        return false;
    }

    /**
     * Does this platform have native JSON type.
     *
     * @return bool
     */
    public function hasNativeJsonType() {
        return false;
    }

    /**
     * @deprecated
     *
     * @todo Remove in 3.0
     */
    public function getIdentityColumnNullInsertSQL() {
        return '';
    }

    /**
     * Whether this platform supports views.
     *
     * @return bool
     */
    public function supportsViews() {
        return true;
    }

    /**
     * Does this platform support column collation?
     *
     * @return bool
     */
    public function supportsColumnCollation() {
        return false;
    }

    /**
     * Gets the format string, as accepted by the date() function, that describes
     * the format of a stored datetime value of this platform.
     *
     * @return string the format string
     */
    public function getDateTimeFormatString() {
        return 'Y-m-d H:i:s';
    }

    /**
     * Gets the format string, as accepted by the date() function, that describes
     * the format of a stored datetime with timezone value of this platform.
     *
     * @return string the format string
     */
    public function getDateTimeTzFormatString() {
        return 'Y-m-d H:i:s';
    }

    /**
     * Gets the format string, as accepted by the date() function, that describes
     * the format of a stored date value of this platform.
     *
     * @return string the format string
     */
    public function getDateFormatString() {
        return 'Y-m-d';
    }

    /**
     * Gets the format string, as accepted by the date() function, that describes
     * the format of a stored time value of this platform.
     *
     * @return string the format string
     */
    public function getTimeFormatString() {
        return 'H:i:s';
    }

    /**
     * Adds an driver-specific LIMIT clause to the query.
     *
     * @param string   $query
     * @param null|int $limit
     * @param null|int $offset
     *
     * @throws DBALException
     *
     * @return string
     */
    final public function modifyLimitQuery($query, $limit, $offset = null) {
        if ($limit !== null) {
            $limit = (int) $limit;
        }

        $offset = (int) $offset;

        if ($offset < 0) {
            throw new CDatabase_Exception(sprintf(
                'Offset must be a positive integer or zero, %d given',
                $offset
            ));
        }

        if ($offset > 0 && !$this->supportsLimitOffset()) {
            throw new CDatabase_Exception(sprintf(
                'Platform %s does not support offset values in limit queries.',
                $this->getName()
            ));
        }

        return $this->doModifyLimitQuery($query, $limit, $offset);
    }

    /**
     * Adds an platform-specific LIMIT clause to the query.
     *
     * @param string   $query
     * @param null|int $limit
     * @param null|int $offset
     *
     * @return string
     */
    protected function doModifyLimitQuery($query, $limit, $offset) {
        if ($limit !== null) {
            $query .= ' LIMIT ' . $limit;
        }

        if ($offset > 0) {
            $query .= ' OFFSET ' . $offset;
        }

        return $query;
    }

    /**
     * Whether the database platform support offsets in modify limit clauses.
     *
     * @return bool
     */
    public function supportsLimitOffset() {
        return true;
    }

    /**
     * Gets the character casing of a column in an SQL result set of this platform.
     *
     * @param string $column the column name for which to get the correct character casing
     *
     * @return string the column name in the character casing used in SQL result sets
     */
    public function getSQLResultCasing($column) {
        return $column;
    }

    /**
     * Makes any fixes to a name of a schema element (table, sequence, ...) that are required
     * by restrictions of the platform, like a maximum length.
     *
     * @param string $schemaElementName
     *
     * @return string
     */
    public function fixSchemaElementName($schemaElementName) {
        return $schemaElementName;
    }

    /**
     * Maximum length of any given database identifier, like tables or column names.
     *
     * @return int
     */
    public function getMaxIdentifierLength() {
        return 63;
    }

    /**
     * Returns the insert SQL for an empty insert statement.
     *
     * @param string $tableName
     * @param string $identifierColumnName
     *
     * @return string
     */
    public function getEmptyIdentityInsertSQL($tableName, $identifierColumnName) {
        return 'INSERT INTO ' . $tableName . ' (' . $identifierColumnName . ') VALUES (null)';
    }

    /**
     * Generates a Truncate Table SQL statement for a given table.
     *
     * Cascade is not supported on many platforms but would optionally cascade the truncate by
     * following the foreign keys.
     *
     * @param string $tableName
     * @param bool   $cascade
     *
     * @return string
     */
    public function getTruncateTableSQL($tableName, $cascade = false) {
        $tableIdentifier = new CDatabase_Schema_Identifier($tableName);

        return 'TRUNCATE ' . $tableIdentifier->getQuotedName($this);
    }

    /**
     * This is for test reasons, many vendors have special requirements for dummy statements.
     *
     * @return string
     */
    public function getDummySelectSQL() {
        $expression = func_num_args() > 0 ? func_get_arg(0) : '1';

        return sprintf('SELECT %s', $expression);
    }

    /**
     * Returns the SQL to create a new savepoint.
     *
     * @param string $savepoint
     *
     * @return string
     */
    public function createSavePoint($savepoint) {
        return 'SAVEPOINT ' . $savepoint;
    }

    /**
     * Returns the SQL to release a savepoint.
     *
     * @param string $savepoint
     *
     * @return string
     */
    public function releaseSavePoint($savepoint) {
        return 'RELEASE SAVEPOINT ' . $savepoint;
    }

    /**
     * Returns the SQL to rollback a savepoint.
     *
     * @param string $savepoint
     *
     * @return string
     */
    public function rollbackSavePoint($savepoint) {
        return 'ROLLBACK TO SAVEPOINT ' . $savepoint;
    }

    /**
     * Returns the keyword list instance of this platform.
     *
     * @throws CDatabase_Exception if no keyword list is specified
     *
     * @return CDatabase_Platform_Keywords
     */
    final public function getReservedKeywordsList() {
        // Check for an existing instantiation of the keywords class.
        if ($this->keywords) {
            return $this->keywords;
        }

        $class = $this->getReservedKeywordsClass();
        $keywords = new $class();
        if (!$keywords instanceof CDatabase_Platform_Keywords) {
            throw CDatabase_Exception::notSupported(__METHOD__);
        }

        // Store the instance so it doesn't need to be generated on every request.
        $this->keywords = $keywords;

        return $keywords;
    }

    /**
     * Returns the class name of the reserved keywords list.
     *
     * @throws CDatabase_Exception if not supported on this platform
     *
     * @return string
     */
    protected function getReservedKeywordsClass() {
        throw CDatabase_Exception::notSupported(__METHOD__);
    }

    /**
     * Quotes a literal string.
     * This method is NOT meant to fix SQL injections!
     * It is only meant to escape this platform's string literal
     * quote character inside the given literal string.
     *
     * @param string $str the literal string to be quoted
     *
     * @return string the quoted literal string
     */
    public function quoteStringLiteral($str) {
        $c = $this->getStringLiteralQuoteCharacter();

        return $c . str_replace($c, $c . $c, $str) . $c;
    }

    /**
     * Gets the character used for string literal quoting.
     *
     * @return string
     */
    public function getStringLiteralQuoteCharacter() {
        return "'";
    }

    /**
     * Escapes metacharacters in a string intended to be used with a LIKE
     * operator.
     *
     * @param string $inputString a literal, unquoted string
     * @param string $escapeChar  should be reused by the caller in the LIKE
     *                            expression
     */
    final public function escapeStringForLike($inputString, $escapeChar) {
        return preg_replace(
            '~([' . preg_quote($this->getLikeWildcardCharacters() . $escapeChar, '~') . '])~u',
            addcslashes($escapeChar, '\\') . '$1',
            $inputString
        );
    }

    protected function getLikeWildcardCharacters() {
        return '%_';
    }
}
