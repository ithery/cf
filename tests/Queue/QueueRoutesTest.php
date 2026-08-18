<?php

use PHPUnit\Framework\TestCase;

interface QueueRoutesHeavyInterface {
}

trait QueueRoutesReportTrait {
}

class QueueRoutesBaseJob implements CQueue_ShouldQueueInterface {
    /**
     * @var null|string
     */
    public $connection;

    /**
     * @var null|string
     */
    public $queue;

    /**
     * @var null|int
     */
    public $delay;
}

class QueueRoutesChildJob extends QueueRoutesBaseJob {
}

class QueueRoutesHeavyJob extends QueueRoutesBaseJob implements QueueRoutesHeavyInterface {
}

class QueueRoutesReportJob extends QueueRoutesBaseJob {
    use QueueRoutesReportTrait;
}

class QueueRoutesFakeQueue implements CQueue_QueueInterface {
    /**
     * @var array
     */
    public $pushed = [];

    /**
     * @var null|string
     */
    public $connectionName;

    public function size($queue = null) {
        return 0;
    }

    public function push($job, $data = '', $queue = null) {
        $this->pushed[] = ['method' => 'push', 'queue' => $queue, 'job' => $job];
    }

    public function pushOn($queue, $job, $data = '') {
        $this->pushed[] = ['method' => 'pushOn', 'queue' => $queue, 'job' => $job];
    }

    public function pushRaw($payload, $queue = null, array $options = []) {
    }

    public function later($delay, $job, $data = '', $queue = null) {
        $this->pushed[] = ['method' => 'later', 'queue' => $queue, 'job' => $job];
    }

    public function laterOn($queue, $delay, $job, $data = '') {
        $this->pushed[] = ['method' => 'laterOn', 'queue' => $queue, 'job' => $job];
    }

    public function bulk($jobs, $data = '', $queue = null) {
    }

    public function pop($queue = null) {
        return null;
    }

    public function getConnectionName() {
        return $this->connectionName;
    }

    public function setConnectionName($name) {
        $this->connectionName = $name;

        return $this;
    }
}

class QueueRoutesTest extends TestCase {
    protected function setUp() {
        CQueue::routes()->flush();
    }

    protected function tearDown() {
        CQueue::routes()->flush();
    }

    public function testNoRoutesMeansNoRoute() {
        $this->assertNull(CQueue::routes()->getRoute(new QueueRoutesBaseJob()));
        $this->assertNull(CQueue::routes()->getQueue(new QueueRoutesBaseJob()));
        $this->assertNull(CQueue::routes()->getConnection(new QueueRoutesBaseJob()));
    }

    public function testAStringRouteIsTheQueueName() {
        CQueue::routes()->set(QueueRoutesBaseJob::class, 'billing');

        $this->assertSame('billing', CQueue::routes()->getQueue(new QueueRoutesBaseJob()));
        $this->assertNull(CQueue::routes()->getConnection(new QueueRoutesBaseJob()));
    }

    public function testARouteCanCarryBothConnectionAndQueue() {
        CQueue::routes()->set(QueueRoutesBaseJob::class, 'slow', 'redis');

        $this->assertSame('redis', CQueue::routes()->getConnection(new QueueRoutesBaseJob()));
        $this->assertSame('slow', CQueue::routes()->getQueue(new QueueRoutesBaseJob()));
    }

    public function testARouteCanBeGivenAsAClassName() {
        CQueue::routes()->set(QueueRoutesBaseJob::class, 'billing');

        $this->assertSame('billing', CQueue::routes()->getQueue(QueueRoutesBaseJob::class));
    }

    /**
     * The point of the class: one line routes a whole family.
     */
    public function testARouteOnTheParentCoversItsChildren() {
        CQueue::routes()->set(QueueRoutesBaseJob::class, 'billing');

        $this->assertSame('billing', CQueue::routes()->getQueue(new QueueRoutesChildJob()));
    }

    public function testARouteOnAnInterfaceCoversItsImplementors() {
        CQueue::routes()->set(QueueRoutesHeavyInterface::class, 'slow', 'redis');

        $this->assertSame('slow', CQueue::routes()->getQueue(new QueueRoutesHeavyJob()));
        $this->assertSame('redis', CQueue::routes()->getConnection(new QueueRoutesHeavyJob()));
    }

    public function testARouteOnATraitCoversItsUsers() {
        CQueue::routes()->set(QueueRoutesReportTrait::class, 'reporting');

        $this->assertSame('reporting', CQueue::routes()->getQueue(new QueueRoutesReportJob()));
    }

