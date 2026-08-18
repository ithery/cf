<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Tests for CDatabase_Schema_Grammar_PostgresGrammar - asserts the actual SQL strings
 * produced when compiling a CDatabase_Schema_Blueprint against the PostgreSQL schema
 * grammar. No real database/PDO connection is used - the CDatabase_Connection is mocked
 * with Mockery.
 */
class DatabasePostgresSchemaGrammarTest extends TestCase {
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
        $grammar = new CDatabase_Schema_Grammar_PostgresGrammar();
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
            'create table "users" ("id" serial not null primary key, "email" varchar(255) not null)',
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
            'create temporary table "users" ("email" varchar(255) not null)',
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
            'alter table "users" add column "name" varchar(255) not null, add column "votes" integer not null',
            $statements[0]
        );
    }

    public function testDropColumn() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropColumn(['votes', 'name']);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'alter table "users" drop column "votes", drop column "name"',
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

        $this->assertSame('drop table if exists "users"', $statements[0]);
    }

    public function testRenameTable() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->rename('people');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table "users" rename to "people"', $statements[0]);
    }

    public function testRenameColumnUsingNativeSchemaOperations() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->renameColumn('from', 'to');

        $connection = $this->getConnection(true);
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table "users" rename column "from" to "to"', $statements[0]);
    }

    public function testRenameIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $command = new CBase_Fluent(['from' => 'foo', 'to' => 'bar']);

        $sql = $this->getGrammar()->compileRenameIndex($blueprint, $command);

        $this->assertSame('alter index "foo" rename to "bar"', $sql);
    }

    public function testAddingPrimaryKey() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->primary(['foo', 'bar']);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table "users" add primary key ("foo", "bar")', $statements[0]);
    }

    public function testAddingUniqueKey() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->unique('foo', 'bar');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table "users" add constraint "bar" unique ("foo")', $statements[0]);
    }

    public function testAddingUniqueKeyWithDeferrable() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->unique('foo', 'bar')->deferrable()->initiallyImmediate();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'alter table "users" add constraint "bar" unique ("foo") deferrable initially immediate',
            $statements[0]
        );
    }

    public function testAddingIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->index(['foo', 'bar'], 'baz');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('create index "baz" on "users" ("foo", "bar")', $statements[0]);
    }

    public function testAddingIndexWithAlgorithm() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->index('foo', 'baz', 'gin');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('create index "baz" on "users" using gin ("foo")', $statements[0]);
    }

    public function testAddingFullTextIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->fullText('body', 'baz');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            "create index \"baz\" on \"users\" using gin ((to_tsvector('english', \"body\")))",
            $statements[0]
        );
    }

    public function testAddingSpatialIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('geo');
        $blueprint->spatialIndex('coordinates', 'baz');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('create index "baz" on "geo" using gist ("coordinates")', $statements[0]);
    }

    public function testAddingForeignKey() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $foreign = $blueprint->foreign('foo_id')->references('id')->on('orders');
        $foreign->onDelete('cascade')->onUpdate('restrict');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'alter table "users" add constraint "users_foo_id_foreign" foreign key ("foo_id") references "orders" ("id") on delete cascade on update restrict',
            $statements[0]
        );
    }

    public function testAddingForeignKeyDeferrableAndNotValid() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $foreign = $blueprint->foreign('foo_id')->references('id')->on('orders');
        $foreign->deferrable()->initiallyImmediate()->notValid();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'alter table "users" add constraint "users_foo_id_foreign" foreign key ("foo_id") references "orders" ("id") deferrable initially immediate not valid',
            $statements[0]
        );
    }

    public function testDropPrimary() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropPrimary();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table "users" drop constraint "users_pkey"', $statements[0]);
    }

    public function testDropUnique() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropUnique('users_foo_unique');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table "users" drop constraint "users_foo_unique"', $statements[0]);
    }

    public function testDropIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropIndex(['foo']);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('drop index "users_foo_index"', $statements[0]);
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
        $this->assertSame('SET CONSTRAINTS ALL IMMEDIATE;', $grammar->compileEnableForeignKeyConstraints());
        $this->assertSame('SET CONSTRAINTS ALL DEFERRED;', $grammar->compileDisableForeignKeyConstraints());
    }

    public function testCompileColumnComment() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $column = new CBase_Fluent(['name' => 'email', 'comment' => 'the email column', 'change' => false]);
        $command = new CBase_Fluent(['column' => $column]);

        $sql = $this->getGrammar()->compileComment($blueprint, $command);

        $this->assertSame(
            "comment on column \"users\".\"email\" is 'the email column'",
            $sql
        );
    }

    public function testCompileTableComment() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $command = new CBase_Fluent(['comment' => "it's a users table"]);

        $sql = $this->getGrammar()->compileTableComment($blueprint, $command);

        $this->assertSame("comment on table \"users\" is 'it''s a users table'", $sql);
    }

    public function testAutoIncrementStartingValue() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $column = new CBase_Fluent(['name' => 'id', 'autoIncrement' => true, 'startingValue' => 100]);
        $command = new CBase_Fluent(['column' => $column]);

        $sql = $this->getGrammar()->compileAutoIncrementStartingValues($blueprint, $command);

        $this->assertSame('alter sequence users_id_seq restart with 100', $sql);
    }

    public function testCreateDatabase() {
        $connection = m::mock(CDatabase_Connection::class);
        $connection->shouldReceive('getConfig')->with('charset')->andReturn('utf8');

        $sql = $this->getGrammar()->compileCreateDatabase('my_database', $connection);

        $this->assertSame('create database "my_database" encoding "utf8"', $sql);
    }

    public function testDropDatabaseIfExists() {
        $this->assertSame(
            'drop database if exists "my_database"',
            $this->getGrammar()->compileDropDatabaseIfExists('my_database')
        );
    }

    public function testCompileChangeIsNotSupportedWithoutNativeSchemaOperations() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->string('a')->nullable()->change();

        $connection = $this->getConnection(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This database driver does not support modifying columns.');

        $blueprint->toSql($connection, $this->getGrammar($connection));
    }

    public function testCompileChangeWithNativeSchemaOperations() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->string('a')->nullable()->change();

        $connection = $this->getConnection(true);
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'alter table "users" alter column "a" type varchar(255), alter column "a" drop not null, '
            . 'alter column "a" drop default, alter column "a" drop identity if exists',
            $statements[0]
        );
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
            'create table "t" ("a" char(10) not null, "b" varchar(20) not null, "c" text not null, '
            . '"d" text not null, "e" text not null)',
            $statements[0]
        );
    }

    public function testIntegerColumnTypesUseSerialWhenAutoIncrementing() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->tinyInteger('a');
        $blueprint->smallInteger('b');
        $blueprint->mediumInteger('c');
        $blueprint->integer('d');
        $blueprint->bigInteger('e');
        $blueprint->increments('f');
        $blueprint->bigIncrements('g');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" smallint not null, "b" smallint not null, "c" integer not null, '
            . '"d" integer not null, "e" bigint not null, "f" serial not null primary key, '
            . '"g" bigserial not null primary key)',
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
            'create table "t" ("a" double precision not null, "b" double precision not null, "c" decimal(8, 2) not null)',
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
            "create table \"t\" (\"a\" boolean not null, \"b\" varchar(255) check (\"b\" in ('easy', 'hard')) not null, "
            . '"c" json not null, "d" jsonb not null)',
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
        $blueprint->timestampTz('e', 1);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" date not null, "b" timestamp(2) without time zone not null, '
            . '"c" time(3) without time zone not null, "d" timestamp(1) without time zone not null, '
            . '"e" timestamp(1) with time zone not null)',
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
            'create table "t" ("a" bytea not null, "b" uuid not null, "c" inet not null, "d" macaddr not null)',
            $statements[0]
        );
    }

    public function testGeometryAndGeographyColumnTypes() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->geometry('a');
        $blueprint->addColumn('geometry', 'b', ['subtype' => 'point', 'srid' => 4326]);
        $blueprint->addColumn('geography', 'c', ['subtype' => 'point']);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" geometry not null, "b" geometry(point,4326) not null, "c" geography(point) not null)',
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
            'create table "t" ("a" integer not null, "b" integer not null generated always as (a + 1), '
            . '"c" integer not null generated always as (a + 2) stored)',
            $statements[0]
        );
    }

    public function testGeneratedAsIdentityModifier() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->integer('a')->generatedAs()->always();
        $blueprint->integer('b')->generatedAs('start with 10');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" integer not null generated always as identity, '
            . '"b" integer not null generated by default as identity (start with 10))',
            $statements[0]
        );
    }

    public function testNullableAndDefaultModifiers() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->string('a')->nullable()->default('foo');
        $blueprint->integer('b')->default(0);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            "create table \"t\" (\"a\" varchar(255) null default 'foo', \"b\" integer not null default '0')",
            $statements[0]
        );
    }

    public function testCollateModifier() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->string('a')->collation('de_DE');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table "t" ("a" varchar(255) collate "de_DE" not null)',
            $statements[0]
        );
    }
}
