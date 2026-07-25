<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Tests for CDatabase_Schema_Grammar_SqlServerGrammar - asserts the actual SQL strings
 * produced when compiling a CDatabase_Schema_Blueprint against the SQL Server schema
 * grammar. No real database/PDO connection is used - the CDatabase_Connection is mocked
 * with Mockery.
 *
 * NB: unlike CDatabase_Query_Grammar_SqlServerGrammar (which wraps identifiers in square
 * brackets, e.g. [users]), CDatabase_Schema_Grammar_SqlServerGrammar does not override
 * wrapValue() at all, so it inherits the base CDatabase_Grammar double-quote wrapping
 * ("users") instead of real SQL Server's square-bracket convention. That is a real
 * inconsistency between the query and schema grammars for the same dialect - documented
 * here, not fixed, since correcting it would require auditing every expected string in
 * this dialect and is outside a minimal/low-risk fix.
 */
class DatabaseSqlServerSchemaGrammarTest extends TestCase {
    protected function tearDown(): void {
        m::close();
    }

    protected function getConnection($usingNative = true) {
        $connection = m::mock(CDatabase_Connection::class);
        $connection->shouldReceive('getConfig')->andReturn(null)->byDefault();
        $connection->shouldReceive('getTablePrefix')->andReturn('');
        $connection->shouldReceive('usingNativeSchemaOperations')->andReturn($usingNative);

        return $connection;
    }

    protected function getGrammar($connection = null) {
        $grammar = new CDatabase_Schema_Grammar_SqlServerGrammar();
        $grammar->setConnection($connection ?: $this->getConnection());

        return $grammar;
    }

