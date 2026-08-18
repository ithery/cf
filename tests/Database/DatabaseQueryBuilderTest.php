<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Pure unit tests for CDatabase_Query_Builder::toSql() / getBindings() across
 * all four grammar dialects (MySQL, Postgres, SQLite, SQL Server).
 *
 * No real database connection is ever created. The CDatabase_Connection the
 * builder holds a reference to is a Mockery mock whose only job is to hand
 * back a real (non-mocked) grammar/processor instance, because
 * CDatabase_Query_Builder::__construct() ignores the $grammar/$processor
 * constructor arguments and instead pulls them via
 * $connection->getQueryGrammar() / $connection->getPostProcessor() (see
 * Builder.php around line 215-223). See the getConnection()/getXBuilder()
 * helpers below for the workaround.
 */
class DatabaseQueryBuilderTest extends TestCase {
    protected function tearDown(): void {
        m::close();
    }

    /**
     * Build a Mockery-mocked CDatabase_Connection that is wired to hand back
     * the given (real, concrete) grammar/processor instances.
     *
     * NOTE: CDatabase_Query_Builder::__construct() does:
     *     $this->grammar = $connection->getQueryGrammar();
     *     $this->processor = $connection->getPostProcessor();
     * completely ignoring the $grammar/$processor arguments passed to the
     * constructor. So the *only* way to control which grammar/processor a
     * builder ends up using is to control what the connection mock returns
     * from getQueryGrammar()/getPostProcessor() -- passing them to `new
     * CDatabase_Query_Builder(...)` directly has no effect. We still pass
     * them along below to mirror Laravel's test style / keep the door open
     * in case that constructor is ever fixed to honor its own arguments.
     *
     * @param CDatabase_Query_Grammar    $grammar
     * @param CDatabase_Query_Processor  $processor
     * @param string                     $prefix
     *
     * @return \Mockery\MockInterface|CDatabase_Connection
     */
    protected function getConnection($grammar, $processor, $prefix = '') {
        $connection = m::mock(CDatabase_Connection::class);
        $connection->shouldReceive('getQueryGrammar')->andReturn($grammar);
        $connection->shouldReceive('getPostProcessor')->andReturn($processor);
        $connection->shouldReceive('getDatabaseName')->andReturn('database');
        $connection->shouldReceive('getTablePrefix')->andReturn($prefix);
        $connection->shouldReceive('raw')->andReturnUsing(function ($value) {
            return new CDatabase_Query_Expression($value);
        });

        // Grammar::wrapTable()/wrap() reach back into the connection for the
        // table prefix (and for escape()), just like Laravel's own Grammar.
        $grammar->setConnection($connection);

        return $connection;
    }

    /**
     * @return CDatabase_Query_Builder
     */
    protected function getBuilder($prefix = '') {
        $grammar = new CDatabase_Query_Grammar();
        $processor = new CDatabase_Query_Processor();

        return new CDatabase_Query_Builder($this->getConnection($grammar, $processor, $prefix), $grammar, $processor);
    }

    /**
     * @return CDatabase_Query_Builder
     */
    protected function getMySqlBuilder($prefix = '') {
        $grammar = new CDatabase_Query_Grammar_MySqlGrammar();
        $processor = new CDatabase_Query_Processor_MySqlProcessor();

        return new CDatabase_Query_Builder($this->getConnection($grammar, $processor, $prefix), $grammar, $processor);
    }

    /**
     * @return CDatabase_Query_Builder
     */
    protected function getPostgresBuilder($prefix = '') {
        $grammar = new CDatabase_Query_Grammar_PostgresGrammar();
        $processor = new CDatabase_Query_Processor_PostgresProcessor();

        return new CDatabase_Query_Builder($this->getConnection($grammar, $processor, $prefix), $grammar, $processor);
    }

    /**
     * @return CDatabase_Query_Builder
     */
    protected function getSQLiteBuilder($prefix = '') {
        $grammar = new CDatabase_Query_Grammar_SqliteGrammar();
        $processor = new CDatabase_Query_Processor_SqliteProcessor();

        return new CDatabase_Query_Builder($this->getConnection($grammar, $processor, $prefix), $grammar, $processor);
    }

    /**
     * @return CDatabase_Query_Builder
     */
    protected function getSqlServerBuilder($prefix = '') {
        $grammar = new CDatabase_Query_Grammar_SqlServerGrammar();
        $processor = new CDatabase_Query_Processor_SqlServerProcessor();

        return new CDatabase_Query_Builder($this->getConnection($grammar, $processor, $prefix), $grammar, $processor);
    }

    // -----------------------------------------------------------------
    // Basic select / columns / distinct / aliasing / from
    // -----------------------------------------------------------------