    public function testTheOwnClassWinsOverItsParent() {
        CQueue::routes()->set(QueueRoutesBaseJob::class, 'billing');
        CQueue::routes()->set(QueueRoutesChildJob::class, 'urgent');

        $this->assertSame('urgent', CQueue::routes()->getQueue(new QueueRoutesChildJob()));
        $this->assertSame('billing', CQueue::routes()->getQueue(new QueueRoutesBaseJob()));
    }

    public function testRoutesCanBeSetInBulk() {
        CQueue::routes()->set([
            QueueRoutesBaseJob::class => 'billing',
            QueueRoutesChildJob::class => ['redis', 'urgent'],
        ]);

        $this->assertSame('billing', CQueue::routes()->getQueue(new QueueRoutesBaseJob()));
        $this->assertNull(CQueue::routes()->getConnection(new QueueRoutesBaseJob()));
        $this->assertSame('urgent', CQueue::routes()->getQueue(new QueueRoutesChildJob()));
        $this->assertSame('redis', CQueue::routes()->getConnection(new QueueRoutesChildJob()));
        $this->assertCount(2, CQueue::routes()->all());
    }

    public function testAnUnknownClassNameDoesNotBlowUp() {
        CQueue::routes()->set(QueueRoutesBaseJob::class, 'billing');

        $this->assertNull(CQueue::routes()->getQueue('NoSuchJobClassAnywhere'));
    }

    /**
     * @return array a list as array(dispatcher, queue)
     */
    protected function makeDispatcher() {
        $fakeQueue = new QueueRoutesFakeQueue();
        $seenConnection = new stdClass();
        $seenConnection->value = 'not called';
        $dispatcher = new CQueue_Dispatcher(CContainer::getInstance(), function ($connection = null) use ($fakeQueue, $seenConnection) {
            $seenConnection->value = $connection;

            return $fakeQueue;
        });

        return [$dispatcher, $fakeQueue, $seenConnection];
    }

    /**
     * A route that never reaches the dispatcher is just dead configuration.
     */
    public function testTheDispatcherPushesToTheRoutedQueue() {
        CQueue::routes()->set(QueueRoutesBaseJob::class, 'billing', 'redis');
        list($dispatcher, $fakeQueue, $seenConnection) = $this->makeDispatcher();

        $dispatcher->dispatchToQueue(new QueueRoutesBaseJob());

        $this->assertSame('redis', $seenConnection->value, 'The routed connection never reached the resolver.');
        $this->assertCount(1, $fakeQueue->pushed);
        $this->assertSame('pushOn', $fakeQueue->pushed[0]['method']);
        $this->assertSame('billing', $fakeQueue->pushed[0]['queue']);
    }

    /**
     * What the job says about itself always wins; the route only fills a gap.
     */
    public function testTheJobsOwnQueueAndConnectionWinOverTheRoute() {
        CQueue::routes()->set(QueueRoutesBaseJob::class, 'billing', 'redis');
        list($dispatcher, $fakeQueue, $seenConnection) = $this->makeDispatcher();
        $job = new QueueRoutesBaseJob();
        $job->connection = 'sqs';
        $job->queue = 'urgent';

        $dispatcher->dispatchToQueue($job);

        $this->assertSame('sqs', $seenConnection->value);
        $this->assertSame('urgent', $fakeQueue->pushed[0]['queue']);
    }

    public function testWithoutAnyRouteTheDispatcherBehavesAsBefore() {
        list($dispatcher, $fakeQueue, $seenConnection) = $this->makeDispatcher();

        $dispatcher->dispatchToQueue(new QueueRoutesBaseJob());

        $this->assertNull($seenConnection->value);
        $this->assertSame('push', $fakeQueue->pushed[0]['method']);
        $this->assertNull($fakeQueue->pushed[0]['queue']);
    }

    public function testADelayedJobStillGoesToTheRoutedQueue() {
        CQueue::routes()->set(QueueRoutesBaseJob::class, 'billing');
        list($dispatcher, $fakeQueue) = $this->makeDispatcher();
        $job = new QueueRoutesBaseJob();
        $job->delay = 60;

        $dispatcher->dispatchToQueue($job);

        $this->assertSame('laterOn', $fakeQueue->pushed[0]['method']);
        $this->assertSame('billing', $fakeQueue->pushed[0]['queue']);
    }
}
