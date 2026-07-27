<?php
use PHPUnit\Framework\TestCase;

class LengthAwarePaginatorTest extends TestCase {
    public function testBasicAttributes() {
        $p = new CPagination_LengthAwarePaginator(['a', 'b', 'c'], 10, 3, 2, ['path' => '/users']);

        $this->assertSame(2, $p->currentPage());
        $this->assertSame(3, $p->perPage());
        $this->assertSame(10, $p->total());
        $this->assertSame(4, $p->lastPage());
        $this->assertSame(['a', 'b', 'c'], $p->items());
        $this->assertSame(4, $p->firstItem());
        $this->assertSame(6, $p->lastItem());
    }

    public function testHasMorePagesIsBasedOnCurrentPageVsLastPage() {
        $p = new CPagination_LengthAwarePaginator(['a'], 10, 3, 4, ['path' => '/']);
        $this->assertFalse($p->hasMorePages());

        $p = new CPagination_LengthAwarePaginator(['a', 'b', 'c'], 10, 3, 1, ['path' => '/']);
        $this->assertTrue($p->hasMorePages());
    }

    public function testOnFirstPage() {
        $p = new CPagination_LengthAwarePaginator([], 0, 15, 1, ['path' => '/']);
        $this->assertTrue($p->onFirstPage());

        $p = new CPagination_LengthAwarePaginator([], 0, 15, 2, ['path' => '/']);
        $this->assertFalse($p->onFirstPage());
    }

    public function testUrlGeneration() {
        $p = new CPagination_LengthAwarePaginator(['a'], 10, 3, 2, ['path' => '/users']);

        $this->assertSame('/users?page=3', $p->url(3));
        $this->assertSame('/users?page=1', $p->url(0), 'page <= 0 should clamp to 1');
        $this->assertSame('/users?page=3', $p->nextPageUrl());
        $this->assertSame('/users?page=1', $p->previousPageUrl());
    }

    public function testNextAndPreviousPageUrlAreNullAtTheEdges() {
        $first = new CPagination_LengthAwarePaginator(['a'], 10, 3, 1, ['path' => '/']);
        $this->assertNull($first->previousPageUrl());
        $this->assertNotNull($first->nextPageUrl());

        $last = new CPagination_LengthAwarePaginator(['a'], 10, 3, 4, ['path' => '/']);
        $this->assertNull($last->nextPageUrl());
        $this->assertNotNull($last->previousPageUrl());
    }

    public function testAppendsAddsQueryStringToUrls() {
        $p = new CPagination_LengthAwarePaginator(['a'], 10, 3, 1, ['path' => '/users']);
        $p->appends(['sort' => 'name']);

        $this->assertSame('/users?sort=name&page=2', $p->url(2));
    }

    public function testAppendsIgnoresThePageNameKey() {
        $p = new CPagination_LengthAwarePaginator(['a'], 10, 3, 1, ['path' => '/users']);
        $p->appends('page', 99);

        $this->assertSame('/users?page=2', $p->url(2));
    }

    public function testFragmentIsAppendedToUrls() {
        $p = new CPagination_LengthAwarePaginator(['a'], 10, 3, 1, ['path' => '/users']);
        $p->fragment('results');

        $this->assertSame('/users?page=2#results', $p->url(2));
    }

    public function testEmptyResultSetHasNullFirstAndLastItem() {
        $p = new CPagination_LengthAwarePaginator([], 0, 15, 1, ['path' => '/']);

        $this->assertNull($p->firstItem());
        $this->assertNull($p->lastItem());
        $this->assertTrue($p->isEmpty());
        $this->assertFalse($p->isNotEmpty());
    }

    public function testToArrayShapeAndJsonSerialize() {
        $p = new CPagination_LengthAwarePaginator(['a', 'b'], 5, 2, 2, ['path' => '/users']);

        $array = $p->toArray();

        $this->assertSame(2, $array['current_page']);
        $this->assertSame(['a', 'b'], $array['data']);
        $this->assertSame(5, $array['total']);
        $this->assertSame(3, $array['last_page']);
        $this->assertSame($array, $p->jsonSerialize());
        $this->assertSame(json_encode($array), $p->toJson());
    }

    public function testArrayAccessDelegatesToUnderlyingCollection() {
        $p = new CPagination_LengthAwarePaginator(['a', 'b', 'c'], 3, 3, 1, ['path' => '/']);

        $this->assertTrue(isset($p[0]));
        $this->assertSame('a', $p[0]);

        $p[0] = 'z';
        $this->assertSame('z', $p[0]);

        unset($p[0]);
        $this->assertFalse(isset($p[0]));
    }

    public function testCountReflectsTheCurrentPageSliceNotTheGrandTotal() {
        $p = new CPagination_LengthAwarePaginator(['a', 'b'], 50, 2, 1, ['path' => '/']);

        $this->assertCount(2, $p);
        $this->assertSame(50, $p->total());
    }

    public function testInvalidCurrentPageFallsBackToOne() {
        $p = new CPagination_LengthAwarePaginator(['a'], 10, 3, 'not-a-page', ['path' => '/']);

        $this->assertSame(1, $p->currentPage());
    }
}