    public function testBasicSelect() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users');
        $this->assertSame('select * from "users"', $builder->toSql());
    }

    public function testBasicSelectMySql() {
        $builder = $this->getMySqlBuilder();
        $builder->select('*')->from('users');
        $this->assertSame('select * from `users`', $builder->toSql());
    }

    public function testBasicSelectSqlServer() {
        $builder = $this->getSqlServerBuilder();
        $builder->select('*')->from('users');
        $this->assertSame('select * from [users]', $builder->toSql());
    }

    public function testBasicSelectSqlite() {
        $builder = $this->getSQLiteBuilder();
        $builder->select('*')->from('users');
        $this->assertSame('select * from "users"', $builder->toSql());
    }

    public function testBasicSelectWithGivenColumns() {
        $builder = $this->getBuilder();
        $builder->select('foo', 'bar')->from('users');
        $this->assertSame('select "foo", "bar" from "users"', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select(['foo', 'bar'])->from('users');
        $this->assertSame('select "foo", "bar" from "users"', $builder->toSql());
    }

    public function testBasicTableWrappingProtectsQuotationMarks() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('some"table');
        $this->assertSame('select * from "some""table"', $builder->toSql());
    }

    public function testAddingSelects() {
        // NOTE: unlike Laravel, CDatabase_Query_Builder::addSelect() does NOT
        // deduplicate columns (see Builder.php addSelect() - it always does
        // `$this->columns[] = $column;` with no array_unique/diff check), so
        // the repeated "bar" shows up twice here.
        $builder = $this->getBuilder();
        $builder->select('foo')->addSelect('bar')->addSelect(['baz', 'boom'])->addSelect('bar')->from('users');
        $this->assertSame('select "foo", "bar", "baz", "boom", "bar" from "users"', $builder->toSql());
    }

    public function testBasicSelectWithPrefix() {
        $builder = $this->getBuilder('prefix_');
        $builder->select('*')->from('users');
        $this->assertSame('select * from "prefix_users"', $builder->toSql());
    }

    public function testBasicSelectDistinct() {
        $builder = $this->getBuilder();
        $builder->distinct()->select('foo', 'bar')->from('users');
        $this->assertSame('select distinct "foo", "bar" from "users"', $builder->toSql());
    }

    public function testBasicSelectDistinctOnColumnsPostgres() {
        $builder = $this->getPostgresBuilder();
        $builder->distinct('foo')->select('foo', 'bar')->from('users');
        $this->assertSame('select distinct on ("foo") "foo", "bar" from "users"', $builder->toSql());
    }

    public function testBasicAlias() {
        $builder = $this->getBuilder();
        $builder->select('foo as bar')->from('users');
        $this->assertSame('select "foo" as "bar" from "users"', $builder->toSql());
    }

    public function testAliasWrappingAsWholeConstant() {
        $builder = $this->getBuilder();
        $builder->select('x.y as foo.bar')->from('baz');
        $this->assertSame('select "x"."y" as "foo.bar" from "baz"', $builder->toSql());
    }

    public function testSelectRaw() {
        $builder = $this->getBuilder();
        $builder->selectRaw('substr(foo, 1, 1) as first_letter')->from('users');
        $this->assertSame('select substr(foo, 1, 1) as first_letter from "users"', $builder->toSql());
    }

    public function testSelectRawWithBindings() {
        $builder = $this->getBuilder();
        $builder->selectRaw('substr(foo, ?, ?) as sub', [1, 3])->from('users');
        $this->assertSame('select substr(foo, ?, ?) as sub from "users"', $builder->toSql());
        $this->assertEquals([1, 3], $builder->getBindings());
    }

    public function testSelectSub() {
        $builder = $this->getBuilder();
        $builder->from('one')->selectSub(function ($query) {
            $query->from('two')->select('baz')->where('subkey', '=', 'subval');
        }, 'sub');
        $this->assertSame(
            'select (select "baz" from "two" where "subkey" = ?) as "sub" from "one"',
            $builder->toSql()
        );
        $this->assertEquals(['subval'], $builder->getBindings());
    }

    public function testSelectSubWithBuilderInstance() {
        $builder = $this->getBuilder();
        $sub = $this->getBuilder()->from('two')->select('baz')->where('subkey', '=', 'subval');
        $builder->from('one')->selectSub($sub, 'sub');
        $this->assertSame(
            'select (select "baz" from "two" where "subkey" = ?) as "sub" from "one"',
            $builder->toSql()
        );
        $this->assertEquals(['subval'], $builder->getBindings());
    }

    public function testFromRaw() {
        $builder = $this->getBuilder();
        $builder->select('*')->fromRaw('(select max(last_seen_at) as last_seen_at from "user_sessions") as "sessions"');
        $this->assertSame('select * from (select max(last_seen_at) as last_seen_at from "user_sessions") as "sessions"', $builder->toSql());
    }

    public function testFromRawWithWhereOnTheMainQuery() {
        $builder = $this->getBuilder();
        $builder->select('*')->fromRaw('(select max(last_seen_at) as last_seen_at from "sessions") as "last_seen_at"')->where('last_seen_at', '>', '1520652582');
        $this->assertSame('select * from (select max(last_seen_at) as last_seen_at from "sessions") as "last_seen_at" where "last_seen_at" > ?', $builder->toSql());
        $this->assertEquals(['1520652582'], $builder->getBindings());
    }

    public function testFromSub() {
        $builder = $this->getBuilder();
        $builder->fromSub(function ($query) {
            $query->select(new CDatabase_Query_Expression('max(last_seen_at) as last_seen_at'))->from('user_sessions')->where('foo', '=', '1');
        }, 'sessions')->where('bar', '<', '10');
        $this->assertSame('select * from (select max(last_seen_at) as last_seen_at from "user_sessions" where "foo" = ?) as "sessions" where "bar" < ?', $builder->toSql());
        $this->assertEquals(['1', '10'], $builder->getBindings());
    }

    public function testFromSubWithoutBindings() {
        $builder = $this->getBuilder();
        $builder->fromSub(function ($query) {
            $query->select(new CDatabase_Query_Expression('max(last_seen_at) as last_seen_at'))->from('user_sessions');
        }, 'sessions');
        $this->assertSame('select * from (select max(last_seen_at) as last_seen_at from "user_sessions") as "sessions"', $builder->toSql());
    }

    public function testFromWithAlias() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users', 'people');
        $this->assertSame('select * from "users" as "people"', $builder->toSql());
    }

    public function testMySqlUseIndex() {
        $builder = $this->getMySqlBuilder();
        $builder->select('*')->from('users')->useIndex('users_index');
        $this->assertSame('select * from `users` use index(users_index)', $builder->toSql());
    }

    // -----------------------------------------------------------------
    // Joins
    // -----------------------------------------------------------------

    public function testBasicJoins() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->join('contacts', 'users.id', '=', 'contacts.id');
        $this->assertSame('select * from "users" inner join "contacts" on "users"."id" = "contacts"."id"', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->leftJoin('photos', 'users.id', '=', 'photos.id');
        $this->assertSame('select * from "users" left join "photos" on "users"."id" = "photos"."id"', $builder->toSql());
    }

    public function testRightJoin() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->rightJoin('photos', 'users.id', '=', 'photos.id');
        $this->assertSame('select * from "users" right join "photos" on "users"."id" = "photos"."id"', $builder->toSql());
    }

    public function testCrossJoin() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('sizes')->crossJoin('colors');
        $this->assertSame('select * from "sizes" cross join "colors"', $builder->toSql());
    }

    public function testCrossJoinWithCondition() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('tableB')->crossJoin('tableA', 'tableA.column1', '=', 'tableB.column2');
        $this->assertSame('select * from "tableB" cross join "tableA" on "tableA"."column1" = "tableB"."column2"', $builder->toSql());
    }

    public function testJoinWhere() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->joinWhere('contacts', 'users.id', '=', 'foo', 'inner');
        $this->assertSame('select * from "users" inner join "contacts" on "users"."id" = ?', $builder->toSql());
        $this->assertEquals(['foo'], $builder->getBindings());
    }

    public function testJoinsWithNestedConditions() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->leftJoin('contacts', function ($j) {
            $j->on('users.id', '=', 'contacts.id')->where(function ($j) {
                $j->where('contacts.country', '=', 'US')->orWhere('contacts.is_partner', '=', 1);
            });
        });
        $this->assertSame('select * from "users" left join "contacts" on "users"."id" = "contacts"."id" and ("contacts"."country" = ? or "contacts"."is_partner" = ?)', $builder->toSql());
        $this->assertEquals(['US', 1], $builder->getBindings());
    }

    public function testJoinsWithAdvancedConditions() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->leftJoin('contacts', function ($j) {
            $j->where('role', 'admin')
                ->orWhereNull('contacts.disabled')
                ->orWhereRaw('year(contacts.created_at) = 2016');
        });
        $this->assertSame('select * from "users" left join "contacts" on "role" = ? or "contacts"."disabled" is null or year(contacts.created_at) = 2016', $builder->toSql());
        $this->assertEquals(['admin'], $builder->getBindings());
    }

    public function testJoinSub() {
        $builder = $this->getBuilder();
        $builder->from('users')->joinSub('select * from "contacts"', 'sub', 'users.id', '=', 'sub.id');
        $this->assertSame('select * from "users" inner join (select * from "contacts") as "sub" on "users"."id" = "sub"."id"', $builder->toSql());

        $builder = $this->getBuilder();
        $eloquentBuilder = $this->getBuilder()->from('contacts');
        $builder->from('users')->joinSub($eloquentBuilder, 'sub', 'users.id', '=', 'sub.id');
        $this->assertSame('select * from "users" inner join (select * from "contacts") as "sub" on "users"."id" = "sub"."id"', $builder->toSql());

        $builder = $this->getBuilder();
        $sub1 = $this->getBuilder()->from('contacts')->where('name', 'foo');
        $sub2 = $this->getBuilder()->from('contacts')->where('name', 'bar');
        $builder->from('users')
            ->joinSub($sub1, 'sub1', 'users.id', '=', 1, 'inner', true)
            ->joinSub($sub2, 'sub2', 'users.id', '=', 'sub2.user_id');
        $expected = 'select * from "users" ';
        $expected .= 'inner join (select * from "contacts" where "name" = ?) as "sub1" on "users"."id" = ? ';
        $expected .= 'inner join (select * from "contacts" where "name" = ?) as "sub2" on "users"."id" = "sub2"."user_id"';
        $this->assertSame($expected, $builder->toSql());
        $this->assertEquals(['foo', 1, 'bar'], $builder->getBindings());
    }

    public function testLeftJoinSub() {
        $builder = $this->getBuilder();
        $builder->from('users')->leftJoinSub($this->getBuilder()->from('contacts'), 'sub', 'users.id', '=', 'sub.id');
        $this->assertSame('select * from "users" left join (select * from "contacts") as "sub" on "users"."id" = "sub"."id"', $builder->toSql());
    }

    public function testRightJoinSub() {
        $builder = $this->getBuilder();
        $builder->from('users')->rightJoinSub($this->getBuilder()->from('contacts'), 'sub', 'users.id', '=', 'sub.id');
        $this->assertSame('select * from "users" right join (select * from "contacts") as "sub" on "users"."id" = "sub"."id"', $builder->toSql());
    }

    // -----------------------------------------------------------------
    // Group by / having
    // -----------------------------------------------------------------

    public function testGroupBys() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->groupBy('email');
        $this->assertSame('select * from "users" group by "email"', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->groupBy('id', 'email');
        $this->assertSame('select * from "users" group by "id", "email"', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->groupBy(['id', 'email']);
        $this->assertSame('select * from "users" group by "id", "email"', $builder->toSql());
    }

    public function testGroupByRaw() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->groupByRaw('DATE(created_at)');
        $this->assertSame('select * from "users" group by DATE(created_at)', $builder->toSql());
    }

    public function testOrderByThenGroupByRawWithBindings() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->groupByRaw('DATE(created_at), ?', ['foo']);
        $this->assertEquals(['foo'], $builder->getBindings());
    }

    public function testHavings() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->having('email', '>', 1);
        $this->assertSame('select * from "users" having "email" > ?', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')
            ->orHaving('email', '=', 'test@example.com')
            ->orHaving('email', '=', 'test2@example.com');
        $this->assertSame('select * from "users" having "email" = ? or "email" = ?', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->groupBy('email')->having('email', '>', 1);
        $this->assertSame('select * from "users" group by "email" having "email" > ?', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select('email as foo_email')->from('users')->having('foo_email', '>', 1);
        $this->assertSame('select "email" as "foo_email" from "users" having "foo_email" > ?', $builder->toSql());
    }

    public function testHavingBetweens() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->havingBetween('id', [1, 2]);
        $this->assertSame('select * from "users" having "id" between ? and ?', $builder->toSql());
        $this->assertEquals([1, 2], $builder->getBindings());
    }

    public function testHavingRaw() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->havingRaw('user_foo < user_bar');
        $this->assertSame('select * from "users" having user_foo < user_bar', $builder->toSql());
    }

    public function testOrHavingRaw() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->having('baz', '=', 1)->orHavingRaw('user_foo < user_bar');
        $this->assertSame('select * from "users" having "baz" = ? or user_foo < user_bar', $builder->toSql());
    }

    // NOTE: nested/closure having() (Laravel's `having(function ($q) {...})`)
    // is NOT implemented in this framework. CDatabase_Query_Builder::having()
    // always treats $column as a plain column (type is hardcoded to 'Basic'
    // with no Closure detection/whereNested-style dispatch), even though
    // CDatabase_Query_Grammar::compileNestedHavings() exists on the grammar
    // side. Passing a Closure to having() fatals with a TypeError inside
    // Grammar::wrap() (stripos() expects a string, gets a Closure). So there
    // is no way to build a 'Nested' type having clause with this Builder -
    // skipped rather than blindly testing Laravel behavior that doesn't
    // exist here.

    // -----------------------------------------------------------------
    // Order by / limit / offset / forPage
    // -----------------------------------------------------------------

    public function testOrders() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->orderBy('email')->orderBy('age', 'desc');
        $this->assertSame('select * from "users" order by "email" asc, "age" desc', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->orderBy('email')->orderByRaw('"age" ? desc', ['foo']);
        $this->assertSame('select * from "users" order by "email" asc, "age" ? desc', $builder->toSql());
        $this->assertEquals(['foo'], $builder->getBindings());
    }

    public function testOrderByDesc() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->orderByDesc('name');
        $this->assertSame('select * from "users" order by "name" desc', $builder->toSql());
    }

    public function testReorder() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->orderBy('name');
        $this->assertSame('select * from "users" order by "name" asc', $builder->toSql());
        $builder->reorder();
        $this->assertSame('select * from "users"', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->orderBy('name');
        $this->assertSame('select * from "users" order by "name" asc', $builder->toSql());
        $builder->reorder('email', 'desc');
        $this->assertSame('select * from "users" order by "email" desc', $builder->toSql());
    }

    public function testOrderBySubQueries() {
        $expected = 'select * from "users" order by (select "created_at" from "logins" where "user_id" = "users"."id" limit 1)';
        $subQuery = function ($query) {
            return $query->select('created_at')->from('logins')->whereColumn('user_id', 'users.id')->limit(1);
        };

        $builder = $this->getBuilder()->select('*')->from('users')->orderBy($subQuery);
        $this->assertSame("{$expected} asc", $builder->toSql());

        $builder = $this->getBuilder()->select('*')->from('users')->orderBy($subQuery, 'desc');
        $this->assertSame("{$expected} desc", $builder->toSql());

        $builder = $this->getBuilder()->select('*')->from('users')->orderByDesc($subQuery);
        $this->assertSame("{$expected} desc", $builder->toSql());
    }

    public function testInRandomOrderMySql() {
        $builder = $this->getMySqlBuilder();
        $builder->select('*')->from('users')->inRandomOrder();
        $this->assertSame('select * from `users` order by RAND()', $builder->toSql());
    }

    public function testInRandomOrderWithSeedMySql() {
        $builder = $this->getMySqlBuilder();
        $builder->select('*')->from('users')->inRandomOrder('12345');
        $this->assertSame('select * from `users` order by RAND(12345)', $builder->toSql());
    }

    public function testInRandomOrderPostgres() {
        $builder = $this->getPostgresBuilder();
        $builder->select('*')->from('users')->inRandomOrder();
        $this->assertSame('select * from "users" order by RANDOM()', $builder->toSql());
    }

    public function testLimitsAndOffsets() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->offset(5)->limit(10);
        $this->assertSame('select * from "users" limit 10 offset 5', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->skip(5)->take(10);
        $this->assertSame('select * from "users" limit 10 offset 5', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->skip(-5)->take(10);
        $this->assertSame('select * from "users" limit 10 offset 0', $builder->toSql());
    }

    public function testForPage() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->forPage(2, 15);
        $this->assertSame('select * from "users" limit 15 offset 15', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->forPage(0, 15);
        $this->assertSame('select * from "users" limit 15 offset 0', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->forPage(-2, 15);
        $this->assertSame('select * from "users" limit 15 offset 0', $builder->toSql());
    }

    public function testSqlServerLimitsAndOffsets() {
        $builder = $this->getSqlServerBuilder();
        $builder->select('*')->from('users')->limit(10);
        $this->assertSame('select top 10 * from [users]', $builder->toSql());

        $builder = $this->getSqlServerBuilder();
        $builder->select('*')->from('users')->offset(10);
        $this->assertSame('select * from [users] order by (SELECT 0) offset 10 rows', $builder->toSql());

        $builder = $this->getSqlServerBuilder();
        $builder->select('*')->from('users')->offset(10)->limit(10);
        $this->assertSame('select * from [users] order by (SELECT 0) offset 10 rows fetch next 10 rows only', $builder->toSql());
    }

    // -----------------------------------------------------------------
    // Unions
    //
    // NOTE: unlike Laravel, CDatabase_Query_Grammar::compileUnion() does NOT
    // wrap the *unioned* query in parentheses - only the base/left-hand query
    // gets wrapped (via wrapUnion() applied to the base $sql before appending
    // compileUnions()). See Grammar.php compileUnion():
    //   return ($union['all'] ? ' union all ' : ' union ') . $union['query']->toSql();
    // Laravel's equivalent wraps both sides. This is a real, confirmed
    // behavioral difference (not a crash/invalid-SQL bug - `select ... union
    // select ...` is valid SQL, just without the parens Laravel would add),
    // so it is documented here rather than "fixed", per the task instructions
    // to prefer documenting surprising-but-working behavior over touching
    // widely-used compile code.
    // -----------------------------------------------------------------

    public function testUnions() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1);
        $builder->union($this->getBuilder()->select('*')->from('users')->where('id', '=', 2));
        $this->assertSame('(select * from "users" where "id" = ?) union select * from "users" where "id" = ?', $builder->toSql());
        $this->assertEquals([1, 2], $builder->getBindings());
    }

    public function testUnionAlls() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1);
        $builder->unionAll($this->getBuilder()->select('*')->from('users')->where('id', '=', 2));
        $this->assertSame('(select * from "users" where "id" = ?) union all select * from "users" where "id" = ?', $builder->toSql());
        $this->assertEquals([1, 2], $builder->getBindings());
    }

    public function testMultipleUnions() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1);
        $builder->union($this->getBuilder()->select('*')->from('users')->where('id', '=', 2));
        $builder->union($this->getBuilder()->select('*')->from('users')->where('id', '=', 3));
        $this->assertSame('(select * from "users" where "id" = ?) union select * from "users" where "id" = ? union select * from "users" where "id" = ?', $builder->toSql());
        $this->assertEquals([1, 2, 3], $builder->getBindings());
    }

    public function testUnionOrderBys() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1);
        $builder->union($this->getBuilder()->select('*')->from('users')->where('id', '=', 2));
        $builder->orderBy('id', 'desc');
        $this->assertSame('(select * from "users" where "id" = ?) union select * from "users" where "id" = ? order by "id" desc', $builder->toSql());
        $this->assertEquals([1, 2], $builder->getBindings());
    }

    public function testUnionLimitsAndOffsets() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users');
        $builder->union($this->getBuilder()->select('*')->from('dogs'));
        $builder->skip(5)->take(10);
        $this->assertSame('(select * from "users") union select * from "dogs" limit 10 offset 5', $builder->toSql());
    }

    public function testUnionWithJoin() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users');
        $builder->union($this->getBuilder()->select('*')->from('dogs')->join('breeds', function ($join) {
            $join->on('dogs.breed_id', '=', 'breeds.id')->where('breeds.is_native', '=', 1);
        }));
        $this->assertSame('(select * from "users") union select * from "dogs" inner join "breeds" on "dogs"."breed_id" = "breeds"."id" and "breeds"."is_native" = ?', $builder->toSql());
        $this->assertEquals([1], $builder->getBindings());
    }

    public function testMySqlUnionOrderBys() {
        $builder = $this->getMySqlBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1);
        $builder->union($this->getMySqlBuilder()->select('*')->from('users')->where('id', '=', 2));
        $builder->orderBy('id', 'desc');
        $this->assertSame('(select * from `users` where `id` = ?) union select * from `users` where `id` = ? order by `id` desc', $builder->toSql());
        $this->assertEquals([1, 2], $builder->getBindings());
    }

    public function testSqlServerUnions() {
        $builder = $this->getSqlServerBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1);
        $builder->union($this->getSqlServerBuilder()->select('*')->from('users')->where('id', '=', 2));
        $this->assertSame('select * from (select * from [users] where [id] = ?) as [temp_table] union select * from [users] where [id] = ?', $builder->toSql());
        $this->assertEquals([1, 2], $builder->getBindings());
    }

    public function testSqliteUnions() {
        $builder = $this->getSQLiteBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1);
        $builder->union($this->getSQLiteBuilder()->select('*')->from('users')->where('id', '=', 2));
        $this->assertSame('select * from (select * from "users" where "id" = ?) union select * from "users" where "id" = ?', $builder->toSql());
        $this->assertEquals([1, 2], $builder->getBindings());
    }

    // -----------------------------------------------------------------
    // Where clauses
    // -----------------------------------------------------------------

    public function testBasicWheres() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1);
        $this->assertSame('select * from "users" where "id" = ?', $builder->toSql());
        $this->assertEquals([1], $builder->getBindings());
    }

    public function testWhereWithTwoArgumentsAssumesEquals() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', 1);
        $this->assertSame('select * from "users" where "id" = ?', $builder->toSql());
        $this->assertEquals([1], $builder->getBindings());
    }

    public function testDateBasedWheresAcceptsTwoArguments() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereDate('created_at', 1);
        $this->assertSame('select * from "users" where date("created_at") = ?', $builder->toSql());
    }

    public function testWheresArePrefixedWithAnd() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1)->where('email', '=', 'foo');
        $this->assertSame('select * from "users" where "id" = ? and "email" = ?', $builder->toSql());
        $this->assertEquals([1, 'foo'], $builder->getBindings());
    }

    public function testOrWheres() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1)->orWhere('email', '=', 'foo');
        $this->assertSame('select * from "users" where "id" = ? or "email" = ?', $builder->toSql());
        $this->assertEquals([1, 'foo'], $builder->getBindings());
    }

    public function testArrayWhereShorthand() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where(['id' => 1, 'email' => 'foo']);
        $this->assertSame('select * from "users" where ("id" = ? and "email" = ?)', $builder->toSql());
        $this->assertEquals([1, 'foo'], $builder->getBindings());
    }

    public function testWhereBetweens() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereBetween('id', [1, 2]);
        $this->assertSame('select * from "users" where "id" between ? and ?', $builder->toSql());
        $this->assertEquals([1, 2], $builder->getBindings());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereNotBetween('id', [1, 2]);
        $this->assertSame('select * from "users" where "id" not between ? and ?', $builder->toSql());
        $this->assertEquals([1, 2], $builder->getBindings());
    }

    public function testWhereBetweenColumns() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereBetweenColumns('id', ['created_at', 'updated_at']);
        $this->assertSame('select * from "users" where "id" between "created_at" and "updated_at"', $builder->toSql());
        $this->assertEquals([], $builder->getBindings());
    }

    public function testOrWhereBetween() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1)->orWhereBetween('id', [3, 5]);
        $this->assertSame('select * from "users" where "id" = ? or "id" between ? and ?', $builder->toSql());
        $this->assertEquals([1, 3, 5], $builder->getBindings());
    }

    public function testBasicWhereIns() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereIn('id', [1, 2, 3]);
        $this->assertSame('select * from "users" where "id" in (?, ?, ?)', $builder->toSql());
        $this->assertEquals([1, 2, 3], $builder->getBindings());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1)->orWhereIn('id', [1, 2, 3]);
        $this->assertSame('select * from "users" where "id" = ? or "id" in (?, ?, ?)', $builder->toSql());
        $this->assertEquals([1, 1, 2, 3], $builder->getBindings());
    }

    public function testEmptyWhereIns() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereIn('id', []);
        $this->assertSame('select * from "users" where 0 = 1', $builder->toSql());
        $this->assertEquals([], $builder->getBindings());
    }

    public function testEmptyWhereNotIns() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereNotIn('id', []);
        $this->assertSame('select * from "users" where 1 = 1', $builder->toSql());
        $this->assertEquals([], $builder->getBindings());
    }

    public function testWhereIntegerInRaw() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereIntegerInRaw('id', ['1a', 2]);
        $this->assertSame('select * from "users" where "id" in (1, 2)', $builder->toSql());
    }

    public function testWhereIntegerNotInRaw() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereIntegerNotInRaw('id', ['1a', 2]);
        $this->assertSame('select * from "users" where "id" not in (1, 2)', $builder->toSql());
    }

    public function testBasicWhereColumn() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereColumn('first_name', 'last_name')->orWhereColumn('first_name', 'middle_name');
        $this->assertSame('select * from "users" where "first_name" = "last_name" or "first_name" = "middle_name"', $builder->toSql());
        $this->assertEquals([], $builder->getBindings());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereColumn('updated_at', '>', 'created_at');
        $this->assertSame('select * from "users" where "updated_at" > "created_at"', $builder->toSql());
    }

    public function testArrayWhereColumn() {
        $builder = $this->getBuilder();
        $conditions = [
            ['first_name', 'last_name'],
            ['updated_at', '>', 'created_at'],
        ];
        $builder->select('*')->from('users')->whereColumn($conditions);
        $this->assertSame('select * from "users" where ("first_name" = "last_name" and "updated_at" > "created_at")', $builder->toSql());
        $this->assertEquals([], $builder->getBindings());
    }

    public function testWhereRaw() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereRaw('id = ? or email = ?', [1, 'foo']);
        $this->assertSame('select * from "users" where id = ? or email = ?', $builder->toSql());
        $this->assertEquals([1, 'foo'], $builder->getBindings());
    }

    public function testWhereRawWithBadFieldName() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereRaw('sum(cash) > ?', [100]);
        $this->assertSame('select * from "users" where sum(cash) > ?', $builder->toSql());
        $this->assertEquals([100], $builder->getBindings());
    }

    public function testWhereRawOrWhereRaw() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1)->orWhereRaw('email = ?', ['foo']);
        $this->assertSame('select * from "users" where "id" = ? or email = ?', $builder->toSql());
        $this->assertEquals([1, 'foo'], $builder->getBindings());
    }

    public function testWhereNull() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereNull('id');
        $this->assertSame('select * from "users" where "id" is null', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1)->orWhereNull('id');
        $this->assertSame('select * from "users" where "id" = ? or "id" is null', $builder->toSql());
    }

    public function testWhereNotNull() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereNotNull('id');
        $this->assertSame('select * from "users" where "id" is not null', $builder->toSql());

        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '>', 1)->orWhereNotNull('id');
        $this->assertSame('select * from "users" where "id" > ? or "id" is not null', $builder->toSql());
    }

    public function testWhereShortcut() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', 1)->orWhere('name', 'foo');
        $this->assertSame('select * from "users" where "id" = ? or "name" = ?', $builder->toSql());
        $this->assertEquals([1, 'foo'], $builder->getBindings());
    }

    public function testNestedWheres() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('email', '=', 'foo')->orWhere(function ($q) {
            $q->where('name', '=', 'bar')->where('age', '=', 25);
        });
        $this->assertSame('select * from "users" where "email" = ? or ("name" = ? and "age" = ?)', $builder->toSql());
        $this->assertEquals(['foo', 'bar', 25], $builder->getBindings());
    }

    public function testFullSubSelects() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('email', '=', 'foo')->orWhere('id', '=', function ($q) {
            $q->select(new CDatabase_Query_Expression('max(id)'))->from('users')->where('email', '=', 'bar');
        });
        $this->assertSame('select * from "users" where "email" = ? or "id" = (select max(id) from "users" where "email" = ?)', $builder->toSql());
        $this->assertEquals(['foo', 'bar'], $builder->getBindings());
    }

    public function testWhereExists() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('orders')->whereExists(function ($q) {
            $q->select('*')->from('products')->where('products.id', '=', new CDatabase_Query_Expression("\"orders\".\"id\""));
        });
        $this->assertSame('select * from "orders" where exists (select * from "products" where "products"."id" = "orders"."id")', $builder->toSql());
    }

    public function testWhereNotExists() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('orders')->whereNotExists(function ($q) {
            $q->select('*')->from('products')->where('products.id', '=', new CDatabase_Query_Expression("\"orders\".\"id\""));
        });
        $this->assertSame('select * from "orders" where not exists (select * from "products" where "products"."id" = "orders"."id")', $builder->toSql());
    }

    public function testOrWhereExists() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('orders')->where('id', '=', 1)->orWhereExists(function ($q) {
            $q->select('*')->from('products')->where('products.id', '=', new CDatabase_Query_Expression("\"orders\".\"id\""));
        });
        $this->assertSame('select * from "orders" where "id" = ? or exists (select * from "products" where "products"."id" = "orders"."id")', $builder->toSql());
    }

    public function testWhereRowValues() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('orders')->whereRowValues(['last_update', 'order_number'], '<', [1, 2]);
        $this->assertSame('select * from "orders" where ("last_update", "order_number") < (?, ?)', $builder->toSql());
        $this->assertEquals([1, 2], $builder->getBindings());
    }

    public function testWhereDate() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereDate('created_at', '=', '2015-12-21');
        $this->assertSame('select * from "users" where date("created_at") = ?', $builder->toSql());
        $this->assertEquals(['2015-12-21'], $builder->getBindings());
    }

    public function testWhereDateSqlite() {
        $builder = $this->getSQLiteBuilder();
        $builder->select('*')->from('users')->whereDate('created_at', '=', '2015-12-21');
        $this->assertSame("select * from \"users\" where strftime('%Y-%m-%d', \"created_at\") = cast(? as text)", $builder->toSql());
        $this->assertEquals(['2015-12-21'], $builder->getBindings());
    }

    public function testWhereDay() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereDay('created_at', '=', 1);
        $this->assertSame('select * from "users" where day("created_at") = ?', $builder->toSql());
        $this->assertEquals([1], $builder->getBindings());
    }

    public function testWhereMonth() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereMonth('created_at', '=', 5);
        $this->assertSame('select * from "users" where month("created_at") = ?', $builder->toSql());
        $this->assertEquals([5], $builder->getBindings());
    }

    public function testWhereYear() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereYear('created_at', '=', 2014);
        $this->assertSame('select * from "users" where year("created_at") = ?', $builder->toSql());
        $this->assertEquals([2014], $builder->getBindings());
    }

    public function testWhereTime() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereTime('created_at', '=', '22:00');
        $this->assertSame('select * from "users" where time("created_at") = ?', $builder->toSql());
        $this->assertEquals(['22:00'], $builder->getBindings());
    }

    public function testOrWhereDate() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1)->orWhereDate('created_at', '=', '2015-12-21');
        $this->assertSame('select * from "users" where "id" = ? or date("created_at") = ?', $builder->toSql());
        $this->assertEquals([1, '2015-12-21'], $builder->getBindings());
    }

    public function testWhereJsonContainsMySql() {
        $builder = $this->getMySqlBuilder();
        $builder->select('*')->from('users')->whereJsonContains('options->languages', 'en');
        $this->assertSame('select * from `users` where json_contains(`options`, ?, \'$."languages"\')', $builder->toSql());
        $this->assertEquals(['"en"'], $builder->getBindings());
    }

    public function testWhereJsonContainsPostgres() {
        $builder = $this->getPostgresBuilder();
        $builder->select('*')->from('users')->whereJsonContains('options->languages', 'en');
        $this->assertSame('select * from "users" where ("options"->\'languages\')::jsonb @> ?', $builder->toSql());
        $this->assertEquals(['"en"'], $builder->getBindings());
    }

    public function testWhereJsonContainsSqliteNotSupported() {
        $builder = $this->getSQLiteBuilder();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This database engine does not support JSON contains operations.');
        $builder->select('*')->from('users')->whereJsonContains('options->languages', 'en')->toSql();
    }

    public function testWhereJsonLengthMySql() {
        $builder = $this->getMySqlBuilder();
        $builder->select('*')->from('users')->whereJsonLength('options->languages', 0);
        $this->assertSame('select * from `users` where json_length(`options`, \'$."languages"\') = ?', $builder->toSql());
        $this->assertEquals([0], $builder->getBindings());
    }

    public function testWhereJsonLengthSqlite() {
        $builder = $this->getSQLiteBuilder();
        $builder->select('*')->from('users')->whereJsonLength('options->languages', 0);
        $this->assertSame('select * from "users" where json_array_length("options", \'$."languages"\') = ?', $builder->toSql());
        $this->assertEquals([0], $builder->getBindings());
    }

    public function testWhereFullTextMySql() {
        $builder = $this->getMySqlBuilder();
        $builder->select('*')->from('users')->whereFullText('body', 'Hello World');
        $this->assertSame('select * from `users` where match (`body`) against (? in natural language mode)', $builder->toSql());
        $this->assertEquals(['Hello World'], $builder->getBindings());
    }

    public function testWhereFullTextPostgres() {
        $builder = $this->getPostgresBuilder();
        $builder->select('*')->from('users')->whereFullText('body', 'Hello World');
        $this->assertSame('select * from "users" where (to_tsvector(\'english\', "body")) @@ plainto_tsquery(\'english\', ?)', $builder->toSql());
        $this->assertEquals(['Hello World'], $builder->getBindings());
    }

    public function testWhereFullTextSqliteNotSupported() {
        $builder = $this->getSQLiteBuilder();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This database engine does not support fulltext search operations.');
        $builder->select('*')->from('users')->whereFullText('body', 'Hello World')->toSql();
    }

    // -----------------------------------------------------------------
    // Raw expressions
    // -----------------------------------------------------------------

    public function testRawExpressionsInSelect() {
        $builder = $this->getBuilder();
        $builder->select(new CDatabase_Query_Expression('substr(foo, 1)'))->from('users');
        $this->assertSame('select substr(foo, 1) from "users"', $builder->toSql());
    }

    public function testTapCallbackAllowsCustomization() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->tap(function ($query) {
            $query->where('id', '=', 1);
        });
        $this->assertSame('select * from "users" where "id" = ?', $builder->toSql());
    }

    // -----------------------------------------------------------------
    // Aggregates (toSql() of the underlying select, per the manual
    // ->aggregate assignment - matches how CDatabase_Query_Grammar's
    // compileAggregate()/compileSelect() dispatch on $query->aggregate)
    // -----------------------------------------------------------------

    public function testAggregateCountSql() {
        $builder = $this->getBuilder();
        $builder->from('users');
        $builder->aggregate = ['function' => 'count', 'columns' => ['*']];
        $this->assertSame('select count(*) as aggregate from "users"', $builder->toSql());
    }

    public function testAggregateMaxSql() {
        $builder = $this->getBuilder();
        $builder->from('users');
        $builder->aggregate = ['function' => 'max', 'columns' => ['id']];
        $this->assertSame('select max("id") as aggregate from "users"', $builder->toSql());
    }

    public function testAggregateMinSql() {
        $builder = $this->getBuilder();
        $builder->from('users');
        $builder->aggregate = ['function' => 'min', 'columns' => ['id']];
        $this->assertSame('select min("id") as aggregate from "users"', $builder->toSql());
    }

    public function testAggregateSumSql() {
        $builder = $this->getBuilder();
        $builder->from('users');
        $builder->aggregate = ['function' => 'sum', 'columns' => ['payment']];
        $this->assertSame('select sum("payment") as aggregate from "users"', $builder->toSql());
    }

    public function testAggregateAvgSql() {
        $builder = $this->getBuilder();
        $builder->from('users');
        $builder->aggregate = ['function' => 'avg', 'columns' => ['payment']];
        $this->assertSame('select avg("payment") as aggregate from "users"', $builder->toSql());
    }

    public function testAggregateWithDistinctSql() {
        $builder = $this->getBuilder();
        $builder->from('users')->distinct();
        $builder->aggregate = ['function' => 'count', 'columns' => ['id']];
        $this->assertSame('select count(distinct "id") as aggregate from "users"', $builder->toSql());
    }

    public function testAggregateWithWhereSql() {
        $builder = $this->getBuilder();
        $builder->from('users')->where('votes', '>', 100);
        $builder->aggregate = ['function' => 'count', 'columns' => ['*']];
        $this->assertSame('select count(*) as aggregate from "users" where "votes" > ?', $builder->toSql());
        $this->assertEquals([100], $builder->getBindings());
    }

    // -----------------------------------------------------------------
    // Insert / update / delete
    // -----------------------------------------------------------------

    public function testInsertMethod() {
        $builder = $this->getBuilder();
        $sql = $builder->grammar->compileInsert($builder->from('users'), ['email' => 'foo']);
        $this->assertSame('insert into "users" ("email") values (?)', $sql);
    }

    public function testInsertMethodMultipleRecords() {
        $builder = $this->getBuilder();
        $sql = $builder->grammar->compileInsert($builder->from('users'), [
            ['email' => 'foo', 'name' => 'bar'],
            ['email' => 'baz', 'name' => 'boom'],
        ]);
        $this->assertSame('insert into "users" ("email", "name") values (?, ?), (?, ?)', $sql);
    }

    public function testInsertMethodEmptyValues() {
        $builder = $this->getBuilder();
        $sql = $builder->grammar->compileInsert($builder->from('users'), []);
        $this->assertSame('insert into "users" default values', $sql);
    }

    public function testInsertMethodMySqlEmptyValues() {
        $builder = $this->getMySqlBuilder();
        $sql = $builder->grammar->compileInsert($builder->from('users'), []);
        $this->assertSame('insert into `users` () values ()', $sql);
    }

    public function testInsertOrIgnoreMethodMySql() {
        $builder = $this->getMySqlBuilder();
        $sql = $builder->grammar->compileInsertOrIgnore($builder->from('users'), ['email' => 'foo']);
        $this->assertSame('insert ignore into `users` (`email`) values (?)', $sql);
    }

    public function testInsertOrIgnoreMethodBaseGrammarNotSupported() {
        $builder = $this->getBuilder();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This database engine does not support inserting while ignoring errors.');
        $builder->grammar->compileInsertOrIgnore($builder->from('users'), ['email' => 'foo']);
    }

    public function testInsertUsingMethod() {
        $builder = $this->getBuilder();
        $sql = $builder->grammar->compileInsertUsing($builder->from('table1'), ['foo'], 'select "bar" from "table2" where "foreign_id" = ?');
        $this->assertSame('insert into "table1" ("foo") select "bar" from "table2" where "foreign_id" = ?', $sql);
    }

    public function testUpdateMethod() {
        $builder = $this->getBuilder();
        $builder->from('users')->where('id', '=', 1);
        $sql = $builder->grammar->compileUpdate($builder, ['email' => 'foo', 'name' => 'bar']);
        $this->assertSame('update "users" set "email" = ?, "name" = ? where "id" = ?', $sql);
    }

    public function testUpdateMethodWithJoins() {
        $builder = $this->getBuilder();
        $builder->from('users')->join('orders', 'users.id', '=', 'orders.user_id')->where('users.id', '=', 1);
        $sql = $builder->grammar->compileUpdate($builder, ['email' => 'foo', 'name' => 'bar']);
        $this->assertSame('update "users" inner join "orders" on "users"."id" = "orders"."user_id" set "email" = ?, "name" = ? where "users"."id" = ?', $sql);
    }

    public function testUpdateMethodWithoutJoinsOnMySqlWithOrderAndLimit() {
        $builder = $this->getMySqlBuilder();
        $builder->from('users')->where('id', '=', 1)->orderBy('foo', 'desc')->limit(5);
        $sql = $builder->grammar->compileUpdate($builder, ['email' => 'foo', 'name' => 'bar']);
        $this->assertSame('update `users` set `email` = ?, `name` = ? where `id` = ? order by `foo` desc limit 5', $sql);
    }

    public function testUpsertMethodMySql() {
        $builder = $this->getMySqlBuilder();
        $sql = $builder->grammar->compileUpsert(
            $builder->from('users'),
            [['email' => 'foo', 'name' => 'bar']],
            ['email'],
            ['name']
        );
        // A plain list of column names (numeric keys) uses MySQL's values()
        // function rather than a bound parameter - see
        // MySqlGrammar::compileUpsert().
        $this->assertSame('insert into `users` (`email`, `name`) values (?, ?) on duplicate key update `name` = values(`name`)', $sql);
    }

    public function testUpsertMethodBaseGrammarNotSupported() {
        $builder = $this->getBuilder();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This database engine does not support upserts.');
        $builder->grammar->compileUpsert($builder->from('users'), [['email' => 'foo']], ['email'], ['email']);
    }

    public function testDeleteMethod() {
        $builder = $this->getBuilder();
        $builder->from('users')->where('email', '=', 'foo');
        $sql = $builder->grammar->compileDelete($builder);
        $this->assertSame('delete from "users" where "email" = ?', $sql);
    }

    public function testDeleteMethodWithJoins() {
        $builder = $this->getBuilder();
        $builder->from('users')->join('contacts', 'users.id', '=', 'contacts.id')->where('users.email', '=', 'foo');
        $sql = $builder->grammar->compileDelete($builder);
        $this->assertSame('delete "users" from "users" inner join "contacts" on "users"."id" = "contacts"."id" where "users"."email" = ?', $sql);
    }

    public function testDeleteMethodWithoutJoinsOnMySqlWithOrderAndLimit() {
        $builder = $this->getMySqlBuilder();
        $builder->from('users')->where('email', '=', 'foo')->orderBy('id')->limit(1);
        $sql = $builder->grammar->compileDelete($builder);
        $this->assertSame('delete from `users` where `email` = ? order by `id` asc limit 1', $sql);
    }

    public function testTruncateMethod() {
        $builder = $this->getBuilder();
        $builder->from('users');
        $sql = $builder->grammar->compileTruncate($builder);
        $this->assertEquals(['truncate "users"' => []], $sql);
    }

    // -----------------------------------------------------------------
    // Full round-trip insert/update/delete/upsert through the Builder
    // itself (not just the Grammar compile* methods), asserting the exact
    // SQL/bindings the mocked CDatabase_Connection receives - this exercises
    // Builder::insert()/update()/delete()/upsert()/updateOrInsert()'s own
    // binding-flattening/cleaning logic in addition to the Grammar.
    // -----------------------------------------------------------------

    public function testBuilderInsert() {
        $builder = $this->getBuilder();
        $builder->getConnection()->shouldReceive('insertWithQuery')
            ->once()
            ->with('insert into "users" ("email") values (?)', ['foo'])
            ->andReturn(true);
        $result = $builder->from('users')->insert(['email' => 'foo']);
        $this->assertTrue($result);
    }

    public function testBuilderInsertGetId() {
        $builder = $this->getBuilder();
        $builder->getConnection()->shouldReceive('insertWithQuery')->once()->with('insert into "users" ("email") values (?)', ['foo']);
        $builder->getConnection()->shouldReceive('getPdo')->once()->andReturn(m::mock(PDO::class, function ($mock) {
            $mock->shouldReceive('lastInsertId')->once()->with(null)->andReturn('1');
        }));
        $result = $builder->from('users')->insertGetId(['email' => 'foo']);
        $this->assertSame(1, $result);
    }

    public function testBuilderInsertOrIgnore() {
        $builder = $this->getMySqlBuilder();
        $builder->getConnection()->shouldReceive('affectingStatement')
            ->once()
            ->with('insert ignore into `users` (`email`) values (?)', ['foo'])
            ->andReturn(1);
        $result = $builder->from('users')->insertOrIgnore(['email' => 'foo']);
        $this->assertSame(1, $result);
    }

    public function testBuilderUpdate() {
        $builder = $this->getBuilder();
        $builder->getConnection()->shouldReceive('updateWithQuery')
            ->once()
            ->with('update "users" set "email" = ?, "name" = ? where "id" = ?', ['foo', 'bar', 1])
            ->andReturn(1);
        $result = $builder->from('users')->where('id', '=', 1)->update(['email' => 'foo', 'name' => 'bar']);
        $this->assertSame(1, $result);
    }

    public function testBuilderDelete() {
        $builder = $this->getBuilder();
        $builder->getConnection()->shouldReceive('deleteWithQuery')
            ->once()
            ->with('delete from "users" where "email" = ?', ['foo'])
            ->andReturn(1);
        $result = $builder->from('users')->where('email', '=', 'foo')->delete();
        $this->assertSame(1, $result);
    }

    public function testBuilderDeleteWithId() {
        $builder = $this->getBuilder();
        $builder->getConnection()->shouldReceive('deleteWithQuery')
            ->once()
            ->with('delete from "users" where "users"."id" = ?', [1])
            ->andReturn(1);
        $result = $builder->from('users')->delete(1);
        $this->assertSame(1, $result);
    }

    public function testBuilderUpsert() {
        $builder = $this->getMySqlBuilder();
        $builder->getConnection()->shouldReceive('affectingStatement')
            ->once()
            ->with('insert into `users` (`email`, `name`) values (?, ?) on duplicate key update `name` = values(`name`)', ['foo', 'bar'])
            ->andReturn(2);
        $result = $builder->from('users')->upsert(['email' => 'foo', 'name' => 'bar'], ['email'], ['name']);
        $this->assertSame(2, $result);
    }

    public function testBuilderUpdateOrInsertInsertsWhenRowDoesNotExist() {
        $builder = $this->getBuilder();
        $builder->getConnection()->shouldReceive('select')
            ->once()
            ->with('select exists(select * from "users" where ("email" = ?)) as "exists"', ['foo'], true)
            ->andReturn([(object) ['exists' => 0]]);
        $builder->getConnection()->shouldReceive('insertWithQuery')
            ->once()
            ->with('insert into "users" ("email", "name") values (?, ?)', ['foo', 'bar'])
            ->andReturn(true);
        $result = $builder->from('users')->updateOrInsert(['email' => 'foo'], ['name' => 'bar']);
        $this->assertTrue($result);
    }

    public function testBuilderUpdateOrInsertUpdatesWhenRowExists() {
        $builder = $this->getBuilder();
        $builder->getConnection()->shouldReceive('select')
            ->once()
            ->with('select exists(select * from "users" where ("email" = ?)) as "exists"', ['foo'], true)
            ->andReturn([(object) ['exists' => 1]]);
        // NOTE: unlike Laravel's MySQL/base grammar, the base
        // CDatabase_Query_Grammar::compileUpdate() does not append a LIMIT
        // clause at all (only MySqlGrammar::compileUpdateWithoutJoins()
        // does), so the ->limit(1) that updateOrInsert() applies internally
        // has no visible effect on this dialect's compiled SQL.
        $builder->getConnection()->shouldReceive('updateWithQuery')
            ->once()
            ->with('update "users" set "name" = ? where ("email" = ?)', ['bar', 'foo'])
            ->andReturn(1);
        $result = $builder->from('users')->updateOrInsert(['email' => 'foo'], ['name' => 'bar']);
        $this->assertTrue($result);
    }

    // -----------------------------------------------------------------
    // Dynamic where (magic __call -> dynamicWhere / __call in Builder)
    // -----------------------------------------------------------------

    public function testDynamicWhere() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereIdAndEmail(1, 'foo');
        $this->assertSame('select * from "users" where "id" = ? and "email" = ?', $builder->toSql());
        $this->assertEquals([1, 'foo'], $builder->getBindings());
    }

    public function testDynamicWhereOr() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->whereIdOrEmail(1, 'foo');
        $this->assertSame('select * from "users" where "id" = ? or "email" = ?', $builder->toSql());
        $this->assertEquals([1, 'foo'], $builder->getBindings());
    }

    // -----------------------------------------------------------------
    // Bitwise where operators
    //
    // NOTE: whereBitwise() is only implemented on
    // CDatabase_Query_Grammar_PostgresGrammar and
    // CDatabase_Query_Grammar_SqlServerGrammar. The base
    // CDatabase_Query_Grammar (and therefore MySqlGrammar/SqliteGrammar,
    // which don't override it) has NO whereBitwise() method at all, even
    // though CDatabase_Query_Builder's own default $bitwiseOperators list
    // (Builder.php) includes '&', '|', '^', '<<', '>>' regardless of dialect.
    // So `where('flags', '&', 1)` on a MySQL/SQLite builder compiles a where
    // clause of type "Bitwise" that the grammar cannot dispatch, and toSql()
    // fatals with "Call to undefined method ...::whereBitwise()". This is a
    // real gap confirmed below rather than blindly assumed; only the two
    // dialects that actually implement it are exercised for the happy path.
    // -----------------------------------------------------------------

    public function testWhereBitwisePostgres() {
        $builder = $this->getPostgresBuilder();
        $builder->select('*')->from('users')->where('access', '&', 1);
        $this->assertSame('select * from "users" where ("access" & ?)::bool', $builder->toSql());
        $this->assertEquals([1], $builder->getBindings());
    }

    public function testWhereBitwiseSqlServer() {
        $builder = $this->getSqlServerBuilder();
        $builder->select('*')->from('users')->where('access', '&', 1);
        $this->assertSame('select * from [users] where ([access] & ?) != 0', $builder->toSql());
        $this->assertEquals([1], $builder->getBindings());
    }

    public function testWhereBitwiseIsNotImplementedOnMySqlGrammar() {
        $builder = $this->getMySqlBuilder();
        $builder->select('*')->from('users')->where('access', '&', 1);
        $this->expectException(Error::class);
        $builder->toSql();
    }

    public function testWhereBitwiseIsNotImplementedOnSqliteGrammar() {
        $builder = $this->getSQLiteBuilder();
        $builder->select('*')->from('users')->where('access', '&', 1);
        $this->expectException(Error::class);
        $builder->toSql();
    }

    // -----------------------------------------------------------------
    // JSON contains-key / doesnt-contain
    //
    // NOTE: whereJsonContainsKey()/orWhereJsonContainsKey() are NOT
    // implemented on CDatabase_Query_Builder (grep confirms no such method
    // exists in Builder.php or BuilderWhereTrait.php), even though the
    // grammar side has working compileJsonContainsKey() implementations for
    // MySQL/Postgres and a whereJsonContainsKey() dispatch method on the base
    // Grammar. Calling ->whereJsonContainsKey(...) falls through to Builder's
    // __call() -> dynamicWhere('whereJsonContainsKey', ...), which (since
    // there's no "And"/"Or" in the method name) treats the whole suffix as a
    // literal snake_cased column name and silently builds a nonsensical
    // `where "json_contains_key" = ?` clause instead of throwing. This is
    // dead grammar-side code with no Builder entry point - skipped rather
    // than tested as if it worked.
    // -----------------------------------------------------------------

    public function testWhereJsonDoesntContainMySql() {
        $builder = $this->getMySqlBuilder();
        $builder->select('*')->from('users')->whereJsonDoesntContain('options->languages', 'en');
        $this->assertSame('select * from `users` where not json_contains(`options`, ?, \'$."languages"\')', $builder->toSql());
        $this->assertEquals(['"en"'], $builder->getBindings());
    }

    // -----------------------------------------------------------------
    // Locking
    // -----------------------------------------------------------------

    public function testLockForUpdateMySql() {
        $builder = $this->getMySqlBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1)->lockForUpdate();
        $this->assertSame('select * from `users` where `id` = ? for update', $builder->toSql());
    }

    public function testSharedLockMySql() {
        $builder = $this->getMySqlBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1)->sharedLock();
        $this->assertSame('select * from `users` where `id` = ? lock in share mode', $builder->toSql());
    }

    public function testLockForUpdatePostgres() {
        $builder = $this->getPostgresBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1)->lockForUpdate();
        $this->assertSame('select * from "users" where "id" = ? for update', $builder->toSql());
    }

    public function testSharedLockPostgres() {
        $builder = $this->getPostgresBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1)->sharedLock();
        $this->assertSame('select * from "users" where "id" = ? for share', $builder->toSql());
    }

    public function testLockForUpdateSqlServer() {
        $builder = $this->getSqlServerBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1)->lockForUpdate();
        $this->assertSame('select * from [users] with(rowlock,updlock,holdlock) where [id] = ?', $builder->toSql());
    }

    public function testLockForUpdateBaseGrammarIsNoop() {
        // The base/SQLite grammar's compileLock() only honors an explicit
        // *string* lock clause; a bool true/false (as produced by
        // lockForUpdate()/sharedLock()) compiles to an empty string.
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1)->lockForUpdate();
        $this->assertSame('select * from "users" where "id" = ?', $builder->toSql());
    }

    public function testLockWithCustomStringClause() {
        $builder = $this->getBuilder();
        $builder->select('*')->from('users')->where('id', '=', 1)->lock('lock in share mode');
        $this->assertSame('select * from "users" where "id" = ? lock in share mode', $builder->toSql());
    }

    // -----------------------------------------------------------------
    // Group limit ("N per group") - a CF-specific addition on top of the
    // Laravel-derived query builder.
    // -----------------------------------------------------------------

    public function testGroupLimitOnPostgres() {
        $builder = $this->getPostgresBuilder();
        $builder->select('*')->from('users')->groupLimit(2, 'user_id');
        $sql = $builder->toSql();
        $this->assertStringContainsString('row_number() over (partition by "user_id")', $sql);
        $this->assertStringContainsString('"cf_row" <= 2', $sql);
    }

    // -----------------------------------------------------------------
    // Same builder chain compiled across all 4 dialects to make the
    // quoting/limit-offset differences explicit side by side.
    // -----------------------------------------------------------------

    public function testSameQueryAcrossAllFourDialects() {
        $chain = function ($builder) {
            return $builder->select('id', 'name')->from('users')->where('active', '=', 1)->orderBy('name')->limit(5)->offset(10);
        };

        $this->assertSame(
            'select "id", "name" from "users" where "active" = ? order by "name" asc limit 5 offset 10',
            $chain($this->getBuilder())->toSql()
        );
        $this->assertSame(
            'select `id`, `name` from `users` where `active` = ? order by `name` asc limit 5 offset 10',
            $chain($this->getMySqlBuilder())->toSql()
        );
        $this->assertSame(
            'select "id", "name" from "users" where "active" = ? order by "name" asc limit 5 offset 10',
            $chain($this->getPostgresBuilder())->toSql()
        );
        $this->assertSame(
            'select "id", "name" from "users" where "active" = ? order by "name" asc limit 5 offset 10',
            $chain($this->getSQLiteBuilder())->toSql()
        );
        $this->assertSame(
            'select [id], [name] from [users] where [active] = ? order by [name] asc offset 10 rows fetch next 5 rows only',
            $chain($this->getSqlServerBuilder())->toSql()
        );
    }

    public function testIdentifierQuotingDiffersPerDialectForReservedLikeNames() {
        $this->assertSame('"order"', $this->getBuilder()->grammar->wrap('order'));
        $this->assertSame('`order`', $this->getMySqlBuilder()->grammar->wrap('order'));
        $this->assertSame('"order"', $this->getPostgresBuilder()->grammar->wrap('order'));
        $this->assertSame('"order"', $this->getSQLiteBuilder()->grammar->wrap('order'));
        $this->assertSame('[order]', $this->getSqlServerBuilder()->grammar->wrap('order'));
    }
}
