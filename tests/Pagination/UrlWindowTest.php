<?php

use PHPUnit\Framework\TestCase;

/**
 * `CPagination_UrlWindow` menyusun tiga kelompok nomor halaman yang dipakai
 * penampil paginasi: `first`, `slider`, dan `last`.
 *
 * Yang dijaga di sini batas-batas peralihannya. Ambangnya majemuk -
 * `onEachSide * 2 + 8` menentukan jendela kecil, sedangkan `onEachSide + 4`
 * menentukan seberapa dekat halaman berjalan boleh ke tepi - dan kesalahan satu
 * angka di situ tidak melempar apa pun, hanya menghasilkan tautan halaman yang
 * salah.
 */
class UrlWindowTest extends TestCase {
    /**
     * @param int      $lastPage
     * @param int      $currentPage
     * @param null|int $onEachSide
     *
     * @return CPagination_LengthAwarePaginator
     */
    private function paginator($lastPage, $currentPage, $onEachSide = null) {
        $paginator = new CPagination_LengthAwarePaginator(['a'], $lastPage * 10, 10, $currentPage, ['path' => '/u']);
        if ($onEachSide !== null) {
            $paginator->onEachSide($onEachSide);
        }

        return $paginator;
    }

    /**
     * @param null|array $group
     *
     * @return null|array
     */
    private function pages($group) {
        return $group === null ? null : array_map('intval', array_keys($group));
    }

    /**
     * @return void
     */
    public function testDefaultOnEachSideIsThree() {
        $this->assertSame(3, $this->paginator(20, 10)->onEachSide);
    }

    /**
     * @return void
     */
    public function testSmallSliderListsEveryPageInFirstGroup() {
        $window = CPagination_UrlWindow::make($this->paginator(13, 1));

        $this->assertSame(range(1, 13), $this->pages($window['first']));
        $this->assertNull($window['slider']);
        $this->assertNull($window['last']);
    }

    /**
     * Ambang jendela kecil: `lastPage < onEachSide * 2 + 8`, jadi 13 masih
     * kecil dan 14 sudah memakai penggeser.
     *
     * @return void
     */
    public function testThirteenPagesIsSmallButFourteenSwitchesToSlider() {
        $small = CPagination_UrlWindow::make($this->paginator(13, 1));
        $this->assertCount(13, $small['first']);
        $this->assertNull($small['last']);

        $slider = CPagination_UrlWindow::make($this->paginator(14, 1));
        $this->assertSame([13, 14], $this->pages($slider['last']), 'halaman ke-14 seharusnya sudah memakai penggeser');
    }

    /**
     * @return void
     */
    public function testSinglePageStillListsThatPage() {
        $window = CPagination_UrlWindow::make($this->paginator(1, 1));

        $this->assertSame([1], $this->pages($window['first']));
        $this->assertNull($window['slider']);
        $this->assertNull($window['last']);
    }

    /**
     * Dekat awal: seluruh awal dirender sampai `window + onEachSide`, tanpa
     * penggeser, ditutup dua halaman terakhir.
     *
     * @return void
     */
    public function testNearBeginningRendersLeadingPagesAndTheClosingCap() {
        $window = CPagination_UrlWindow::make($this->paginator(20, 1));

        $this->assertSame(range(1, 10), $this->pages($window['first']));
        $this->assertNull($window['slider']);
        $this->assertSame([19, 20], $this->pages($window['last']));
    }

    /**
     * Halaman ke-7 masih dianggap dekat awal (`currentPage <= onEachSide + 4`),
     * halaman ke-8 sudah tidak.
     *
     * @return void
     */
    public function testSeventhPageIsStillNearBeginningButEighthIsNot() {
        $seventh = CPagination_UrlWindow::make($this->paginator(20, 7));
        $this->assertSame(range(1, 10), $this->pages($seventh['first']));
        $this->assertNull($seventh['slider']);

        $eighth = CPagination_UrlWindow::make($this->paginator(20, 8));
        $this->assertSame([1, 2], $this->pages($eighth['first']));
        $this->assertSame(range(5, 11), $this->pages($eighth['slider']));
    }

    /**
     * @return void
     */
    public function testNearEndingRendersOpeningCapAndTrailingPages() {
        $window = CPagination_UrlWindow::make($this->paginator(20, 20));

        $this->assertSame([1, 2], $this->pages($window['first']));
        $this->assertNull($window['slider']);
        $this->assertSame(range(11, 20), $this->pages($window['last']));
    }

    /**
     * @return void
     */
    public function testThirteenthPageStillSlidesButFourteenthIsNearEnding() {
        $sliding = CPagination_UrlWindow::make($this->paginator(20, 13));
        $this->assertSame(range(10, 16), $this->pages($sliding['slider']));

        $ending = CPagination_UrlWindow::make($this->paginator(20, 14));
        $this->assertNull($ending['slider']);
        $this->assertSame(range(11, 20), $this->pages($ending['last']));
    }

    /**
     * @return void
     */
    public function testFullSliderIsCenteredOnTheCurrentPage() {
        $window = CPagination_UrlWindow::make($this->paginator(20, 10));

        $this->assertSame([1, 2], $this->pages($window['first']));
        $this->assertSame(range(7, 13), $this->pages($window['slider']));
        $this->assertSame([19, 20], $this->pages($window['last']));
    }

    /**
     * @return void
     */
    public function testOnEachSideNarrowsTheSlider() {
        $window = CPagination_UrlWindow::make($this->paginator(20, 10, 1));

        $this->assertSame([9, 10, 11], $this->pages($window['slider']));
    }

    /**
     * @return void
     */
    public function testGroupsHoldUrlsKeyedByPageNumber() {
        $window = CPagination_UrlWindow::make($this->paginator(20, 10));

        $this->assertSame('/u?page=1', $window['first'][1]);
        $this->assertSame('/u?page=10', $window['slider'][10]);
        $this->assertSame('/u?page=20', $window['last'][20]);
    }

    /**
     * @return void
     */
    public function testHasPagesIsFalseForASinglePage() {
        $this->assertFalse((new CPagination_UrlWindow($this->paginator(1, 1)))->hasPages());
        $this->assertTrue((new CPagination_UrlWindow($this->paginator(2, 1)))->hasPages());
    }

    /**
     * @return void
     */
    public function testStartAndFinishCapsAreTwoPagesEach() {
        $urlWindow = new CPagination_UrlWindow($this->paginator(20, 10));

        $this->assertSame([1, 2], $this->pages($urlWindow->getStart()));
        $this->assertSame([19, 20], $this->pages($urlWindow->getFinish()));
    }

    /**
     * @return void
     */
    public function testAdjacentUrlRangeSurroundsTheCurrentPage() {
        $urlWindow = new CPagination_UrlWindow($this->paginator(20, 10));

        $this->assertSame(range(8, 12), $this->pages($urlWindow->getAdjacentUrlRange(2)));
    }

    /**
     * @return void
     */
    public function testMakeMatchesGetOnAnInstance() {
        $paginator = $this->paginator(20, 10);

        $this->assertEquals(
            (new CPagination_UrlWindow($paginator))->get(),
            CPagination_UrlWindow::make($paginator)
        );
    }
}
