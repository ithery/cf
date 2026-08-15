<?php

use PHPUnit\Framework\TestCase;

/**
 * `CPagination_CursorPaginator` memaginasi tanpa nomor halaman: posisinya
 * ditandai nilai kolom dari baris terakhir yang ditampilkan.
 *
 * Dua hal yang paling mudah salah dan karena itu dijaga di sini - pemotongan
 * satu baris lebih untuk mendeteksi masih adanya halaman berikutnya, dan
 * pembalikan urutan ketika kursornya menunjuk mundur.
 */
class CursorPaginatorTest extends TestCase {
    /**
     * @return array
     */
    private function rows(array $ids) {
        return array_map(function ($id) {
            return ['id' => $id, 'name' => 'n' . $id];
        }, $ids);
    }

    /**
     * @return array
     */
    private function options() {
        return ['path' => '/u', 'parameters' => ['id']];
    }

    /**
     * @param int                     $perPage
     * @param null|CPagination_Cursor $cursor
     *
     * @return CPagination_CursorPaginator
     */
    private function paginator(array $ids, $perPage = 3, $cursor = null) {
        return new CPagination_CursorPaginator($this->rows($ids), $perPage, $cursor, $this->options());
    }

    /**
     * @param CPagination_CursorPaginator $paginator
     *
     * @return array
     */
    private function ids($paginator) {
        return array_column($paginator->items(), 'id');
    }

    /**
     * Baris ke-(perPage + 1) hanya dipakai sebagai penanda bahwa masih ada
     * lanjutannya, dan tidak ikut ditampilkan.
     *
     * @return void
     */
    public function testSlicesToPerPageAndUsesTheExtraRowAsAMoreFlag() {
        $paginator = $this->paginator([1, 2, 3, 4]);

        $this->assertSame([1, 2, 3], $this->ids($paginator));
        $this->assertSame(3, $paginator->count());
        $this->assertTrue($paginator->hasMorePages());
    }

    /**
     * @return void
     */
    public function testExactlyPerPageRowsMeansNoMorePages() {
        $paginator = $this->paginator([1, 2, 3]);

        $this->assertSame([1, 2, 3], $this->ids($paginator));
        $this->assertFalse($paginator->hasMorePages());
    }

    /**
     * @return void
     */
    public function testFirstPageHasNoPreviousCursor() {
        $paginator = $this->paginator([1, 2, 3, 4]);

        $this->assertTrue($paginator->onFirstPage());
        $this->assertNull($paginator->previousCursor());
        $this->assertNull($paginator->previousPageUrl());
    }

    /**
     * @return void
     */
    public function testNextCursorIsBuiltFromTheLastVisibleRow() {
        $paginator = $this->paginator([1, 2, 3, 4]);

        $this->assertSame(
            ['id' => 3, '_pointsToNextItems' => true],
            $paginator->nextCursor()->toArray()
        );
    }

    /**
     * @return void
     */
    public function testFollowingACursorLeavesTheFirstPageAndOffersAWayBack() {
        $paginator = new CPagination_CursorPaginator(
            $this->rows([4]),
            3,
            new CPagination_Cursor(['id' => 3], true),
            $this->options()
        );

        $this->assertSame([4], $this->ids($paginator));
        $this->assertFalse($paginator->onFirstPage());
        $this->assertFalse($paginator->hasMorePages());
        $this->assertSame(
            ['id' => 4, '_pointsToNextItems' => false],
            $paginator->previousCursor()->toArray()
        );
    }

    /**
     * Kueri mundur mengembalikan baris dalam urutan terbalik, jadi paginator
     * membalikkannya lagi supaya tampilannya tetap menaik.
     *
     * @return void
     */
    public function testBackwardCursorRestoresTheDisplayOrder() {
        $paginator = new CPagination_CursorPaginator(
            $this->rows([4, 3, 2]),
            3,
            new CPagination_Cursor(['id' => 5], false),
            $this->options()
        );

        $this->assertSame([2, 3, 4], $this->ids($paginator));
    }

