<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Tests for CDatabase_Schema_Grammar_MySqlGrammar - asserts the actual SQL strings
 * produced when compiling a CDatabase_Schema_Blueprint against the MySQL schema grammar.
 *
 * No real database/PDO connection is used - the CDatabase_Connection is mocked with
 * Mockery, following the same convention as DatabaseQueryBuilderTest.
 */
class DatabaseMySqlSchemaGrammarTest extends TestCase {
    protected function tearDown(): void {
        m::close();
    }

    /**
     * @param bool $usingNative
     *
     * @return \Mockery\MockInterface
     */
    protected function getConnection($usingNative = true) {
        $connection = m::mock(CDatabase_Connection::class);
        $connection->shouldReceive('getConfig')->andReturn(null)->byDefault();
        $connection->shouldReceive('getTablePrefix')->andReturn('');
        $connection->shouldReceive('usingNativeSchemaOperations')->andReturn($usingNative);

        return $connection;
    }

    /**
     * @param null|\Mockery\MockInterface $connection
     *
     * @return CDatabase_Schema_Grammar_MySqlGrammar
     */
    protected function getGrammar($connection = null) {
        $grammar = new CDatabase_Schema_Grammar_MySqlGrammar();
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
            'create table `users` (`id` int unsigned not null auto_increment primary key, `email` varchar(255) not null)',
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
            'create temporary table `users` (`email` varchar(255) not null)',
            $statements[0]
        );
    }

    public function testCreateTableWithExplicitPrimaryKey() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->create();
        $blueprint->string('email');
        $blueprint->primary('email');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table `users` (`email` varchar(255) not null, primary key (`email`))',
            $statements[0]
        );
    }

    public function testCreateTableWithCompositePrimaryKeyAndAlgorithm() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->create();
        $blueprint->string('first');
        $blueprint->string('last');
        $blueprint->primary(['first', 'last'], null, 'hash');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table `users` (`first` varchar(255) not null, `last` varchar(255) not null, primary key using hash(`first`, `last`))',
            $statements[0]
        );
    }

    public function testCreateTableWithEngineFromBlueprint() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->create();
        $blueprint->string('email');
        $blueprint->engine = 'InnoDB';

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table `users` (`email` varchar(255) not null) engine = InnoDB',
            $statements[0]
        );
    }

    public function testCreateTableWithCharsetAndCollationFromBlueprint() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->create();
        $blueprint->string('email');
        $blueprint->charset = 'utf8mb4';
        $blueprint->collation = 'utf8mb4_unicode_ci';

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            "create table `users` (`email` varchar(255) not null) default character set utf8mb4 collate 'utf8mb4_unicode_ci'",
            $statements[0]
        );
    }

    public function testCreateTableWithEngineAndCharsetFromConnectionConfig() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->create();
        $blueprint->string('email');

        $connection = m::mock(CDatabase_Connection::class);
        $connection->shouldReceive('getTablePrefix')->andReturn('');
        $connection->shouldReceive('usingNativeSchemaOperations')->andReturn(true);
        $connection->shouldReceive('getConfig')->with('charset')->andReturn('utf8mb4');
        $connection->shouldReceive('getConfig')->with('collation')->andReturn('utf8mb4_unicode_ci');
        $connection->shouldReceive('getConfig')->with('engine')->andReturn('MyISAM');

        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            "create table `users` (`email` varchar(255) not null) default character set utf8mb4 collate 'utf8mb4_unicode_ci' engine = MyISAM",
            $statements[0]
        );
    }

    public function testAddColumns() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->string('name')->nullable();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertCount(1, $statements);
        $this->assertSame(
            'alter table `users` add `name` varchar(255) null',
            $statements[0]
        );
    }

    public function testAddMultipleColumns() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->string('name');
        $blueprint->integer('votes');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'alter table `users` add `name` varchar(255) not null, add `votes` int not null',
            $statements[0]
        );
    }

    public function testAddColumnAfterAnotherColumn() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->string('name')->after('id');
        $blueprint->string('email')->first();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'alter table `users` add `name` varchar(255) not null after `id`, add `email` varchar(255) not null first',
            $statements[0]
        );
    }

    public function testDropColumn() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropColumn('votes');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `users` drop `votes`', $statements[0]);
    }

    public function testDropMultipleColumns() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropColumn(['votes', 'name']);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `users` drop `votes`, drop `name`', $statements[0]);
    }

    public function testDropTable() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->drop();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('drop table `users`', $statements[0]);
    }

    public function testDropTableIfExists() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropIfExists();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('drop table if exists `users`', $statements[0]);
    }

    public function testRenameTable() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->rename('people');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('rename table `users` to `people`', $statements[0]);
    }

    public function testRenameColumnUsingNativeSchemaOperations() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->renameColumn('from', 'to');

        $connection = $this->getConnection(true);
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `users` rename column `from` to `to`', $statements[0]);
    }

    public function testRenameColumnWithoutNativeSchemaOperationsFallsBackToBaseGrammar() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->renameColumn('from', 'to');

        $connection = $this->getConnection(false);
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `users` rename column `from` to `to`', $statements[0]);
    }

    public function testRenameIndex() {
        // NB: CDatabase_Schema_Blueprint does not expose a public renameIndex() builder
        // method, even though the grammar implements compileRenameIndex(). Exercise the
        // grammar method directly with a hand-built command, as the Blueprint API cannot
        // produce this command itself.
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $command = new CBase_Fluent(['from' => 'foo', 'to' => 'bar']);

        $sql = $this->getGrammar()->compileRenameIndex($blueprint, $command);

        $this->assertSame('alter table `users` rename index `foo` to `bar`', $sql);
    }

    public function testAddingPrimaryKey() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->primary('foo', 'bar');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `users` add primary key `bar`(`foo`)', $statements[0]);
    }

    public function testAddingUniqueKey() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->unique('foo', 'bar');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `users` add unique `bar`(`foo`)', $statements[0]);
    }

    public function testAddingIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->index(['foo', 'bar'], 'baz');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `users` add index `baz`(`foo`, `bar`)', $statements[0]);
    }

    public function testAddingIndexWithAlgorithm() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->index('foo', 'baz', 'hash');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `users` add index `baz` using hash(`foo`)', $statements[0]);
    }

    public function testAddingFullTextIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->fullText('body', 'baz');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `users` add fulltext `baz`(`body`)', $statements[0]);
    }

    public function testAddingSpatialIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('geo');
        $blueprint->spatialIndex('coordinates', 'baz');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `geo` add spatial index `baz`(`coordinates`)', $statements[0]);
    }

    public function testAddingForeignKey() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $foreign = $blueprint->foreign('foo_id')->references('id')->on('orders');
        $foreign->onDelete('cascade')->onUpdate('restrict');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'alter table `users` add constraint `users_foo_id_foreign` foreign key (`foo_id`) references `orders` (`id`) on delete cascade on update restrict',
            $statements[0]
        );
    }

    public function testDropPrimary() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropPrimary();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `users` drop primary key', $statements[0]);
    }

    public function testDropUnique() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropUnique('users_foo_unique');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `users` drop index `users_foo_unique`', $statements[0]);
    }

    public function testDropIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropIndex(['foo']);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `users` drop index `users_foo_index`', $statements[0]);
    }

    public function testDropFullText() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropFullText(['body']);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `users` drop index `users_body_fulltext`', $statements[0]);
    }

    public function testDropSpatialIndex() {
        $blueprint = new CDatabase_Schema_Blueprint('geo');
        $blueprint->dropSpatialIndex(['coordinates']);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `geo` drop index `geo_coordinates_spatialindex`', $statements[0]);
    }

    public function testDropForeign() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $blueprint->dropForeign('users_foo_id_foreign');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('alter table `users` drop foreign key `users_foo_id_foreign`', $statements[0]);
    }

    public function testEnableForeignKeyConstraints() {
        $this->assertSame('SET FOREIGN_KEY_CHECKS=1;', $this->getGrammar()->compileEnableForeignKeyConstraints());
    }

    public function testDisableForeignKeyConstraints() {
        $this->assertSame('SET FOREIGN_KEY_CHECKS=0;', $this->getGrammar()->compileDisableForeignKeyConstraints());
    }

    public function testCompileChangeIsNotSupported() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $command = new CBase_Fluent(['name' => 'change']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This database driver does not support modifying columns.');

        $this->getGrammar()->compileChange($blueprint, $command);
    }

    public function testCreateDatabaseWithCharsetAndCollation() {
        $connection = m::mock(CDatabase_Connection::class);
        $connection->shouldReceive('getConfig')->with('charset')->andReturn('utf8mb4');
        $connection->shouldReceive('getConfig')->with('collation')->andReturn('utf8mb4_unicode_ci');

        $sql = $this->getGrammar()->compileCreateDatabase('my_database', $connection);

        $this->assertSame(
            'create database `my_database` default character set `utf8mb4` default collate `utf8mb4_unicode_ci`',
            $sql
        );
    }

    public function testCreateDatabaseWithoutCharsetAndCollation() {
        $connection = m::mock(CDatabase_Connection::class);
        $connection->shouldReceive('getConfig')->with('charset')->andReturn(null);
        $connection->shouldReceive('getConfig')->with('collation')->andReturn(null);

        $sql = $this->getGrammar()->compileCreateDatabase('my_database', $connection);

        $this->assertSame('create database `my_database`', $sql);
    }

    public function testDropDatabaseIfExists() {
        $this->assertSame(
            'drop database if exists `my_database`',
            $this->getGrammar()->compileDropDatabaseIfExists('my_database')
        );
    }

    public function testGetAllTablesAndViews() {
        $this->assertSame("SHOW FULL TABLES WHERE table_type = 'BASE TABLE'", $this->getGrammar()->compileGetAllTables());
        $this->assertSame("SHOW FULL TABLES WHERE table_type = 'VIEW'", $this->getGrammar()->compileGetAllViews());
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
            'create table `t` (`a` char(10) not null, `b` varchar(20) not null, `c` text not null, '
            . '`d` mediumtext not null, `e` longtext not null)',
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
        $blueprint->unsignedBigInteger('f');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table `t` (`a` tinyint not null, `b` smallint not null, `c` mediumint not null, '
            . '`d` int not null, `e` bigint not null, `f` bigint unsigned not null)',
            $statements[0]
        );
    }

    public function testNumericColumnTypes() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->float('a');
        $blueprint->double('b');
        $blueprint->decimal('c');
        $blueprint->unsignedDecimal('d', 5, 1);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table `t` (`a` double(8, 2) not null, `b` double not null, `c` decimal(8, 2) not null, '
            . '`d` decimal(5, 1) unsigned not null)',
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
            "create table `t` (`a` tinyint(1) not null, `b` enum('easy', 'hard') not null, "
            . '`c` json not null, `d` json not null)',
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
            'create table `t` (`a` date not null, `b` datetime(2) not null, `c` time(3) not null, `d` timestamp(1) not null)',
            $statements[0]
        );
    }

    public function testTimestampUseCurrentAndOnUpdate() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->timestamp('a')->useCurrent()->useCurrentOnUpdate();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table `t` (`a` timestamp default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP not null)',
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
            'create table `t` (`a` blob not null, `b` char(36) not null, `c` varchar(45) not null, `d` varchar(17) not null)',
            $statements[0]
        );
    }

    public function testSpatialColumnTypes() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->geometry('a');
        $blueprint->point('b');
        $blueprint->lineString('c');
        $blueprint->polygon('d');
        $blueprint->geometryCollection('e');
        $blueprint->multiPoint('f');
        $blueprint->multiLineString('g');
        $blueprint->multiPolygon('h');

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            'create table `t` (`a` geometry not null, `b` point not null, `c` linestring not null, '
            . '`d` polygon not null, `e` geometrycollection not null, `f` multipoint not null, '
            . '`g` multilinestring not null, `h` multipolygon not null)',
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

    public function testNullableAndDefaultModifiers() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->string('a')->nullable()->default('foo');
        $blueprint->integer('b')->default(0);
        $blueprint->boolean('c')->default(true);

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            "create table `t` (`a` varchar(255) null default 'foo', `b` int not null default '0', "
            . "`c` tinyint(1) not null default '1')",
            $statements[0]
        );
    }

    public function testUnsignedCharsetCollateAndCommentModifiers() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->integer('a')->unsigned();
        $blueprint->string('b')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
        $blueprint->string('c')->comment("it's a comment");

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame(
            "create table `t` (`a` int unsigned not null, `b` varchar(255) character set utf8mb4 collate 'utf8mb4_unicode_ci' not null, "
            . "`c` varchar(255) not null comment 'it\\'s a comment')",
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
            'create table `t` (`a` int not null, `b` int as (a + 1), `c` int as (a + 2) stored)',
            $statements[0]
        );
    }

    public function testInvisibleModifier() {
        $blueprint = new CDatabase_Schema_Blueprint('t');
        $blueprint->create();
        $blueprint->string('a')->invisible();

        $connection = $this->getConnection();
        $statements = $blueprint->toSql($connection, $this->getGrammar($connection));

        $this->assertSame('create table `t` (`a` varchar(255) not null invisible)', $statements[0]);
    }

    public function testAutoIncrementStartingValue() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $column = new CBase_Fluent(['autoIncrement' => true, 'startingValue' => 100]);
        $command = new CBase_Fluent(['column' => $column]);

        $sql = $this->getGrammar()->compileAutoIncrementStartingValues($blueprint, $command);

        $this->assertSame('alter table `users` auto_increment = 100', $sql);
    }

    public function testAutoIncrementStartingValueIsNullWhenNotAutoIncrementing() {
        $blueprint = new CDatabase_Schema_Blueprint('users');
        $column = new CBase_Fluent(['autoIncrement' => false]);
        $command = new CBase_Fluent(['column' => $column]);

        $sql = $this->getGrammar()->compileAutoIncrementStartingValues($blueprint, $command);

        $this->assertNull($sql);
    }
}
