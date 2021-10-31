<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Framework;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class WarningTestCase extends TestCase
{
    /**
     * @var bool
     */
    protected $backupGlobals = false;

    /**
     * @var bool
     */
    protected $backupStaticAttributes = false;

    /**
     * @var bool
     */
    protected $runTestInSeparateProcess = false;

    /**
     * @var string
     */
    private $message;

    public function __construct($message = '')
    {
        $this->message = $message;

        parent::__construct('Warning');
    }

    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Returns a string representation of the test case.
     */
    public function toString()
    {
        return 'Warning';
    }

    /**
     * @throws Exception
     *
     * @psalm-return never-return
     */
    protected function runTest()
    {
        throw new Warning($this->message);
    }
}
