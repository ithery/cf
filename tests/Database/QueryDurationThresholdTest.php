<?php

use CarbonV3\CarbonInterval;

use PHPUnit\Framework\TestCase;

class QueryDurationThresholdTest extends TestCase {
    public function testItCanHandleReachingADurationThresholdInTheDb() {
        $connection = new CDatabase_Connection(new PDO('sqlite::memory:'));
        $connection->setEventDispatcher(new CEvent_Dispatcher());
        $called = 0;
        $connection->whenQueryingForLongerThan(CarbonInterval::milliseconds(1.1), function () use (&$called) {
            $called++;
        });

        $connection->logQuery('xxxx', [], 1.0);
        $connection->logQuery('xxxx', [], 0.1);
        $this->assertSame(0, $called);

        $connection->logQuery('xxxx', [], 0.1);
        $this->assertSame(1, $called);
    }

    public function testItIsOnlyCalledOnce() {
        $connection = new CDatabase_Connection(new PDO('sqlite::memory:'));
        $connection->setEventDispatcher(new CEvent_Dispatcher());
        $called = 0;
        $connection->whenQueryingForLongerThan(CarbonInterval::milliseconds(1), function () use (&$called) {
            $called++;
        });

        $connection->logQuery('xxxx', [], 1);
        $connection->logQuery('xxxx', [], 1);
        $connection->logQuery('xxxx', [], 1);

        $this->assertSame(1, $called);
    }

    public function testItIsOnlyCalledOnceWhenGivenDateTime() {
        $connection = new CDatabase_Connection(new PDO('sqlite::memory:'));
        $connection->setEventDispatcher(new CEvent_Dispatcher());
        $called = 0;
        $connection->whenQueryingForLongerThan(c::now()->addMilliseconds(1), function () use (&$called) {
            $called++;
        });

        $connection->logQuery('xxxx', [], 1);
        $connection->logQuery('xxxx', [], 1);
        $connection->logQuery('xxxx', [], 1);

        $this->assertSame(1, $called);
    }

    public function testItCanSpecifyMultipleHandlersWithTheSameIntervals() {
        $connection = new CDatabase_Connection(new PDO('sqlite::memory:'));
        $connection->setEventDispatcher(new CEvent_Dispatcher());
        $called = [];
        $connection->whenQueryingForLongerThan(CarbonInterval::milliseconds(1), function () use (&$called) {
            $called['a'] = true;
        });
        $connection->whenQueryingForLongerThan(CarbonInterval::milliseconds(1), function () use (&$called) {
            $called['b'] = true;
        });

        $connection->logQuery('xxxx', [], 1);
        $connection->logQuery('xxxx', [], 1);

        $this->assertSame([
            'a' => true,
            'b' => true,
        ], $called);
    }

    public function testItCanSpecifyMultipleHandlersWithDifferentIntervals() {
        $connection = new CDatabase_Connection(new PDO('sqlite::memory:'));
        $connection->setEventDispatcher(new CEvent_Dispatcher());
        $called = [];
        $connection->whenQueryingForLongerThan(CarbonInterval::milliseconds(1), function () use (&$called) {
            $called['a'] = true;
        });
        $connection->whenQueryingForLongerThan(CarbonInterval::milliseconds(2), function () use (&$called) {
            $called['b'] = true;
        });

        $connection->logQuery('xxxx', [], 1);
        $connection->logQuery('xxxx', [], 1);
        $this->assertSame([
            'a' => true,
        ], $called);

        $connection->logQuery('xxxx', [], 1);
        $this->assertSame([
            'a' => true,
            'b' => true,
        ], $called);
    }

    public function testItHasAccessToConnectionInHandler() {
        $connection = new CDatabase_Connection(new PDO('sqlite::memory:'), '', '', ['name' => 'expected-name']);
        $connection->setEventDispatcher(new CEvent_Dispatcher());
        $name = null;
        $connection->whenQueryingForLongerThan(CarbonInterval::milliseconds(1), function ($connection) use (&$name) {
            $name = $connection->getName();
        });

        $connection->logQuery('xxxx', [], 1);
        $connection->logQuery('xxxx', [], 1);

        $this->assertSame('expected-name', $name);
    }

    public function testItHasSpecifyThresholdWithFloat() {
        $connection = new CDatabase_Connection(new PDO('sqlite::memory:'));
        $connection->setEventDispatcher(new CEvent_Dispatcher());
        $called = false;
        $connection->whenQueryingForLongerThan(1.1, function () use (&$called) {
            $called = true;
        });

        $connection->logQuery('xxxx', [], 1.1);
        $this->assertFalse($called);

        $connection->logQuery('xxxx', [], 0.1);
        $this->assertTrue($called);
    }

    public function testItHasSpecifyThresholdWithInt() {
        $connection = new CDatabase_Connection(new PDO('sqlite::memory:'));
        $connection->setEventDispatcher(new CEvent_Dispatcher());
        $called = false;
        $connection->whenQueryingForLongerThan(2, function () use (&$called) {
            $called = true;
        });

        $connection->logQuery('xxxx', [], 1.1);
        $this->assertFalse($called);

        $connection->logQuery('xxxx', [], 1.0);
        $this->assertTrue($called);
    }

    public function testItCanResetTotalQueryDuration() {
        $connection = new CDatabase_Connection(new PDO('sqlite::memory:'));
        $connection->setEventDispatcher(new CEvent_Dispatcher());

        $connection->logQuery('xxxx', [], 1.1);
        $this->assertSame(1.1, $connection->totalQueryDuration());
        $connection->logQuery('xxxx', [], 1.1);
        $this->assertSame(2.2, $connection->totalQueryDuration());

        $connection->resetTotalQueryDuration();
        $this->assertSame(0.0, $connection->totalQueryDuration());
    }

    public function testItCanRestoreAlreadyRunHandlers() {
        $connection = new CDatabase_Connection(new PDO('sqlite::memory:'));
        $connection->setEventDispatcher(new CEvent_Dispatcher());
        $called = 0;
        $connection->whenQueryingForLongerThan(CarbonInterval::milliseconds(1), function () use (&$called) {
            $called++;
        });

        $connection->logQuery('xxxx', [], 1);
        $connection->logQuery('xxxx', [], 1);
        $connection->logQuery('xxxx', [], 1);
        $this->assertSame(1, $called);

        $connection->allowQueryDurationHandlersToRunAgain();
        $connection->logQuery('xxxx', [], 1);
        $connection->logQuery('xxxx', [], 1);
        $connection->logQuery('xxxx', [], 1);
        $this->assertSame(2, $called);

        $connection->allowQueryDurationHandlersToRunAgain();
        $connection->logQuery('xxxx', [], 1);
        $connection->logQuery('xxxx', [], 1);
        $connection->logQuery('xxxx', [], 1);
        $this->assertSame(3, $called);
    }

    public function testItCanAccessAllQueriesWhenQueryLoggingIsActive() {
        $connection = new CDatabase_Connection(new PDO('sqlite::memory:'));
        $connection->setEventDispatcher(new CEvent_Dispatcher());
        $connection->enableQueryLog();
        $queries = [];
        $connection->whenQueryingForLongerThan(CarbonInterval::milliseconds(2), function ($connection, $event) use (&$queries) {
            $queries = carr::pluck($connection->getQueryLog(), 'query');
            $queries[] = $event->sql;
        });

        $connection->logQuery('foo', [], 1);
        $connection->logQuery('bar', [], 1);
        $connection->logQuery('baz', [], 1);

        $this->assertSame([
            'foo',
            'bar',
            'baz',
        ], $queries);
    }
}
