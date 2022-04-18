<?php
use PHPUnit\Framework\TestCase;

class PatternBuilderTest extends TestCase {
    public function testPatternBuilder() {
        $this->assertEquals('\\$GPRMC', CString::createPatternBuilder()->text('$GPRMC')->__toString());
        $this->assertEquals('(\\d{2}\\.[0-9a-fA-F]+)', CString::createPatternBuilder()->number('(dd.x+)')->__toString());
        $this->assertEquals('a(?:bc)?', CString::createPatternBuilder()->text('a')->text('b')->text('c')->optional(2)->__toString());
        $this->assertEquals('a|b', CString::createPatternBuilder()->expression('a|b')->__toString());
        $this->assertEquals('ab\\|', CString::createPatternBuilder()->expression('ab|')->__toString());
        $this->assertEquals('|', CString::createPatternBuilder()->or()->__toString());
        $this->assertEquals('\\|\\d|\\d\\|', CString::createPatternBuilder()->number('|d|d|')->__toString());
    }
}
