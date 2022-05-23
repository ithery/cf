<?php

use PHPUnit\Framework\TestCase;

class DatabaseConcernsBuildsQueriesTraitTest extends TestCase {
    public function testTapCallbackInstance() {
        $mock = $this->getMockForTrait(CDatabase_Trait_Builder::class);
        $mock->tap(function ($builder) use ($mock) {
            $this->assertEquals($mock, $builder);
        });
    }
}