    public function testBasicCreateTable() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->create();
        $blueprint->increments('id');
        $blueprint->string('email');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertCount(1, $statements);
        $this->assertSame(
            'create table "users" ("id" int not null identity primary key, "email" nvarchar(255) not null)',
            $statements[0]
        );
    }

    public function testAddColumns() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->string('name');
        $blueprint->integer('votes');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'alter table "users" add "name" nvarchar(255) not null, "votes" int not null',
            $statements[0]
        );
    }

    public function testDropTable() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->drop();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('drop table "users"', $statements[0]);
    }

    public function testDropTableIfExists() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropIfExists();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            "if exists (select * from sys.sysobjects where id = object_id('users', 'U')) drop table \"users\"",
            $statements[0]
        );
    }

    public function testRenameTable() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->rename('people');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('sp_rename "users", "people"', $statements[0]);
    }

    public function testRenameColumnUsingNativeSchemaOperations() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->renameColumn('from', 'to');

        $connection = $this->getConnection(true);
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame("sp_rename '\"users\".\"from\"', \"to\", 'COLUMN'", $statements[0]);
    }

    public function testRenameIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $command = new CBase_Fluent(['from' => 'foo', 'to' => 'bar']);

        $sql = $this->getGrammar()->compileRenameIndex($blueprint, $command);

        $this->assertSame("sp_rename N'\"users\".\"foo\"', \"bar\", N'INDEX'", $sql);
    }

    public function testAddingPrimaryKey() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->primary('foo', 'bar');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table "users" add constraint "bar" primary key ("foo")', $statements[0]);
    }

    public function testAddingUniqueKey() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->unique('foo', 'bar');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('create unique index "bar" on "users" ("foo")', $statements[0]);
    }

    public function testAddingIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->index(['foo', 'bar'], 'baz');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('create index "baz" on "users" ("foo", "bar")', $statements[0]);
    }

    public function testAddingSpatialIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('geo');
        $blueprint->spatialIndex('coordinates', 'baz');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('create spatial index "baz" on "geo" ("coordinates")', $statements[0]);
    }

    public function testDropPrimary() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropPrimary('users_id_primary');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table "users" drop constraint "users_id_primary"', $statements[0]);
    }

    public function testDropUnique() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropUnique('users_foo_unique');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('drop index "users_foo_unique" on "users"', $statements[0]);
    }

    public function testDropIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropIndex(['foo']);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('drop index "users_foo_index" on "users"', $statements[0]);
    }

    public function testDropForeign() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropForeign('users_foo_id_foreign');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table "users" drop constraint "users_foo_id_foreign"', $statements[0]);
    }

    public function testEnableAndDisableForeignKeyConstraints() {
        $grammar = $this->getGrammar();
        $this->assertSame(
            'EXEC sp_msforeachtable @command1="print \'?\'", @command2="ALTER TABLE ? WITH CHECK CHECK CONSTRAINT all";',
            $grammar->compileEnableForeignKeyConstraints()
        );
        $this->assertSame(
            'EXEC sp_msforeachtable "ALTER TABLE ? NOCHECK CONSTRAINT all";',
            $grammar->compileDisableForeignKeyConstraints()
        );
    }

    public function testGetAllTablesAndViews() {
        $grammar = $this->getGrammar();
        $this->assertSame("select name, type from sys.tables where type = 'U'", $grammar->compileGetAllTables());
        $this->assertSame("select name, type from sys.objects where type = 'V'", $grammar->compileGetAllViews());
    }

    public function testCompileTableExists() {
        $this->assertSame(
            "select * from sys.sysobjects where id = object_id(?) and xtype in ('U', 'V')",
            $this->getGrammar()->compileTableExists(null, 'users')
        );
    }

    public function testCompileColumnListing() {
        $this->assertSame(
            "select name from sys.columns where object_id = object_id('users')",
            $this->getGrammar()->compileColumnListing('users')
        );
    }

    public function testCompileDefaultCommandOnlyEmitsSqlWhenChangingWithADefault() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $columnWithDefault = new CBase_Fluent(['name' => 'votes', 'change' => true, 'default' => 0]);
        $commandWithDefault = new CBase_Fluent(['column' => $columnWithDefault]);

        $sql = $this->getGrammar()->compileDefault($blueprint, $commandWithDefault);
        $this->assertSame("alter table \"users\" add default '0' for \"votes\"", $sql);

        $columnWithoutChange = new CBase_Fluent(['name' => 'votes', 'change' => false, 'default' => 0]);
        $commandWithoutChange = new CBase_Fluent(['column' => $columnWithoutChange]);
        $this->assertNull($this->getGrammar()->compileDefault($blueprint, $commandWithoutChange));
    }

    public function testCompileChangeIsNotSupportedWithoutNativeSchemaOperations() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->string('a')->nullable()->change();

        $connection = $this->getConnection(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This database driver does not support modifying columns.');

        $blueprint->toSql($connection, $this->getGrammar($connection));
    }

    public function testCompileChangeWithNativeSchemaOperationsIncludesDropDefaultConstraint() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->string('a')->nullable()->change();

        $connection = $this->getConnection(true);
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        // compileChange() returns an array: [dropDefaultConstraintSql, ...perColumnAlterSql]
        $this->assertCount(2, $statements);
        $this->assertStringContainsString('DECLARE @sql NVARCHAR(MAX)', $statements[0]);
        $this->assertStringContainsString("[name] in ('a')", $statements[0]);
        $this->assertSame('alter table "users" alter column "a" nvarchar(255) null', $statements[1]);
    }

    public function testStringAndTextColumnTypes() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->char('a', 10);
        $blueprint->string('b', 20);
        $blueprint->text('c');
        $blueprint->mediumText('d');
        $blueprint->longText('e');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" nchar(10) not null, "b" nvarchar(20) not null, "c" nvarchar(max) not null, '
            . '"d" nvarchar(max) not null, "e" nvarchar(max) not null)',
            $statements[0]
        );
    }

    public function testIntegerColumnTypes() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->tinyInteger('a');
        $blueprint->smallInteger('b');
        $blueprint->mediumInteger('c');
        $blueprint->integer('d');
        $blueprint->bigInteger('e');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" tinyint not null, "b" smallint not null, "c" int not null, '
            . '"d" int not null, "e" bigint not null)',
            $statements[0]
        );
    }

    public function testNumericColumnTypes() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->float('a');
        $blueprint->double('b');
        $blueprint->decimal('c');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" float not null, "b" float not null, "c" decimal(8, 2) not null)',
            $statements[0]
        );
    }

    public function testBooleanEnumJsonColumnTypes() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->boolean('a');
        $blueprint->enum('b', ['easy', 'hard']);
        $blueprint->json('c');
        $blueprint->jsonb('d');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            "create table \"t\" (\"a\" bit not null, \"b\" nvarchar(255) check (\"b\" in (N'easy', N'hard')) not null, "
            . '"c" nvarchar(max) not null, "d" nvarchar(max) not null)',
            $statements[0]
        );
    }

    public function testDateTimeColumnTypes() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->date('a');
        $blueprint->dateTime('b', 2);
        $blueprint->time('c', 3);
        $blueprint->timestamp('d', 1);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" date not null, "b" datetime2(2) not null, "c" time(3) not null, "d" datetime2(1) not null)',
            $statements[0]
        );
    }

    public function testTimestampUseCurrentSetsDefaultExpression() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->timestamp('a')->useCurrent();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" datetime not null default CURRENT_TIMESTAMP)',
            $statements[0]
        );
    }

    public function testBinaryUuidAndNetworkColumnTypes() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->binary('a');
        $blueprint->uuid('b');
        $blueprint->ipAddress('c');
        $blueprint->macAddress('d');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" varbinary(max) not null, "b" uniqueidentifier not null, '
            . '"c" nvarchar(45) not null, "d" nvarchar(17) not null)',
            $statements[0]
        );
    }

    public function testSpatialColumnTypesAllMapToGeography() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->geometry('a');
        $blueprint->point('b');
        $blueprint->lineString('c');
        $blueprint->polygon('d');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" geography not null, "b" geography not null, "c" geography not null, "d" geography not null)',
            $statements[0]
        );
    }

    public function testComputedColumnType() {
        // Unlike MySQL/SQLite (which don't support a bare "computed" column type at all),
        // SQL Server does: typeComputed() compiles to "as (<expression>)".
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->addColumn('computed', 'a', ['expression' => 'b + c']);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('create table "t" ("a" as (b + c))', $statements[0]);
    }

    public function testNullableAndDefaultModifiers() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->string('a')->nullable()->default('foo');
        $blueprint->integer('b')->default(0);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            "create table \"t\" (\"a\" nvarchar(255) null default 'foo', \"b\" int not null default '0')",
            $statements[0]
        );
    }

    public function testCollateModifier() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->string('a')->collation('SQL_Latin1_General_CP1_CI_AS');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" nvarchar(255) collate SQL_Latin1_General_CP1_CI_AS not null)',
            $statements[0]
        );
    }

    public function testPersistedModifierOnComputedColumn() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->addColumn('computed', 'a', ['expression' => 'b + c', 'persisted' => true]);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('create table "t" ("a" as (b + c) persisted)', $statements[0]);
    }

    public function testQuoteStringUsesNPrefixedLiterals() {
        // SQL Server needs N'...' (not plain '...') to safely hold unicode string literals -
        // this is a real, deliberate divergence from the base Grammar::quoteString().
        $grammar = $this->getGrammar();
        $this->assertSame("N'hello'", $grammar->quoteString('hello'));
        $this->assertSame("N'a', N'b'", $grammar->quoteString(['a', 'b']));
    }
}