    /**
     * Mundur dan sudah tidak ada sisanya berarti memang sudah di halaman
     * pertama.
     *
     * @return void
     */
    public function testBackwardCursorWithoutRemainderCountsAsFirstPage() {
        $paginator = new CPagination_CursorPaginator(
            $this->rows([4, 3, 2]),
            3,
            new CPagination_Cursor(['id' => 5], false),
            $this->options()
        );

        $this->assertTrue($paginator->onFirstPage());
    }

    /**
     * @return void
     */
    public function testUrlCarriesTheEncodedCursor() {
        $paginator = $this->paginator([1, 2, 3, 4]);
        $expected = '/u?cursor=' . $paginator->nextCursor()->encode();

        $this->assertSame($expected, $paginator->nextPageUrl());
    }

    /**
     * @return void
     */
    public function testAppendsFragmentAndCursorNameAllReachTheUrl() {
        $paginator = $this->paginator([1, 2, 3, 4]);
        $paginator->appends(['q' => 'cari'])->fragment('hasil')->setCursorName('kur');

        $url = $paginator->nextPageUrl();

        $this->assertStringStartsWith('/u?q=cari&kur=', $url);
        $this->assertStringEndsWith('#hasil', $url);
        $this->assertSame('kur', $paginator->getCursorName());
    }

    /**
     * @return void
     */
    public function testWithPathReplacesTheBaseUrl() {
        $paginator = $this->paginator([1, 2], 1)->withPath('/lain');

        $this->assertSame('/lain', $paginator->path());
        $this->assertStringStartsWith('/lain?cursor=', $paginator->nextPageUrl());
    }

    /**
     * @return void
     */
    public function testEmptyPaginatorHasNothingToOffer() {
        $paginator = $this->paginator([]);

        $this->assertSame(0, $paginator->count());
        $this->assertTrue($paginator->isEmpty());
        $this->assertFalse($paginator->isNotEmpty());
        $this->assertFalse($paginator->hasPages());
        $this->assertNull($paginator->nextCursor());
        $this->assertNull($paginator->nextPageUrl());
    }

    /**
     * @return void
     */
    public function testObjectRowsAreSupportedAlongsideArrays() {
        $paginator = new CPagination_CursorPaginator(
            [(object) ['id' => 9], (object) ['id' => 10]],
            1,
            null,
            $this->options()
        );

        $this->assertSame(['id' => 9, '_pointsToNextItems' => true], $paginator->nextCursor()->toArray());
    }

    /**
     * @return void
     */
    public function testThroughMapsEveryVisibleRow() {
        $paginator = $this->paginator([1, 2], 5)->through(function ($row) {
            $row['id'] *= 10;

            return $row;
        });

        $this->assertSame([10, 20], $this->ids($paginator));
    }

    /**
     * @return void
     */
    public function testToArrayCarriesBothCursorsAndTheirUrls() {
        $paginator = $this->paginator([1, 2, 3, 4]);
        $array = $paginator->toArray();

        $this->assertSame(
            ['data', 'path', 'per_page', 'next_cursor', 'next_page_url', 'prev_cursor', 'prev_page_url'],
            array_keys($array)
        );
        $this->assertSame(3, $array['per_page']);
        $this->assertSame('/u', $array['path']);
        $this->assertNull($array['prev_cursor']);
        $this->assertSame($paginator->nextCursor()->encode(), $array['next_cursor']);
    }

    /**
     * @return void
     */
    public function testCursorNameDefaultsToCursor() {
        $this->assertSame('cursor', $this->paginator([1])->getCursorName());
    }

    /**
     * @return void
     */
    public function testScalarRowsAreRejectedWhenBuildingACursor() {
        $paginator = new CPagination_CursorPaginator([1, 2], 1, null, $this->options());

        $this->expectException(Exception::class);
        $paginator->nextCursor();
    }
}
