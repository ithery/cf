<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Tabel rute job: kelas ke pasangan (connection, queue).
 *
 * Gunanya memindahkan penempatan antrean keluar dari tiap job. Tanpa ini
 * penempatan tersebar di `onQueue()` di dalam job atau di tiap pemanggilan
 * dispatch, sehingga memindahkan satu keluarga job ke antrean lain berarti
 * menyunting semuanya satu per satu.
 *
 * Pencocokannya menelusuri kelas job **beserta induk, interface, dan trait**-nya,
 * jadi satu baris bisa merutekan sekeluarga job sekaligus:
 *
 *     CQueue::routes()->set(SendInvoiceJob::class, 'billing');
 *     CQueue::routes()->set(ShouldBeHeavyInterface::class, 'slow', 'redis');
 *
 * Yang tertulis di job sendiri tetap menang; rute ini hanya dipakai ketika job
 * tidak menentukan tujuannya.
 */
class CQueue_QueueRoutes {
    /**
     * Pemetaan nama kelas ke rute bawaannya.
     *
     * Nilainya berupa string nama antrean, atau list array(connection, queue).
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Connection tujuan sebuah job, atau null bila tidak dirutekan.
     *
     * @param object|string $queueable
     *
     * @return null|string
     */
    public function getConnection($queueable) {
        $route = $this->getRoute($queueable);
        if ($route === null || is_string($route)) {
            return null;
        }

        return carr::get($route, 0);
    }

    /**
     * Antrean tujuan sebuah job, atau null bila tidak dirutekan.
     *
     * @param object|string $queueable
     *
     * @return null|string
     */
    public function getQueue($queueable) {
        $route = $this->getRoute($queueable);
        if ($route === null) {
            return null;
        }

        return is_string($route) ? $route : carr::get($route, 1);
    }

    /**
     * Rute sebuah job, dicocokkan dengan kelasnya sendiri lebih dulu, baru
     * induk, interface, dan trait-nya.
     *
     * @param object|string $queueable
     *
     * @return null|array|string
     */
    public function getRoute($queueable) {
        if (count($this->routes) == 0) {
            return null;
        }
        $className = is_object($queueable) ? get_class($queueable) : $queueable;
        if (!is_string($className) || !class_exists($className) && !interface_exists($className)) {
            return null;
        }

        $candidateList = array_merge(
            [$className],
            array_values((array) class_parents($className)),
            array_values((array) class_implements($className)),
            array_values((array) c::classUsesRecursive($className))
        );
        foreach ($candidateList as $candidate) {
            if (isset($this->routes[$candidate])) {
                return $this->routes[$candidate];
            }
        }

        return null;
    }

    /**
     * Daftarkan rute untuk sebuah kelas, atau sekumpulan sekaligus.
     *
     * Bentuk sekumpulan menerima nama antrean saja atau pasangan lengkap:
     *
     *     ['SendInvoiceJob' => 'billing', 'SyncJob' => ['redis', 'slow']]
     *
     * @param array|string $class
     * @param null|string  $queue
     * @param null|string  $connection
     *
     * @return $this
     */
    public function set($class, $queue = null, $connection = null) {
        $routeList = is_array($class) ? $class : [$class => [$connection, $queue]];
        foreach ($routeList as $from => $to) {
            $this->routes[$from] = $to;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function all() {
        return $this->routes;
    }

    /**
     * Buang seluruh rute.
     *
     * Tidak ada di Laravel, tetapi CQueue::routes() sebuah singleton statis,
     * jadi test butuh cara mengembalikannya ke keadaan bersih.
     *
     * @return $this
     */
    public function flush() {
        $this->routes = [];

        return $this;
    }
}
