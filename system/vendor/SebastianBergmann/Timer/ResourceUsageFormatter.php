<?php
/*
 * This file is part of phpunit/php-timer.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Timer;

use function is_float;
use function memory_get_peak_usage;
use function microtime;
use function sprintf;

final class ResourceUsageFormatter
{
    /**
     * @psalm-var array<string,int>
     */
    public static $SIZES = [
        'GB' => 1073741824,
        'MB' => 1048576,
        'KB' => 1024,
    ];

    public function resourceUsage(Duration $duration)
    {
        return sprintf(
            'Time: %s, Memory: %s',
            $duration->asString(),
            $this->bytesToString(memory_get_peak_usage(true))
        );
    }

    /**
     * @throws TimeSinceStartOfRequestNotAvailableException
     */
    public function resourceUsageSinceStartOfRequest()
    {
        if (!isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            throw new TimeSinceStartOfRequestNotAvailableException(
                'Cannot determine time at which the request started because $_SERVER[\'REQUEST_TIME_FLOAT\'] is not available'
            );
        }

        if (!is_float($_SERVER['REQUEST_TIME_FLOAT'])) {
            throw new TimeSinceStartOfRequestNotAvailableException(
                'Cannot determine time at which the request started because $_SERVER[\'REQUEST_TIME_FLOAT\'] is not of type float'
            );
        }

        return $this->resourceUsage(
            Duration::fromMicroseconds(
                (1000000 * (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']))
            )
        );
    }

    private function bytesToString($bytes)
    {
        foreach (self::$SIZES as $unit => $value) {
            if ($bytes >= $value) {
                return sprintf('%.2f %s', $bytes >= 1024 ? $bytes / $value : $bytes, $unit);
            }
        }

        // @codeCoverageIgnoreStart
        return $bytes . ' byte' . ($bytes !== 1 ? 's' : '');
        // @codeCoverageIgnoreEnd
    }
}
