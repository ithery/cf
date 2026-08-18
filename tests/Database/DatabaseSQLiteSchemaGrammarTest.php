<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Tests for CDatabase_Schema_Grammar_SqliteGrammar - asserts the actual SQL strings
 * produced when compiling a CDatabase_Schema_Blueprint against the SQLite schema
 * grammar. No real database/PDO connection is used (the sqlite PDO driver is not even
 * installed in this sandbox) - the CDatabase_Connection is mocked with Mockery.
 *
 * NOT covered here: compileDropColumn() and compileRenameIndex(), both of which require
 * a real Doctrine DBAL schema manager fetched off the connection (getDoctrineSchemaManager())
 * rather than pure grammar compilation - they cannot be exercised without a live DB.
 */
class DatabaseSQLiteSchemaGrammarTest extends TestCase {
    protected function tearDown(): void {
        m::close();
    }

    protected function getConnection() {
        $connection = m::mock(CDatabase_Connection::class);
        $connection->shouldReceive('getConfig')->andReturn(null)->byDefault();
        $connection->shouldReceive('getTablePrefix')->andReturn('');
        $connection->shouldReceive('usingNativeSchemaOperations')->andReturn(true);

        return $connection;
    }

    protected function getGrammar($connection = null) {
        $grammar = new CDatabase_Schema_Grammar_SqliteGrammar();
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
            'create table "users" ("id" integer not null primary key autoincrement, "email" varchar not null)',
            $statements[0]
        );
    }

    public function testCreateTemporaryTable() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->create();
        $blueprint->temporary();
        $blueprint->string('email');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create temporary table "users" ("email" varchar not null)',
            $statements[0]
        );
    }

    public function testCreateTableWithPrimaryKeyIsInlined() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->create();
        $blueprint->string('first');
        $blueprint->string('last');
        $blueprint->primary(['first', 'last']);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "users" ("first" varchar not null, "last" varchar not null, primary key ("first", "last"))',
            $statements[0]
        );
    }

    public function testStandalonePrimaryCommandCompilesToNothing() {
        // Unlike MySQL/Postgres/SqlServer, SQLite has no compilePrimary() method at all -
        // a primary key can only be expressed inline at CREATE TABLE time (see
        // testCreateTableWithPrimaryKeyIsInlined above). Adding ->primary() to an ALTER
        // (non-create) blueprint therefore silently produces no SQL statement.
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->primary('id');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame([], $statements);
    }

    public function testCreateTableWithForeignKeyIsInlined() {
        $blueprint = new CDatabase_Schema_Blueprint('posts');
        $blueprint->create();
        $blueprint->integer('user_id');
        $blueprint->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('restrict');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "posts" ("user_id" integer not null, foreign key("user_id") references "users"("id") on delete cascade on update restrict)',
            $statements[0]
        );
    }

    public function testStandaloneForeignCommandCompilesToNothing() {
        // Also handled purely at CREATE TABLE time - compileForeign() explicitly returns
        // nothing ("// Handled on table creation...").
        $blueprint = new CDatabase_Schema_Blueprint('posts');
        $blueprint->foreign('user_id')->references('id')->on('users');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame([], $statements);
    }

    public function testAddColumnsProducesOneAlterStatementPerColumn() {
        // Unlike MySQL/Postgres, SQLite's compileAdd() returns one "alter table ... add
        // column ..." statement per column rather than a single comma-joined statement.
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->string('name');
        $blueprint->integer('votes');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            [
                'alter table "users" add column "name" varchar not null',
                'alter table "users" add column "votes" integer not null',
            ],
            $statements
        );
    }

    public function testAddColumnRejectsStoredGeneratedColumns() {
        // SQLite cannot ALTER TABLE ADD COLUMN a stored generated column, so compileAdd()
        // filters those out entirely rather than emitting invalid SQL.
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->string('name');
        $blueprint->integer('doubled')->storedAs('name * 2');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            ['alter table "users" add column "name" varchar not null'],
            $statements
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

        $this->assertSame('drop table if exists "users"', $statements[0]);
    }

    public function testRenameTable() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->rename('people');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table "users" rename to "people"', $statements[0]);
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

    public function testSpatialIndexIsNotSupported() {
        $blueprint = new CDatabase_Schema_Blueprint('geo');
        $command = new CBase_Fluent(['index' => 'baz', 'columns' => ['coordinates']]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The database driver in use does not support spatial indexes.');

        $this->getGrammar()->compileSpatialIndex($blueprint, $command);
    }

    public function testDropUnique() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropUnique('users_foo_unique');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('drop index "users_foo_unique"', $statements[0]);
    }

    public function testDropIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropIndex(['foo']);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('drop index "users_foo_index"', $statements[0]);
    }

    public function testDropSpatialIndexIsNotSupported() {
        $blueprint = new CDatabase_Schema_Blueprint('geo');
        $command = new CBase_Fluent(['index' => 'geo_coordinates_spatialindex']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The database driver in use does not support spatial indexes.');

        $this->getGrammar()->compileDropSpatialIndex($blueprint, $command);
    }

    public function testEnableAndDisableForeignKeyConstraints() {
        $grammar = $this->getGrammar();
        $this->assertSame('PRAGMA foreign_keys = ON;', $grammar->compileEnableForeignKeyConstraints());
        $this->assertSame('PRAGMA foreign_keys = OFF;', $grammar->compileDisableForeignKeyConstraints());
    }

    public function testEnableAndDisableWriteableSchema() {
        $grammar = $this->getGrammar();
        $this->assertSame('PRAGMA writable_schema = 1;', $grammar->compileEnableWriteableSchema());
        $this->assertSame('PRAGMA writable_schema = 0;', $grammar->compileDisableWriteableSchema());
    }

    public function testCompileRebuild() {
        $this->assertSame('vacuum', $this->getGrammar()->compileRebuild());
    }

    public function testGetAllTablesAndViews() {
        $grammar = $this->getGrammar();
        $this->assertSame(
            "select type, name from sqlite_master where type = 'table' and name not like 'sqlite_%'",
            $grammar->compileGetAllTables()
        );
        $this->assertSame(
            "select type, name from sqlite_master where type = 'view'",
            $grammar->compileGetAllViews()
        );
    }

    public function testCompileTableExists() {
        $this->assertSame(
            "select * from sqlite_master where type = 'table' and name = ?",
            $this->getGrammar()->compileTableExists(null, 'users')
        );
    }

    public function testCompileColumnListing() {
        $this->assertSame(
            'pragma table_info("users")',
            $this->getGrammar()->compileColumnListing('users')
        );
    }

    public function testStringAndTextColumnTypesLoseTheirLength() {
        // SQLite's type affinity system doesn't use lengths at all - char/string both
        // compile to bare "varchar", regardless of the length passed in.
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
            'create table "t" ("a" varchar not null, "b" varchar not null, "c" text not null, '
            . '"d" text not null, "e" text not null)',
            $statements[0]
        );
    }

    public function testIntegerColumnTypesAllMapToInteger() {
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
            'create table "t" ("a" integer not null, "b" integer not null, "c" integer not null, '
            . '"d" integer not null, "e" integer not null)',
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
            'create table "t" ("a" float not null, "b" float not null, "c" numeric not null)',
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
            'create table "t" ("a" tinyint(1) not null, "b" varchar check ("b" in (\'easy\', \'hard\')) not null, '
            . '"c" text not null, "d" text not null)',
            $statements[0]
        );
    }

    public function testDateTimeColumnTypes() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->date('a');
        $blueprint->dateTime('b');
        $blueprint->time('c');
        $blueprint->timestamp('d');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" date not null, "b" datetime not null, "c" time not null, "d" datetime not null)',
            $statements[0]
        );
    }

    public function testTimestampUseCurrent() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->timestamp('a')->useCurrent();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" datetime default CURRENT_TIMESTAMP not null)',
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
            'create table "t" ("a" blob not null, "b" varchar not null, "c" varchar not null, "d" varchar not null)',
            $statements[0]
        );
    }

    public function testComputedColumnTypeIsNotSupported() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->addColumn('computed', 'a');

        $connection = $this->getConnection();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This database driver requires a type, see the virtualAs / storedAs modifiers.');

        $blueprint->toSql($connection, $this->getGrammar($connection));
    }

    public function testNullableModifierOmitsNullKeywordWhenNullable() {
        // Unlike MySQL (which emits an explicit " null"), SQLite's modifyNullable() emits
        // nothing at all for nullable columns - only "not null" is ever written out.
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->string('a')->nullable();
        $blueprint->string('b')->nullable(false);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" varchar, "b" varchar not null)',
            $statements[0]
        );
    }

    public function testDefaultModifier() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->string('a')->default('foo');
        $blueprint->integer('b')->default(0);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            "create table \"t\" (\"a\" varchar not null default 'foo', \"b\" integer not null default '0')",
            $statements[0]
        );
    }

    public function testVirtualAsAndStoredAsModifiers() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->integer('a');
        $blueprint->integer('b')->virtualAs('a + 1');
        $blueprint->integer('c')->storedAs('a + 2');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" integer not null, "b" integer as (a + 1), "c" integer as (a + 2) stored)',
            $statements[0]
        );
    }

    public function testUnsignedAndCommentModifiersHaveNoEffect() {
        // SQLite's $modifiers list is only ['VirtualAs', 'StoredAs', 'Nullable', 'Default',
        // 'Increment'] - unlike MySQL, there is no modifyUnsigned()/modifyComment(), so
        // calling ->unsigned()/->comment() on a column is silently a no-op here.
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->integer('a')->unsigned()->comment('some comment');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('create table "t" ("a" integer not null)', $statements[0]);
    }
}
