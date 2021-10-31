<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration\Logging;

use PHPUnit\TextUI\XmlConfiguration\Exception;
use PHPUnit\TextUI\XmlConfiguration\Logging\TestDox\Html as TestDoxHtml;
use PHPUnit\TextUI\XmlConfiguration\Logging\TestDox\Text as TestDoxText;
use PHPUnit\TextUI\XmlConfiguration\Logging\TestDox\Xml as TestDoxXml;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class Logging
{
    /**
     * @var ?Junit
     */
    private $junit;

    /**
     * @var ?Text
     */
    private $text;

    /**
     * @var ?TeamCity
     */
    private $teamCity;

    /**
     * @var ?TestDoxHtml
     */
    private $testDoxHtml;

    /**
     * @var ?TestDoxText
     */
    private $testDoxText;

    /**
     * @var ?TestDoxXml
     */
    private $testDoxXml;

    public function __construct($junit, $text, $teamCity, $testDoxHtml,  $testDoxText,  $testDoxXml)
    {
        $this->junit       = $junit;
        $this->text        = $text;
        $this->teamCity    = $teamCity;
        $this->testDoxHtml = $testDoxHtml;
        $this->testDoxText = $testDoxText;
        $this->testDoxXml  = $testDoxXml;
    }

    public function hasJunit()
    {
        return $this->junit !== null;
    }

    public function junit()
    {
        if ($this->junit === null) {
            throw new Exception('Logger "JUnit XML" is not configured');
        }

        return $this->junit;
    }

    public function hasText()
    {
        return $this->text !== null;
    }

    public function text()
    {
        if ($this->text === null) {
            throw new Exception('Logger "Text" is not configured');
        }

        return $this->text;
    }

    public function hasTeamCity()
    {
        return $this->teamCity !== null;
    }

    public function teamCity()
    {
        if ($this->teamCity === null) {
            throw new Exception('Logger "Team City" is not configured');
        }

        return $this->teamCity;
    }

    public function hasTestDoxHtml()
    {
        return $this->testDoxHtml !== null;
    }

    public function testDoxHtml()
    {
        if ($this->testDoxHtml === null) {
            throw new Exception('Logger "TestDox HTML" is not configured');
        }

        return $this->testDoxHtml;
    }

    public function hasTestDoxText()
    {
        return $this->testDoxText !== null;
    }

    public function testDoxText()
    {
        if ($this->testDoxText === null) {
            throw new Exception('Logger "TestDox Text" is not configured');
        }

        return $this->testDoxText;
    }

    public function hasTestDoxXml()
    {
        return $this->testDoxXml !== null;
    }

    public function testDoxXml()
    {
        if ($this->testDoxXml === null) {
            throw new Exception('Logger "TestDox XML" is not configured');
        }

        return $this->testDoxXml;
    }
}
