<?php

use PHPUnit\Framework\TestCase;

/**
 * `CPagination` hanya meneruskan pemilihan gaya ke `CPagination_Paginator`,
 * yang menyimpannya sebagai properti statis bersama.
 *
 * Karena statis, nilainya bertahan lintas test - maka nilai semula disimpan
 * dan dikembalikan pada tiap test, supaya berkas ini tidak diam-diam mengubah
 * tampilan paginasi bagi test lain yang kebetulan berjalan sesudahnya.
 */
class PaginationStyleTest extends TestCase {
    /**
     * @var string
     */
    private $defaultView;

    /**
     * @var string
     */
    private $defaultSimpleView;

    protected function setUp(): void {
        parent::setUp();
        $this->defaultView = CPagination_AbstractPaginator::$defaultView;
        $this->defaultSimpleView = CPagination_AbstractPaginator::$defaultSimpleView;
    }

    protected function tearDown(): void {
        CPagination_AbstractPaginator::$defaultView = $this->defaultView;
        CPagination_AbstractPaginator::$defaultSimpleView = $this->defaultSimpleView;
        parent::tearDown();
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public function styleProvider() {
        return [
            'tailwind' => ['useTailwind', 'pagination.tailwind', 'pagination.simple-tailwind'],
            'bootstrap' => ['useBootstrap', 'pagination.bootstrap-4', 'pagination.simple-bootstrap-4'],
            'bootstrap 3' => ['useBootstrapThree', 'pagination.default', 'pagination.simple-default'],
            'bootstrap 4' => ['useBootstrapFour', 'pagination.bootstrap-4', 'pagination.simple-bootstrap-4'],
            'bootstrap 5' => ['useBootstrapFive', 'pagination.bootstrap-5', 'pagination.simple-bootstrap-5'],
        ];
    }

    /**
     * @dataProvider styleProvider
     *
     * @param string $method
     * @param string $expectedView
     * @param string $expectedSimpleView
     *
     * @return void
     */
    public function testStyleSelectionSetsBothDefaultViews($method, $expectedView, $expectedSimpleView) {
        CPagination::{$method}();

        $this->assertSame($expectedView, CPagination_AbstractPaginator::$defaultView);
        $this->assertSame($expectedSimpleView, CPagination_AbstractPaginator::$defaultSimpleView);
    }

    /**
     * `useBootstrap()` tanpa angka dan `useBootstrapFour()` sengaja mengarah ke
     * berkas yang sama - dijaga supaya salah satunya tidak diam-diam bergeser.
     *
     * @return void
     */
    public function testPlainBootstrapIsTheSameAsBootstrapFour() {
        CPagination::useBootstrap();
        $plain = [CPagination_AbstractPaginator::$defaultView, CPagination_AbstractPaginator::$defaultSimpleView];

        CPagination::useBootstrapFour();
        $four = [CPagination_AbstractPaginator::$defaultView, CPagination_AbstractPaginator::$defaultSimpleView];

        $this->assertSame($plain, $four);
    }

    /**
     * Nilai yang berlaku saat berjalan sengaja tidak diuji.
     *
     * Deklarasi kelasnya `pagination.tailwind`, tetapi bootstrap aplikasi
     * menimpanya - saat berkas ini ditulis, yang aktif `pagination.bootstrap-4`.
     * Meng-assert nilai itu berarti mengunci konfigurasi boot ke dalam test
     * paginasi, dan ia akan gagal begitu setelan temanya diubah tanpa ada yang
     * rusak sama sekali.
     *
     * @return void
     */
    public function testSelectingAStyleIsVisibleThroughThePaginatorToo() {
        CPagination::useBootstrapFive();

        $this->assertSame('pagination.bootstrap-5', CPagination_Paginator::$defaultView);
    }
}
