<?php

use PHPUnit\Framework\TestCase;

class EventDispatcherTestEvent {
    /**
     * @var string
     */
    public $nilai;

    public function __construct($nilai = 'a') {
        $this->nilai = $nilai;
    }
}

class EventDispatcherTestOtherEvent {
}

class EventDispatcherTestListener {
    /**
     * @var array
     */
    public static $seen = [];

    /**
     * @param mixed $event
     *
     * @return void
     */
    public function handle($event) {
        static::$seen[] = $event;
    }
}

class EventDispatcherTestInvokable {
    /**
     * @var array
     */
    public static $seen = [];

    /**
     * @param mixed $event
     *
     * @return void
     */
    public function __invoke($event) {
        static::$seen[] = $event;
    }
}

class EventDispatcherTestSubscriber {
    /**
     * @var array
     */
    public static $seen = [];

    /**
     * @param CEvent_Dispatcher $events
     *
     * @return void
     */
    public function subscribe($events) {
        $events->listen('langganan.satu', [$this, 'onSatu']);
        $events->listen('langganan.dua', [$this, 'onDua']);
    }

    /**
     * @return void
     */
    public function onSatu() {
        static::$seen[] = 'satu';
    }

    /**
     * @return void
     */
    public function onDua() {
        static::$seen[] = 'dua';
    }
}

class EventDispatcherTest extends TestCase {
    protected function setUp() {
        EventDispatcherTestListener::$seen = [];
        EventDispatcherTestInvokable::$seen = [];
        EventDispatcherTestSubscriber::$seen = [];
    }

    /**
     * @return CEvent_Dispatcher
     */
    protected function makeDispatcher() {
        return new CEvent_Dispatcher(CContainer::getInstance());
    }

    public function testAClosureListenerIsCalled() {
        $events = $this->makeDispatcher();
        $seen = [];
        //muatan disebar menjadi argumen, bukan dioper utuh sebagai larik
        $events->listen('nama.peristiwa', function ($satu, $dua) use (&$seen) {
            $seen[] = [$satu, $dua];
        });
        $events->dispatch('nama.peristiwa', ['muatan', 'kedua']);

        $this->assertSame([['muatan', 'kedua']], $seen);
    }

    public function testAnObjectEventIsPassedToItsListener() {
        $events = $this->makeDispatcher();
        $seen = null;
        $events->listen(EventDispatcherTestEvent::class, function ($event) use (&$seen) {
            $seen = $event;
        });
        $event = new EventDispatcherTestEvent('b');
        $events->dispatch($event);

        $this->assertSame($event, $seen);
    }

    public function testListenersRunInTheOrderTheyWereRegistered() {
        $events = $this->makeDispatcher();
        $urutan = [];
        $events->listen('urut', function () use (&$urutan) {
            $urutan[] = 'a';
        });
        $events->listen('urut', function () use (&$urutan) {
            $urutan[] = 'b';
        });
        $events->dispatch('urut');

        $this->assertSame(['a', 'b'], $urutan);
    }

    public function testAListenerForAnotherEventStaysQuiet() {
        $events = $this->makeDispatcher();
        $fired = 0;
        $events->listen(EventDispatcherTestEvent::class, function () use (&$fired) {
            $fired++;
        });
        $events->dispatch(new EventDispatcherTestOtherEvent());

        $this->assertSame(0, $fired);
    }

    public function testSeveralEventNamesCanShareOneListener() {
        $events = $this->makeDispatcher();
        $fired = 0;
        $events->listen(['satu', 'dua'], function () use (&$fired) {
            $fired++;
        });
        $events->dispatch('satu');
        $events->dispatch('dua');

        $this->assertSame(2, $fired);
    }

    public function testDispatchCollectsEveryListenerResponse() {
        $events = $this->makeDispatcher();
        $events->listen('kumpul', function () {
            return 'a';
        });
        $events->listen('kumpul', function () {
            return 'b';
        });

        $this->assertSame(['a', 'b'], $events->dispatch('kumpul'));
    }

    /**
     * A listener returning false stops the ones behind it — that is how a
     * listener vetoes an action.
     */
    public function testReturningFalseStopsTheRemainingListeners() {
        $events = $this->makeDispatcher();
        $urutan = [];
        $events->listen('henti', function () use (&$urutan) {
            $urutan[] = 'a';

            return false;
        });
        $events->listen('henti', function () use (&$urutan) {
            $urutan[] = 'b';
        });
        $events->dispatch('henti');

        $this->assertSame(['a'], $urutan);
    }

    public function testHaltStopsAtTheFirstNonNullResponse() {
        $events = $this->makeDispatcher();
        $events->listen('sampai', function () {
            return null;
        });
        $events->listen('sampai', function () {
            return 'jawaban';
        });
        $events->listen('sampai', function () {
            return 'tidak terpakai';
        });

        $this->assertSame('jawaban', $events->until('sampai'));
    }

    public function testUntilGivesNullWhenNobodyAnswers() {
        $events = $this->makeDispatcher();
        $events->listen('sampai', function () {
            return null;
        });

        $this->assertNull($events->until('sampai'));
    }

