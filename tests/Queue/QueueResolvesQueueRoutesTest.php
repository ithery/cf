<?php

use PHPUnit\Framework\TestCase;

class QueueResolvesQueueRoutesTestSubject {
    use CQueue_Trait_ResolvesQueueRoutesTrait;

    /**
     * @return CQueue_QueueRoutes
     */
    public function routes() {
        return $this->queueRoutes();
    }
}

class QueueResolvesQueueRoutesTestJob {
}

class QueueResolvesQueueRoutesTest extends TestCase {
    protected function setUp() {
        CQueue::routes()->flush();
        CContainer::getInstance()->forgetInstance('queue.routes');
    }

    protected function tearDown() {
        CQueue::routes()->flush();
        CContainer::getInstance()->forgetInstance('queue.routes');
    }

    /**
     * Tanpa ikatan khusus di kontainer, trait ini memakai tabel rute bersama
     * yang sama dengan CQueue::routes() -- kalau tidak, rute yang sudah
     * didaftarkan aplikasi menjadi tidak terlihat.
     */
    public function testItFallsBackToTheSharedRoutingTable() {
        $subject = new QueueResolvesQueueRoutesTestSubject();

        $this->assertSame(CQueue::routes(), $subject->routes());
    }

    public function testItReadsTheConnectionAndQueueOfARegisteredClass() {
        CQueue::routes()->set(QueueResolvesQueueRoutesTestJob::class, 'lambat', 'redis');
        $subject = new QueueResolvesQueueRoutesTestSubject();
        $job = new QueueResolvesQueueRoutesTestJob();

        $this->assertSame('redis', $subject->resolveConnectionFromQueueRoute($job));
        $this->assertSame('lambat', $subject->resolveQueueFromQueueRoute($job));
    }

    public function testAnUnroutedClassResolvesToNothing() {
        $subject = new QueueResolvesQueueRoutesTestSubject();
        $job = new QueueResolvesQueueRoutesTestJob();

        $this->assertNull($subject->resolveConnectionFromQueueRoute($job));
        $this->assertNull($subject->resolveQueueFromQueueRoute($job));
    }

    /**
     * Ikatan `queue.routes` di kontainer menang, dan itulah gunanya trait ini:
     * sebuah aplikasi atau test dapat menukar seluruh tabel rutenya tanpa
     * menyentuh singleton bersama.
     */
    public function testAContainerBindingTakesOver() {
        $sendiri = new CQueue_QueueRoutes();
        $sendiri->set(QueueResolvesQueueRoutesTestJob::class, 'sendiri', 'sqs');
        CContainer::getInstance()->instance('queue.routes', $sendiri);

        $subject = new QueueResolvesQueueRoutesTestSubject();
        $job = new QueueResolvesQueueRoutesTestJob();

        $this->assertSame($sendiri, $subject->routes());
        $this->assertSame('sqs', $subject->resolveConnectionFromQueueRoute($job));
        $this->assertSame('sendiri', $subject->resolveQueueFromQueueRoute($job));
    }

    public function testTheSharedTableIsIgnoredWhileTheBindingStands() {
        CQueue::routes()->set(QueueResolvesQueueRoutesTestJob::class, 'bersama', 'database');
        CContainer::getInstance()->instance('queue.routes', new CQueue_QueueRoutes());

        $subject = new QueueResolvesQueueRoutesTestSubject();

        $this->assertNull($subject->resolveQueueFromQueueRoute(new QueueResolvesQueueRoutesTestJob()));
    }

    /**
     * Manager memakai trait yang sama, sehingga pemanggil dapat menanyakan
     * tujuan sebuah job tanpa lebih dulu menyalurkannya.
     */
    public function testTheQueueManagerAnswersRouteQuestionsToo() {
        CQueue::routes()->set(QueueResolvesQueueRoutesTestJob::class, 'lambat', 'redis');
        $manager = new CQueue_Manager();
        $job = new QueueResolvesQueueRoutesTestJob();

        $this->assertSame('redis', $manager->resolveConnectionFromQueueRoute($job));
        $this->assertSame('lambat', $manager->resolveQueueFromQueueRoute($job));
    }
}
