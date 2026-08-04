<?php

use PHPUnit\Framework\TestCase;

class QueueUniqueLockTestJob {
}

class QueueUniqueLockTestOtherJob {
}

class QueueUniqueLockTestJobWithProperty {
    /**
     * @var string
     */
    public $uniqueId = '';

    /**
     * @var int
     */
    public $uniqueFor = 0;
}

class QueueUniqueLockTestJobWithMethod {
    /**
     * @var string
     */
    public $id = '';

    /**
     * @return string
     */
    public function uniqueId() {
        return $this->id;
    }

    /**
     * @return int
     */
    public function uniqueFor() {
        return 3600;
    }
}

class QueueUniqueLockTest extends TestCase {
    /**
     * @var CCache_Repository
     */
    protected $cache;

    protected function setUp() {
        //penyimpan array dipakai bersama sepanjang proses, jadi kunci yang
        //ditinggalkan satu test akan menolak test berikutnya
        $this->cache = CCache::manager()->driver('array');
        $this->cache->flush();
    }

    /**
     * @return CQueue_UniqueLock
     */
    protected function makeLock() {
        return new CQueue_UniqueLock($this->cache);
    }

    /**
     * Kunci pertama selalu didapat; yang kedua untuk job yang sama ditolak.
     * Itulah seluruh gunanya: satu job unik tidak boleh mengantre dua kali.
     */
    public function testTheSecondAcquireForTheSameJobIsRefused() {
        $lock = $this->makeLock();
        $job = new QueueUniqueLockTestJob();

        $this->assertTrue($lock->acquire($job));
        $this->assertFalse($lock->acquire($job));
    }

    public function testReleasingLetsTheNextOneThrough() {
        $lock = $this->makeLock();
        $job = new QueueUniqueLockTestJob();

        $lock->acquire($job);
        $lock->release($job);

        $this->assertTrue($lock->acquire($job));
    }

    /**
     * Kuncinya mengandung nama kelas, jadi dua jenis job tidak saling
     * menghalangi walau tidak punya uniqueId.
     */
    public function testTwoDifferentJobClassesDoNotBlockEachOther() {
        $lock = $this->makeLock();

        $this->assertTrue($lock->acquire(new QueueUniqueLockTestJob()));
        $this->assertTrue($lock->acquire(new QueueUniqueLockTestOtherJob()));
    }

    /**
     * uniqueId membedakan dua contoh dari kelas yang sama -- misalnya satu job
     * per pesanan, bukan satu job untuk seluruh pesanan.
     */
    public function testUniqueIdSeparatesTwoInstancesOfTheSameClass() {
        $lock = $this->makeLock();

        $satu = new QueueUniqueLockTestJobWithProperty();
        $satu->uniqueId = 'pesanan-1';
        $dua = new QueueUniqueLockTestJobWithProperty();
        $dua->uniqueId = 'pesanan-2';
        $lagi = new QueueUniqueLockTestJobWithProperty();
        $lagi->uniqueId = 'pesanan-1';

        $this->assertTrue($lock->acquire($satu));
        $this->assertTrue($lock->acquire($dua));
        $this->assertFalse($lock->acquire($lagi));
    }

    /**
     * uniqueId boleh berupa properti maupun method; keduanya menghasilkan kunci
     * yang sama untuk nilai yang sama.
     */
    public function testUniqueIdWorksAsAMethodToo() {
        $lock = $this->makeLock();

        $satu = new QueueUniqueLockTestJobWithMethod();
        $satu->id = 'pesanan-1';
        $dua = new QueueUniqueLockTestJobWithMethod();
        $dua->id = 'pesanan-1';

        $this->assertTrue($lock->acquire($satu));
        $this->assertFalse($lock->acquire($dua));
    }

    public function testReleasingIsSafeEvenWhenNothingWasAcquired() {
        $lock = $this->makeLock();
        $lock->release(new QueueUniqueLockTestJob());

        $this->assertTrue($lock->acquire(new QueueUniqueLockTestJob()));
    }
}