    public function testAWildcardListenerCatchesAFamilyOfEvents() {
        $events = $this->makeDispatcher();
        $seen = [];
        $events->listen('pesanan.*', function ($eventName) use (&$seen) {
            $seen[] = $eventName;
        });
        $events->dispatch('pesanan.dibuat');
        $events->dispatch('pesanan.dibatalkan');
        $events->dispatch('pengguna.dibuat');

        $this->assertSame(['pesanan.dibuat', 'pesanan.dibatalkan'], $seen);
    }

    public function testHasListenersReportsRegistration() {
        $events = $this->makeDispatcher();

        $this->assertFalse($events->hasListeners('belum'));
        $events->listen('belum', function () {
        });
        $this->assertTrue($events->hasListeners('belum'));
    }

    /**
     * hasListeners() dulu hanya menengok kunci persisnya, jadi sebuah
     * pendengar `pesanan.*` tidak membuat hasListeners('pesanan.dibuat')
     * menjawab true walau dispatch('pesanan.dibuat') benar-benar akan
     * memanggil pendengar itu (lewat getWildcardListeners()'s cstr::is()
     * matching, yang sudah benar dari awal). Sudah diperbaiki supaya
     * hasListeners() memakai hasWildcardListeners() -- yang matching-nya
     * sama persis dengan yang dipakai dispatch() -- bukan cek kunci persis.
     */
    public function testHasListenersMatchesWildcardPatternsLikeDispatchDoes() {
        $events = $this->makeDispatcher();
        $events->listen('pesanan.*', function () {
        });

        $this->assertTrue($events->hasWildcardListeners('pesanan.dibuat'));
        $this->assertTrue($events->hasListeners('pesanan.dibuat'));
        $this->assertTrue($events->hasListeners('pesanan.*'));
        $this->assertFalse($events->hasListeners('pengguna.dibuat'));
    }

    public function testAClassNameListenerIsResolvedAndCalled() {
        $events = $this->makeDispatcher();
        $events->listen('kelas', EventDispatcherTestListener::class);
        $events->dispatch('kelas', ['muatan']);

        $this->assertCount(1, EventDispatcherTestListener::$seen);
    }

    public function testAnInvokableClassListenerIsCalled() {
        $events = $this->makeDispatcher();
        $events->listen('invokable', EventDispatcherTestInvokable::class);
        $events->dispatch('invokable', ['muatan']);

        $this->assertCount(1, EventDispatcherTestInvokable::$seen);
    }

    public function testAClassAtMethodListenerIsCalled() {
        $events = $this->makeDispatcher();
        $events->listen('atsign', EventDispatcherTestListener::class . '@handle');
        $events->dispatch('atsign', ['muatan']);

        $this->assertCount(1, EventDispatcherTestListener::$seen);
    }

    public function testForgetRemovesTheListeners() {
        $events = $this->makeDispatcher();
        $fired = 0;
        $events->listen('lupakan', function () use (&$fired) {
            $fired++;
        });
        $events->forget('lupakan');
        $events->dispatch('lupakan');

        $this->assertSame(0, $fired);
        $this->assertFalse($events->hasListeners('lupakan'));
    }

    public function testForgetRemovesAWildcardListenerToo() {
        $events = $this->makeDispatcher();
        $fired = 0;
        $events->listen('pesanan.*', function () use (&$fired) {
            $fired++;
        });
        $events->forget('pesanan.*');
        $events->dispatch('pesanan.dibuat');

        $this->assertSame(0, $fired);
    }

    /**
     * push() menunda **peristiwanya**, bukan mendaftarkan pendengar: ia
     * menerima muatan, dan baru flush() yang menyalurkannya. Itu yang membuat
     * sebuah permintaan dapat menahan pekerjaan sampai titik yang diketahui.
     */
    public function testAPushedEventOnlyFiresOnFlush() {
        $events = $this->makeDispatcher();
        $seen = [];
        $events->listen('tunda', function ($nilai) use (&$seen) {
            $seen[] = $nilai;
        });
        $events->push('tunda', ['muatan']);
        $this->assertSame([], $seen);

        $events->flush('tunda');
        $this->assertSame(['muatan'], $seen);
    }

    public function testForgetPushedDropsThePendingEvents() {
        $events = $this->makeDispatcher();
        $seen = [];
        $events->listen('tunda', function ($nilai) use (&$seen) {
            $seen[] = $nilai;
        });
        $events->push('tunda', ['muatan']);
        $events->forgetPushed();
        $events->flush('tunda');

        $this->assertSame([], $seen);
    }

    public function testASubscriberRegistersItsOwnListeners() {
        $events = $this->makeDispatcher();
        $events->subscribe(EventDispatcherTestSubscriber::class);
        $events->dispatch('langganan.satu');
        $events->dispatch('langganan.dua');

        $this->assertSame(['satu', 'dua'], EventDispatcherTestSubscriber::$seen);
    }

    public function testGetListenersIncludesTheWildcardOnes() {
        $events = $this->makeDispatcher();
        $events->listen('pesanan.dibuat', function () {
        });
        $events->listen('pesanan.*', function () {
        });

        $this->assertCount(2, $events->getListeners('pesanan.dibuat'));
    }

    public function testDispatchingWithNoListenersIsHarmless() {
        $events = $this->makeDispatcher();

        $this->assertSame([], $events->dispatch('tidak.ada.pendengar'));
    }
}
