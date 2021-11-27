<?php
use PHPUnit\Framework\TestCase;

class PeriodTest extends TestCase {
    public function testPeriodCreate() {
        $dateStart = CCarbon::now();
        $dateEnd = CCarbon::now()->addDays(1);
        $period = CPeriod::create($dateStart, $dateEnd);
        $this->assertNotNull($period);
    }
}
