<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util\TestDox;

use function sprintf;
use PHPUnit\Framework\TestResult;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class HtmlResultPrinter extends ResultPrinter
{
    /**
     * @var string
     */
    const PAGE_HEADER = <<<'EOT'
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title>Test Documentation</title>
        <style>
            body {
                text-rendering: optimizeLegibility;
                font-variant-ligatures: common-ligatures;
                font-kerning: normal;
                margin-left: 2em;
            }

            body > ul > li {
                font-family: Source Serif Pro, PT Sans, Trebuchet MS, Helvetica, Arial;
                font-size: 2em;
            }

            h2 {
                font-family: Tahoma, Helvetica, Arial;
                font-size: 3em;
            }

            ul {
                list-style: none;
                margin-bottom: 1em;
            }
        </style>
    </head>
    <body>
EOT;

    /**
     * @var string
     */
    const CLASS_HEADER = <<<'EOT'

        <h2 id="%s">%s</h2>
        <ul>

EOT;

    /**
     * @var string
     */
    const CLASS_FOOTER = <<<'EOT'
        </ul>
EOT;

    /**
     * @var string
     */
    const PAGE_FOOTER = <<<'EOT'

    </body>
</html>
EOT;

    public function printResult(TestResult $result)
    {
    }

    /**
     * Handler for 'start run' event.
     */
    protected function startRun()
    {
        $this->write(self::PAGE_HEADER);
    }

    /**
     * Handler for 'start class' event.
     */
    protected function startClass($name)
    {
        $this->write(
            sprintf(
                self::CLASS_HEADER,
                $name,
                $this->currentTestClassPrettified
            )
        );
    }

    /**
     * Handler for 'on test' event.
     */
    protected function onTest($name, $success = true)
    {
        $this->write(
            sprintf(
                "            <li style=\"color: %s;\">%s %s</li>\n",
                $success ? '#555753' : '#ef2929',
                $success ? '✓' : '❌',
                $name
            )
        );
    }

    /**
     * Handler for 'end class' event.
     */
    protected function endClass($name)
    {
        $this->write(self::CLASS_FOOTER);
    }

    /**
     * Handler for 'end run' event.
     */
    protected function endRun()
    {
        $this->write(self::PAGE_FOOTER);
    }
}
