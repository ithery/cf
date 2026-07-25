<?php
use PHPUnit\Framework\TestCase;

class PeriodTest extends TestCase {
    public function testPeriodCreate() {
        $dateStart = CCarbon::now();
        $dateEnd = CCarbon::now()->addDays(1);
        $period = CPeriod::create($dateStart, $dateEnd);
        $this->assertNotNull($period);
        $this->assertInstanceOf(CPeriod::class, $period);
    }

    public function testCreateThrowsWhenStartAfterEnd() {
        $this->expectException(CPeriod_Exception_InvalidPeriodException::class);

        CPeriod::create(CCarbon::parse('2024-01-10'), CCarbon::parse('2024-01-01'));
    }

    public function testGetStartDateAndGetEndDate() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));

        $this->assertSame('2024-01-01 00:00:00', $period->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertSame('2024-01-10 00:00:00', $period->getEndDate()->format('Y-m-d H:i:s'));
    }

    public function testToArray() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));

        $array = $period->toArray();

        $this->assertCount(2, $array);
        $this->assertSame('2024-01-01', $array[0]->format('Y-m-d'));
        $this->assertSame('2024-01-10', $array[1]->format('Y-m-d'));
    }

    public function testIncludedStartAndIncludedEnd() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));

        $this->assertSame('2024-01-01 00:00:00', $period->includedStart()->format('Y-m-d H:i:s'));
        $this->assertSame('2024-01-10 00:00:00', $period->includedEnd()->format('Y-m-d H:i:s'));
    }

    public function testBoundariesDefaultToIncludeBoth() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));

        $this->assertTrue($period->isStartIncluded());
        $this->assertTrue($period->isEndIncluded());
        $this->assertFalse((bool) $period->isStartExcluded());
        $this->assertFalse((bool) $period->isEndExcluded());
    }

    public function testLength() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));

        $this->assertSame(10, $period->length());
    }

    public function testDurationReturnsDurationInstance() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));

        $this->assertInstanceOf(CPeriod_Duration::class, $period->duration());
    }

    public function testPrecisionDefaultsToDay() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));

        $this->assertTrue($period->precision()->equals(CPeriod_Precision::DAY()));
    }

    public function testBoundariesReturnsBoundariesInstance() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));

        $this->assertInstanceOf(CPeriod_Boundaries::class, $period->boundaries());
    }

    public function testIterationYieldsEveryDay() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));

        $this->assertSame(10, iterator_count($period));

        $days = [];
        foreach ($period as $day) {
            $days[] = $day->format('Y-m-d');
        }
        $this->assertSame('2024-01-01', $days[0]);
        $this->assertSame('2024-01-10', $days[9]);
        $this->assertCount(10, $days);
    }

    public function testStartsBeforeAndStartsAfter() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));

        $this->assertTrue($period->startsBefore(CCarbon::parse('2024-01-05')));
        $this->assertFalse($period->startsBefore(CCarbon::parse('2023-12-01')));
        $this->assertTrue($period->startsAfter(CCarbon::parse('2023-12-01')));
        $this->assertFalse($period->startsAfter(CCarbon::parse('2024-01-05')));
    }

    public function testEndsBeforeAndEndsAfter() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));

        $this->assertTrue($period->endsBefore(CCarbon::parse('2024-02-01')));
        $this->assertFalse($period->endsBefore(CCarbon::parse('2023-12-01')));
        $this->assertTrue($period->endsAfter(CCarbon::parse('2023-12-01')));
        $this->assertFalse($period->endsAfter(CCarbon::parse('2024-02-01')));
    }

    public function testOverlapsWith() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));
        $overlapping = CPeriod::create(CCarbon::parse('2024-01-05'), CCarbon::parse('2024-01-15'));
        $notOverlapping = CPeriod::create(CCarbon::parse('2024-02-01'), CCarbon::parse('2024-02-10'));

        $this->assertTrue($period->overlapsWith($overlapping));
        $this->assertFalse($period->overlapsWith($notOverlapping));
    }

    public function testTouchesWith() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));
        $touching = CPeriod::create(CCarbon::parse('2024-01-11'), CCarbon::parse('2024-01-20'));
        $notTouching = CPeriod::create(CCarbon::parse('2024-02-01'), CCarbon::parse('2024-02-10'));

        $this->assertTrue($period->touchesWith($touching));
        $this->assertFalse($period->touchesWith($notTouching));
    }

    public function testContainsDate() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));

        $this->assertTrue($period->contains(CCarbon::parse('2024-01-05')));
        $this->assertTrue($period->contains(CCarbon::parse('2024-01-01')));
        $this->assertTrue($period->contains(CCarbon::parse('2024-01-10')));
        $this->assertFalse($period->contains(CCarbon::parse('2024-02-05')));
    }

    public function testContainsPeriod() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-31'));
        $inner = CPeriod::create(CCarbon::parse('2024-01-05'), CCarbon::parse('2024-01-10'));
        $outer = CPeriod::create(CCarbon::parse('2023-12-01'), CCarbon::parse('2024-02-01'));

        $this->assertTrue($period->contains($inner));
        $this->assertFalse($period->contains($outer));
    }

    public function testEquals() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));
        $same = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));
        $different = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-11'));

        $this->assertTrue($period->equals($same));
        $this->assertFalse($period->equals($different));
    }

    public function testGap() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));
        $other = CPeriod::create(CCarbon::parse('2024-02-01'), CCarbon::parse('2024-02-10'));
        $originalPeriodEnd = $period->getEndDate()->format('Y-m-d');
        $originalOtherStart = $other->getStartDate()->format('Y-m-d');

        $gap = $period->gap($other);

        $this->assertInstanceOf(CPeriod::class, $gap);
        $this->assertGreaterThan($originalPeriodEnd, $gap->getStartDate()->format('Y-m-d'));
        $this->assertLessThan($originalOtherStart, $gap->getEndDate()->format('Y-m-d'));
    }

    /**
     * Documents a pre-existing bug: CPeriod's constructor aliases `$includedEnd`
     * to the very same object instance as `$endDate` whenever the end boundary
     * is included (the default). Since CCarbon's add()/sub() mutate in place
     * (unlike CarbonImmutable), gap()'s `$this->includedEnd()->add($this->interval)`
     * silently mutates the original period's own end date as a side effect of
     * merely computing a gap. Out of scope to fix here (not a CCollection*
     * file) - see system/libraries/CPeriod.php's constructor and
     * system/libraries/CPeriod/Trait/OperationTrait.php::gap().
     */
    public function testGapMutatesTheOriginalPeriodsEndDateAsASideEffect() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));
        $other = CPeriod::create(CCarbon::parse('2024-02-01'), CCarbon::parse('2024-02-10'));

        $this->assertSame('2024-01-10', $period->getEndDate()->format('Y-m-d'));

        $period->gap($other);

        $this->assertNotSame('2024-01-10', $period->getEndDate()->format('Y-m-d'));
    }

    public function testGapReturnsNullWhenPeriodsOverlap() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));
        $overlapping = CPeriod::create(CCarbon::parse('2024-01-05'), CCarbon::parse('2024-01-15'));

        $this->assertNull($period->gap($overlapping));
    }

    public function testGapReturnsNullWhenPeriodsTouch() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));
        $touching = CPeriod::create(CCarbon::parse('2024-01-11'), CCarbon::parse('2024-01-20'));

        $this->assertNull($period->gap($touching));
    }

    public function testSubtractWithNoOverlapReturnsWholePeriod() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));
        $other = CPeriod::create(CCarbon::parse('2024-02-01'), CCarbon::parse('2024-02-10'));

        $result = $period->subtract($other);

        $this->assertCount(1, $result);
    }

    public function testSubtractWithOverlapRemovesOverlappingPortion() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));
        $other = CPeriod::create(CCarbon::parse('2024-01-05'), CCarbon::parse('2024-01-15'));

        $result = $period->subtract($other);

        $this->assertCount(1, $result);
        $remaining = $result[0];
        $this->assertSame('2024-01-01', $remaining->getStartDate()->format('Y-m-d'));
        $this->assertSame('2024-01-04', $remaining->getEndDate()->format('Y-m-d'));
    }

    public function testSubtractWithNoArgumentsReturnsSelf() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));

        $result = $period->subtract();

        $this->assertCount(1, $result);
    }

    public function testRenew() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));
        $originalPeriodEnd = $period->getEndDate()->format('Y-m-d');

        $renewed = $period->renew();

        $this->assertInstanceOf(CPeriod::class, $renewed);
        $this->assertGreaterThan($originalPeriodEnd, $renewed->getStartDate()->format('Y-m-d'));
    }

    public function testDaysFactory() {
        $period = CPeriod::days(3);

        $this->assertSame(
            CCarbon::today()->subDays(3)->startOfDay()->format('Y-m-d H:i:s'),
            $period->getStartDate()->format('Y-m-d H:i:s')
        );
        $this->assertSame(
            CCarbon::today()->endOfDay()->format('Y-m-d H:i:s'),
            $period->getEndDate()->format('Y-m-d H:i:s')
        );
    }

    public function testTodayFactory() {
        $period = CPeriod::today();

        $this->assertSame(CCarbon::today()->startOfDay()->format('Y-m-d H:i:s'), $period->getStartDate()->format('Y-m-d H:i:s'));
        $this->assertSame(CCarbon::today()->endOfDay()->format('Y-m-d H:i:s'), $period->getEndDate()->format('Y-m-d H:i:s'));
    }

    public function testYesterdayFactory() {
        $period = CPeriod::yesterday();

        $this->assertSame(CCarbon::today()->subDays(1)->startOfDay()->format('Y-m-d'), $period->getStartDate()->format('Y-m-d'));
        $this->assertSame(CCarbon::today()->subDays(1)->format('Y-m-d'), $period->getEndDate()->format('Y-m-d'));
    }

    public function testMonthsFactory() {
        $period = CPeriod::months(1);

        $this->assertSame(
            CCarbon::today()->subMonths(1)->startOfDay()->format('Y-m-d'),
            $period->getStartDate()->format('Y-m-d')
        );
    }

    public function testYearsFactory() {
        $period = CPeriod::years(1);

        $this->assertSame(
            CCarbon::today()->subYears(1)->startOfDay()->format('Y-m-d'),
            $period->getStartDate()->format('Y-m-d')
        );
    }

    public function testLast7Days() {
        $this->assertTrue(CPeriod::last7Days()->equals(CPeriod::days(6)));
    }

    public function testLast14Days() {
        $this->assertTrue(CPeriod::last14Days()->equals(CPeriod::days(13)));
    }

    public function testLast30Days() {
        $this->assertTrue(CPeriod::last30Days()->equals(CPeriod::days(29)));
    }

    public function testLast3Month() {
        $this->assertTrue(CPeriod::last3Month()->equals(CPeriod::months(3)));
    }

    public function testThisMonthFactory() {
        $period = CPeriod::thisMonth();

        $this->assertSame(CCarbon::now()->startOfMonth()->format('Y-m-d'), $period->getStartDate()->format('Y-m-d'));
        $this->assertSame(CCarbon::now()->endOfMonth()->format('Y-m-d'), $period->getEndDate()->format('Y-m-d'));
    }

    public function testThisYearFactory() {
        $period = CPeriod::thisYear();

        $this->assertSame(CCarbon::now()->startOfYear()->format('Y-m-d'), $period->getStartDate()->format('Y-m-d'));
        $this->assertSame(CCarbon::now()->endOfYear()->format('Y-m-d'), $period->getEndDate()->format('Y-m-d'));
    }

    public function testMakeFactory() {
        $period = CPeriod::make('2024-01-01', '2024-01-10');

        $this->assertSame('2024-01-01', $period->getStartDate()->format('Y-m-d'));
        $this->assertSame('2024-01-10', $period->getEndDate()->format('Y-m-d'));
    }

    public function testFromStringFactory() {
        $period = CPeriod::fromString('[2024-01-01,2024-01-10]');

        $this->assertSame('2024-01-01', $period->getStartDate()->format('Y-m-d'));
        $this->assertSame('2024-01-10', $period->getEndDate()->format('Y-m-d'));
    }

    public function testCreateFromInterval() {
        $period = CPeriod::createFromInterval('day', 5, CCarbon::parse('2024-01-01'));

        $this->assertSame('2024-01-01', $period->getStartDate()->format('Y-m-d'));
        $this->assertSame('2024-01-06', $period->getEndDate()->format('Y-m-d'));
    }

    /**
     * Documents a pre-existing bug: CPeriod_Trait_GetterTrait::start()/end() read
     * an undefined `$this->start` / `$this->end` property (the real property on
     * CPeriod is `$startDate` / `$endDate`), so both throw instead of returning
     * the start/end date. This also breaks asString(), which calls start()/end()
     * internally. Out of scope to fix here (not a CCollection* file) - see
     * system/libraries/CPeriod/Trait/GetterTrait.php.
     */
    public function testStartAndEndGettersAreBrokenDueToUndefinedProperty() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));

        $this->expectException(\ErrorException::class);
        $this->expectExceptionMessage('Undefined property: CPeriod::$start');

        $period->start();
    }

    /**
     * Documents a pre-existing bug: CPeriod_Trait_OperationTrait::overlap() calls
     * CPeriod_Factory::makeWithBoundaries() with an extra leading `static::class`
     * argument that doesn't match makeWithBoundaries()'s 4-parameter signature
     * ($includedStart, $includedEnd, $precision, $boundaries), shifting every
     * argument by one and causing it to try to parse the class name string as a
     * date. This also breaks overlapAny() and diffSymmetric(), which call
     * overlap() internally. Out of scope to fix here (not a CCollection* file) -
     * see system/libraries/CPeriod/Trait/OperationTrait.php.
     */
    public function testOverlapIsBrokenDueToArgumentMismatch() {
        $period = CPeriod::create(CCarbon::parse('2024-01-01'), CCarbon::parse('2024-01-10'));
        $other = CPeriod::create(CCarbon::parse('2024-01-05'), CCarbon::parse('2024-01-15'));

        $this->expectException(CPeriod_Exception_InvalidDateException::class);

        $period->overlap($other);
    }
}
