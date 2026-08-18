<?php
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase {
    public function testSlicesItemsToPerPageAndDetectsMorePages() {
        $p = new CPagination_Paginator(['a', 'b', 'c', 'd'], 3, 1, ['path' => '/']);

        $this->assertSame(['a', 'b', 'c'], $p->items());
        $this->assertTrue($p->hasMorePages());
    }

    public function testHasMorePagesIsFalseWhenExactlyPerPageItemsGiven() {
        $p = new CPagination_Paginator(['a', 'b', 'c'], 3, 1, ['path' => '/']);

        $this->assertSame(['a', 'b', 'c'], $p->items());
        $this->assertFalse($p->hasMorePages());
    }

    public function testNextPageUrlIsNullWhenNoMorePages() {
        $p = new CPagination_Paginator(['a'], 3, 1, ['path' => '/users']);

        $this->assertNull($p->nextPageUrl());
    }

    public function testNextPageUrlPointsForwardWhenMorePagesExist() {
        $p = new CPagination_Paginator(['a', 'b', 'c', 'd'], 3, 2, ['path' => '/users']);

        $this->assertSame('/users?page=3', $p->nextPageUrl());
        $this->assertSame('/users?page=1', $p->previousPageUrl());
    }

    public function testHasMorePagesWhenOverride() {
        $p = new CPagination_Paginator(['a'], 3, 1, ['path' => '/']);
        $p->hasMorePagesWhen(true);

        $this->assertTrue($p->hasMorePages());
    }

    public function testToArrayShapeHasNoTotalOrLastPage() {
        $p = new CPagination_Paginator(['a', 'b'], 2, 1, ['path' => '/users']);

        $array = $p->toArray();

        $this->assertArrayNotHasKey('total', $array);
        $this->assertArrayNotHasKey('last_page', $array);
        $this->assertSame(1, $array['current_page']);
        $this->assertSame(['a', 'b'], $array['data']);
    }

    public function testAcceptsACollectionDirectly() {
        $collection = c::collect(['x', 'y', 'z']);
        $p = new CPagination_Paginator($collection, 2, 1, ['path' => '/']);

        $this->assertSame(['x', 'y'], $p->items());
        $this->assertTrue($p->hasMorePages());
    }
}
