<?php

use Mockery as m;
use PHPUnit\Framework\TestCase;

require_once dirname(__FILE__) . '/Support/WorkerFixture.php';

/**
 * Penjagaan atas pengecualian job yang gagal ketika antrean dijalankan daemon.
 *
 * `CQueue_Worker::runJob()` bercabang pada `CDaemon::getRunningService()`.
 * Cabang non-daemon memanggil `report()`, yang memeriksa kontrak DontReport dan
 * menyalakan collector. Cabang daemon dulu hanya menulis `CLogger::error` -
 * sehingga dua hal terjadi diam-diam: seluruh kegagalan antrean yang dijalankan
 * daemon tidak pernah terkumpul ke collector, dan pengecualian yang sengaja
 * ditandai "jangan dilaporkan" tetap tercatat sebagai ERROR.
 *
 * Yang kedua bukan soal kerapian: di tracking.bctech.co.id satu keadaan lumrah
 * mengisi 222 dari 226 baris ERROR dalam dua hari, sehingga galat yang
 * sesungguhnya tenggelam.
 */
class QueueDaemonExceptionTest extends TestCase {
    /**
     * @var mixed
     */
    protected $previousRunningService;

    protected function setUp() {
        $this->previousRunningService = $this->runningServiceProperty()->getValue();
    }

    protected function tearDown() {
        $this->setRunningService($this->previousRunningService);
        m::close();
    }

    /**
     * @return ReflectionProperty
     */
    protected function runningServiceProperty() {
        $property = new ReflectionProperty(CDaemon::class, 'runningService');
        $property->setAccessible(true);

        return $property;
    }

    /**
     * Daemon yang sedang berjalan hanya ada saat proses daemon hidup, jadi di
     * dalam test keadaannya dipasang lewat refleksi.
     *
     * @param mixed $service
     *
     * @return void
     */
    protected function setRunningService($service) {
        $this->runningServiceProperty()->setValue(null, $service);
    }

    /**
     * @return CQueue_WorkerOptions
     */
    protected function makeOptions() {
        return new CQueue_WorkerOptions('default', 0, 128, 60, 0, 1, false, false, 0, 0, 0);
    }

    /**
     * @param CException_ExceptionHandler $handler
     *
     * @return QueueWorkerTestWorker
     */
    protected function makeWorker($handler) {
        return new QueueWorkerTestWorker(
            new QueueWorkerFakeManager(new QueueWorkerFakeConnection([])),
            new CEvent_Dispatcher(),
            $handler,
            function () {
                return false;
            }
        );
    }

    /**
     * Menjalankan runJob() atas job yang pasti melempar.
     *
     * @param CException_ExceptionHandler $handler
     * @param Throwable                   $exception
     *
     * @return void
     */
    protected function runFailingJob($handler, $exception) {
        $job = new QueueWorkerFakeJob('job-1', [], function () use ($exception) {
            throw $exception;
        });

        $worker = $this->makeWorker($handler);
        $method = new ReflectionMethod(CQueue_Worker::class, 'runJob');
        $method->setAccessible(true);
        $method->invoke($worker, $job, 'test-connection', $this->makeOptions());
    }

    // --- cabang daemon ---

    /**
     * Di dalam daemon, keputusan mencatat harus melewati shouldReport() -
     * itulah yang menghormati kontrak DontReport.
     *
     * @return void
     */
    public function testDaemonBranchConsultsShouldReport() {
        $this->setRunningService(new QueueDaemonExceptionFakeService());

        $exception = new RuntimeException('meledak');
        $handler = m::mock(CException_ExceptionHandler::class);
        $handler->shouldIgnoreMissing();
        $handler->shouldReceive('shouldReport')->with($exception)->atLeast()->once()->andReturn(true);

        $this->runFailingJob($handler, $exception);

        $this->assertTrue(true, 'shouldReport() dipanggil pada cabang daemon');
    }

    /**
     * Pengecualian yang ditandai "jangan dilaporkan" tidak boleh menempuh jalur
     * pencatatan sama sekali di dalam daemon.
     *
     * @return void
     */
    public function testDaemonBranchSkipsExceptionThatShouldNotBeReported() {
        $this->setRunningService(new QueueDaemonExceptionFakeService());

        $exception = new QueueDaemonExceptionDontReport('lumrah, bukan kegagalan');
        $handler = m::mock(CException_ExceptionHandler::class);
        $handler->shouldIgnoreMissing();
        //keputusannya harus benar-benar ditanyakan pada handler, bukan
        //disimpulkan sendiri oleh worker
        $handler->shouldReceive('shouldReport')->with($exception)->atLeast()->once()->andReturn(false);
        $handler->shouldReceive('report')->never();

        $this->runFailingJob($handler, $exception);

        $this->assertTrue(true, 'pengecualian DontReport ditanyakan ke handler di cabang daemon');
    }

