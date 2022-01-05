<?php
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ScheduledEventTest extends TestCase {
    /**
     * The default configuration timezone.
     *
     * @var string
     */
    protected $defaultTimezone;

    protected function setUp() {
        $this->defaultTimezone = date_default_timezone_get();
        date_default_timezone_set('UTC');
    }

    protected function tearDown() {
        date_default_timezone_set($this->defaultTimezone);
        CCarbon::setTestNow(null);
        m::close();
    }

    public function testBasicCronCompilation() {
        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo');
        $this->assertSame('* * * * *', $event->getExpression());
        $this->assertTrue($event->isDue());
        $this->assertTrue($event->skip(function () {
            return true;
        })->isDue());
        $this->assertFalse($event->skip(function () {
            return true;
        })->filtersPass());

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo');
        $this->assertSame('* * * * *', $event->getExpression());
        $this->assertFalse($event->environments('local')->isDue());

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo');
        $this->assertSame('* * * * *', $event->getExpression());
        $this->assertFalse($event->when(function () {
            return false;
        })->filtersPass());

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo');
        $this->assertSame('* * * * *', $event->getExpression());
        $this->assertFalse($event->when(false)->filtersPass());

        // chained rules should be commutative
        $eventA = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo');
        $eventB = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo');
        $this->assertEquals(
            $eventA->daily()->hourly()->getExpression(),
            $eventB->hourly()->daily()->getExpression()
        );

        $eventA = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo');
        $eventB = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo');
        $this->assertEquals(
            $eventA->weekdays()->hourly()->getExpression(),
            $eventB->hourly()->weekdays()->getExpression()
        );
    }

    public function testEventIsDueCheck() {
        CCarbon::setTestNow(CCarbon::create(2015, 1, 1, 0, 0, 0));

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo');
        $this->assertSame('* * * * 4', $event->thursdays()->getExpression());
        $this->assertTrue($event->isDue());

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo');
        $this->assertSame('0 19 * * 3', $event->wednesdays()->at('19:00')->timezone('EST')->getExpression());
        $this->assertTrue($event->isDue());
    }

    public function testTimeBetweenChecks() {
        CCarbon::setTestNow(CCarbon::now()->startOfDay()->addHours(9));

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo', 'UTC');
        $this->assertTrue($event->between('8:00', '10:00')->filtersPass());

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo', 'UTC');
        $this->assertTrue($event->between('9:00', '9:00')->filtersPass());

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo', 'UTC');
        $this->assertTrue($event->between('23:00', '10:00')->filtersPass());

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo', 'UTC');
        $this->assertTrue($event->between('8:00', '6:00')->filtersPass());

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo', 'UTC');
        $this->assertFalse($event->between('10:00', '11:00')->filtersPass());

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo', 'UTC');
        $this->assertFalse($event->between('10:00', '8:00')->filtersPass());
    }

    public function testTimeUnlessBetweenChecks() {
        CCarbon::setTestNow(CCarbon::now()->startOfDay()->addHours(9));

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo', 'UTC');
        $this->assertFalse($event->unlessBetween('8:00', '10:00')->filtersPass());

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo', 'UTC');
        $this->assertFalse($event->unlessBetween('9:00', '9:00')->filtersPass());

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo', 'UTC');
        $this->assertFalse($event->unlessBetween('23:00', '10:00')->filtersPass());

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo', 'UTC');
        $this->assertFalse($event->unlessBetween('8:00', '6:00')->filtersPass());

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo', 'UTC');
        $this->assertTrue($event->unlessBetween('10:00', '11:00')->filtersPass());

        $event = new CCron_Event(m::mock(CCron_Contract_EventMutexInterface::class), 'php foo', 'UTC');
        $this->assertTrue($event->unlessBetween('10:00', '8:00')->filtersPass());
    }
}
