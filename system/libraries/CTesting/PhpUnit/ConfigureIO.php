<?php

declare(strict_types=1);

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\Output;

/**
 * @internal
 */
final class CTesting_PHPUnit_ConfigureIO {
    /**
     * Configures both given input and output with
     * options from the enviroment.
     *
     * @throws \ReflectionException
     */
    public static function of(InputInterface $input, Output $output): void {
        $application = new Application();
        $reflector = new ReflectionObject($application);
        $method = $reflector->getMethod('configureIO');
        $method->setAccessible(true);
        $method->invoke($application, $input, $output);
    }
}