    /**
     * Pencatatan ERROR harus berada DI DALAM penjaga shouldReport().
     *
     * Tanpa pemeriksaan ini, penjaganya bisa saja dipanggil namun hasilnya
     * diabaikan - dan pengecualian DontReport kembali membanjiri log tanpa ada
     * test yang memerah. Diperiksa pada sumbernya karena `CLogger` statis dan
     * tidak dapat disela dari test.
     *
     * @return void
     */
    public function testDaemonErrorLogSitsInsideTheShouldReportGuard() {
        $source = $this->workerSource();
        $start = strpos($source, 'if (CDaemon::getRunningService() != null) {');
        $this->assertNotFalse($start, 'cabang daemon pada runJob() tidak ditemukan');

        $end = strpos($source, '} else {', $start);
        $branch = substr($source, $start, $end - $start);

        $guard = strpos($branch, 'shouldReport(');
        $log = strpos($branch, "CLogger::error('QueueException:");

        $this->assertNotFalse($guard, 'penjaga shouldReport() hilang dari cabang daemon');
        $this->assertNotFalse($log, 'pencatatan QueueException hilang dari cabang daemon');
        $this->assertTrue(
            $guard < $log,
            'CLogger::error berada di luar penjaga shouldReport() - pengecualian DontReport akan kembali tercatat sebagai ERROR'
        );
    }

    // --- cabang non-daemon, dijaga agar tidak ikut berubah ---

    /**
     * @return void
     */
    public function testNonDaemonBranchStillReports() {
        $this->setRunningService(null);

        $exception = new RuntimeException('meledak');
        $handler = m::mock(CException_ExceptionHandler::class);
        $handler->shouldIgnoreMissing();
        $handler->shouldReceive('report')->with($exception)->atLeast()->once();

        $this->runFailingJob($handler, $exception);

        $this->assertTrue(true, 'cabang non-daemon tetap memanggil report()');
    }

    // --- penjagaan atas kode sumbernya ---

    /**
     * @return string
     */
    protected function workerSource() {
        return (string) file_get_contents(dirname(__DIR__, 2) . '/system/libraries/CQueue/Worker.php');
    }

    /**
     * Cabang daemon harus benar-benar menyalakan collector. Tanpa ini
     * kegagalan job di daemon tidak terlihat di mana pun selain berkas log.
     *
     * @return void
     */
    public function testDaemonBranchNotifiesCollector() {
        $this->assertTrue(
            strpos($this->workerSource(), 'CDebug::collector()->collectException(') !== false,
            'cabang daemon tidak lagi memberitahu collector - kegagalan job di daemon akan kembali tak terlihat'
        );
    }

    /**
     * Collector sengaja dipanggil langsung, bukan lewat report(): report()
     * menulis lognya sendiri, sehingga satu kegagalan menghasilkan dua baris
     * ERROR sementara baris berkonteks yang sudah ada lebih berguna.
     *
     * @return void
     */
    public function testDaemonBranchDoesNotAlsoCallReportAndDoubleLog() {
        $source = $this->workerSource();
        $start = strpos($source, 'if (CDaemon::getRunningService() != null) {');
        $this->assertNotFalse($start, 'cabang daemon pada runJob() tidak ditemukan');

        $end = strpos($source, '} else {', $start);
        $this->assertNotFalse($end, 'penutup cabang daemon tidak ditemukan');

        $branch = substr($source, $start, $end - $start);
        $this->assertFalse(
            strpos($branch, '$this->exceptions->report(') !== false,
            'cabang daemon memanggil report() lagi - satu kegagalan akan tercatat dua kali sebagai ERROR'
        );
    }
}

/**
 * Cukup bukan-null: runJob() hanya memeriksa keberadaannya, dan `CDaemon::log()`
 * meneruskan ke `log()` milik layanan ini.
 */
class QueueDaemonExceptionFakeService {
    /**
     * @var string[]
     */
    public $logged = [];

    /**
     * @param string      $message
     * @param null|string $label
     *
     * @return void
     */
    public function log($message, $label = null) {
        $this->logged[] = $message;
    }
}

/**
 * Pengecualian yang menyatakan dirinya tidak perlu dilaporkan - bentuk yang
 * sama dipakai aplikasi lewat SFException_KnownException.
 */
class QueueDaemonExceptionDontReport extends Exception implements CException_Contract_DontReportInterface {
}
