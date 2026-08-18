<?php
use Mockery as m;
use PHPUnit\Framework\TestCase;

class DatabaseAbstractSchemaGrammarTest extends TestCase {
    protected function tearDown(): void {
        m::close();
    }

    public function testCreateDatabase() {
        $grammar = new class() extends CDatabase_Schema_Grammar {
        };

        $this->assertSame(
            'create database "foo"',
            $grammar->compileCreateDatabase('foo', m::mock(CDatabase_Connection::class))
        );
    }

    public function testDropDatabaseIfExists() {
        $grammar = new class() extends CDatabase_Schema_Grammar {
        };

        $this->assertSame('drop database if exists "foo"', $grammar->compileDropDatabaseIfExists('foo'));
    }
}
