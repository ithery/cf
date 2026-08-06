<?php

use PHPUnit\Framework\TestCase;

/**
 * Proposed 2026-08-03 after a file descriptor leak in a Tribelio daemon ran
 * for months undetected - the daemon's only self-reported metric was memory
 * usage, and a leaking process has an entirely normal memory footprint.
 * CDaemon_ProcessMetrics::forPid() is a plain function of a pid rather than
 * a CDaemon_Runner method specifically so these tests can point it at the
 * running PHPUnit process's own getmypid() - a real, live /proc entry -
 * instead of needing an actual daemon.
 */
class ProcessMetricsTest extends TestCase {
    protected function setUp() {
        if (!is_dir('/proc')) {
            $this->markTestSkipped('/proc is not available on this platform.');
        }
    }

    public function testForPidReadsRealMetricsForTheCurrentProcess() {
        $metrics = CDaemon_ProcessMetrics::forPid(getmypid());

        $this->assertNotNull($metrics);
        // At minimum stdin/stdout/stderr are open, so this is never zero for
        // the process actually running this test.
        $this->assertGreaterThan(0, $metrics['fdUsed']);
        $this->assertGreaterThanOrEqual(0, $metrics['ageSeconds']);
        $this->assertGreaterThanOrEqual(0, $metrics['cpuSeconds']);

        if ($metrics['fdLimit'] !== null) {
            $this->assertGreaterThan($metrics['fdUsed'], $metrics['fdLimit']);
        }
    }

    public function testForPidComputesTheCpuRatioFromAgeAndCpuTime() {
        $metrics = CDaemon_ProcessMetrics::forPid(getmypid());

        if ($metrics['ageSeconds'] > 0) {
            $this->assertEquals(
                round($metrics['cpuSeconds'] / $metrics['ageSeconds'], 4),
                $metrics['cpuRatio']
            );
        } else {
            $this->assertNull($metrics['cpuRatio']);
        }
    }

    /**
     * A pid that has never existed on this machine - /proc/<pid>/fd simply
     * won't exist, and this must degrade to nulls rather than warn/crash.
     */
    public function testForPidReturnsAllNullsForAPidThatDoesNotExist() {
        $metrics = CDaemon_ProcessMetrics::forPid(999999999);

        $this->assertNotNull($metrics);
        $this->assertNull($metrics['fdUsed']);
        $this->assertNull($metrics['fdLimit']);
        $this->assertNull($metrics['ageSeconds']);
        $this->assertNull($metrics['cpuSeconds']);
        $this->assertNull($metrics['cpuRatio']);
    }

    public function testFormatShowsDashesForNullMetrics() {
        $this->assertSame(['fd' => '-', 'age' => '-', 'cpu' => '-'], CDaemon_ProcessMetrics::format(null));
    }

    public function testFormatRendersFdAsUsedOverLimit() {
        $formatted = CDaemon_ProcessMetrics::format([
            'fdUsed' => 42, 'fdLimit' => 1024, 'ageSeconds' => null, 'cpuSeconds' => null, 'cpuRatio' => null,
        ]);

        $this->assertSame('42 / 1024', $formatted['fd']);
    }

    public function testFormatRendersFdWithoutALimitWhenUnlimited() {
        $formatted = CDaemon_ProcessMetrics::format([
            'fdUsed' => 42, 'fdLimit' => null, 'ageSeconds' => null, 'cpuSeconds' => null, 'cpuRatio' => null,
        ]);

        $this->assertSame('42', $formatted['fd']);
    }

    /**
     * @dataProvider durationProvider
     */
    public function testFormatRendersAgeAsAHumanDuration($seconds, $expected) {
        $formatted = CDaemon_ProcessMetrics::format([
            'fdUsed' => null, 'fdLimit' => null, 'ageSeconds' => $seconds, 'cpuSeconds' => null, 'cpuRatio' => null,
        ]);

        $this->assertSame($expected, $formatted['age']);
    }

    public function durationProvider() {
        return [
            'seconds only' => [45, '45s'],
            'minutes and seconds' => [125, '2m 5s'],
            'hours and minutes' => [3725, '1h 2m'],
            'days and hours' => [90000, '1d 1h'],
        ];
    }

    public function testFormatRendersCpuTimeWithItsRatioPercentage() {
        // 27 minutes of age, 2 seconds of CPU time - the healthy end of the
        // ratio the TODO describes (well under 1%).
        $formatted = CDaemon_ProcessMetrics::format([
            'fdUsed' => null, 'fdLimit' => null, 'ageSeconds' => 1620, 'cpuSeconds' => 2, 'cpuRatio' => round(2 / 1620, 4),
        ]);

        $this->assertSame('2s (0.1%)', $formatted['cpu']);
    }

    public function testFormatRendersASpinningDaemonsHighCpuRatio() {
        // Mirrors the incident: ~90 days of CPU time accrued while five
        // sibling daemons on the same box sat at ~1 second each.
        $ageSeconds = 90 * 86400;
        $formatted = CDaemon_ProcessMetrics::format([
            'fdUsed' => null, 'fdLimit' => null, 'ageSeconds' => $ageSeconds, 'cpuSeconds' => $ageSeconds, 'cpuRatio' => 1.0,
        ]);

        $this->assertSame('90d 0h (100%)', $formatted['cpu']);
    }
}
