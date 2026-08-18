<?php
use TeamTNT\TNTSearch\TNTSearch;
use TeamTNT\TNTSearch\Exceptions\IndexNotFoundException;

trait CTrait_Controller_Application_Model_Scout {
    /**
     * @return array searchable model class names
     */
    protected function getSearchableModels() {
        return [];
    }

    /**
     * @return string
     */
    protected function getTitle() {
        return 'Scout Manager';
    }

    /**
     * Maximum rows the search tester renders.
     *
     * @return int
     */
    protected function getTesterLimit() {
        return 50;
    }

    public function index() {
        $app = c::app();
        $app->setTitle($this->getTitle());

        $searchableModels = $this->getSearchableModels();
        $tableData = [];
        $totalIndexed = 0;
        $totalRows = 0;
        $outOfSync = 0;

        foreach ($searchableModels as $class) {
            $status = $this->indexStatus($class);
            $totalIndexed += $status['rows_indexed'];
            $totalRows += $status['rows_total'];
            if ($status['difference'] != 0) {
                $outOfSync++;
            }
            $tableData[] = $status;
        }

        $this->addScoutSummary($app, [
            'driver' => (string) CF::config('model.scout.driver'),
            'storage' => $this->getIndexStorage(),
            'models' => count($searchableModels),
            'out_of_sync' => $outOfSync,
            'rows_indexed' => $totalIndexed,
            'rows_total' => $totalRows,
        ]);

        $widget = $app->addWidget()->addClass('mb-3')->setIcon('ti ti-list')->setTitle('Searchable Model');
        $table = $widget->addTable();
        $table->setDataFromArray($tableData);
        $table->setAjax(false);
        $table->setApplyDataTable(false);
        $table->setLabelNoData('Belum ada model yang didaftarkan pada getSearchableModels().');
        $table->addColumn('searchable')->setLabel('Searchable');
        $table->addColumn('index')->setLabel('Index');
        $table->addColumn('columns')->setLabel('Indexed Columns')->customCss('word-break', 'break-all');
        $table->addColumn('rows_total')->setLabel('DB Records');
        $table->addColumn('rows_indexed')->setLabel('Indexed Records');
        $table->addColumn('difference')->setLabel('Difference')->setCallback(function ($row) {
            $difference = carr::get($row, 'difference');
            if ($difference == 0) {
                return '<span class="badge badge-success">Synchronized</span>';
            }

            //selisih negatif berarti indeks memuat lebih banyak daripada yang
            //ada di basis data - baris terhapus yang belum dibersihkan
            $label = $difference > 0 ? '+' . $difference . ' belum terindeks' : $difference . ' yatim';

            return '<span class="badge badge-danger">' . $label . '</span>';
        });
        $table->addColumn('index_size')->setLabel('Index Size')->setCallback(function ($row) {
            $size = carr::get($row, 'index_size');

            return $size > 0 ? $this->formatIndexSize($size) : '-';
        });
        $table->addColumn('index_updated')->setLabel('Index Updated')->setCallback(function ($row) {
            return carr::get($row, 'index_updated') ?: '-';
        });
        $table->setRowActionStyle('btn-dropdown');
        $table->addRowAction()->setLabel('Test Search')->setIcon('ti ti-search')
            ->setLink($this->controllerUrl() . 'tester?model={searchable}');
        $table->addRowAction()->setLabel('Import')->setIcon('ti ti-reload')
            ->setLink($this->controllerUrl() . 'import/{searchable}')->setConfirm();
        $table->addRowAction()->setLabel('Flush')->setIcon('ti ti-trash')
            ->setLink($this->controllerUrl() . 'flush/{searchable}')->setConfirm();

        return $app;
    }

    /**
     * Search tester - runs a real query against the live index so a matching
     * problem can be reproduced here instead of in the storefront.
     *
     * @return CApp
     */
    public function tester() {
        $app = c::app();
        $app->setTitle($this->getTitle() . ' - Test Search');
        $app->addBreadcrumb($this->getTitle(), $this->controllerUrl());

        $request = c::request();
        $searchableModels = $this->getSearchableModels();
        $modelList = [];
        foreach ($searchableModels as $class) {
            $modelList[$class] = $class;
        }

        $model = (string) $request->query('model');
        if (!array_key_exists($model, $modelList)) {
            $model = (string) carr::first(array_keys($modelList));
        }
        $keyword = trim((string) $request->query('keyword', ''));
        $mode = $request->query('mode', 'all') == 'any' ? 'any' : 'all';
        $fuzzy = $request->query('fuzzy') ? true : false;

        $widget = $app->addWidget()->addClass('mb-3')->setIcon('ti ti-search')->setTitle('Test Search');
        $form = $widget->addForm()->setMethod('get');
        $row = $form->addDiv()->addClass('row');
        $row->addDiv()->addClass('col-md-4')->addField()->setLabel('Model')
            ->addSelectControl('model')->setValue($model)->setList($modelList);
        $row->addDiv()->addClass('col-md-4')->addField()->setLabel('Keyword')
            ->addTextControl('keyword')->setValue($keyword)->setPlaceholder('mis. minyak goreng');
        $row->addDiv()->addClass('col-md-2')->addField()->setLabel('Match')
            ->addSelectControl('mode')->setValue($mode)
            ->setList(['all' => 'Semua kata (AND)', 'any' => 'Salah satu kata (OR)']);
        $row->addDiv()->addClass('col-md-2')->addField()->setLabel('Typo Tolerance')
            ->addSelectControl('fuzzy')->setValue($fuzzy ? '1' : '')
            ->setList(['' => 'Tepat', '1' => 'Fuzzy']);
        $actions = $form->addActionList()->setStyle('form-action');
        $actions->addAction()->setLabel('Search')->setSubmit();

        if (strlen($keyword) == 0 || strlen($model) == 0) {
            return $app;
        }

        $result = $this->runTesterSearch($model, $keyword, $mode == 'all', $fuzzy);

        if (carr::get($result, 'error')) {
            $app->addAlert()->setTypeDanger()->add(c::e(carr::get($result, 'error')));

            return $app;
        }

        $ids = carr::get($result, 'ids', []);
        $app->addAlert()->setTypeInfo()->add(
            '<b>' . count($ids) . '</b> hasil dalam <b>' . carr::get($result, 'elapsed') . ' ms</b>'
            . (count($ids) > $this->getTesterLimit() ? ' (ditampilkan ' . $this->getTesterLimit() . ' teratas)' : '')
        );

        if (count($ids) == 0) {
            return $app;
        }

        $resultWidget = $app->addWidget()->setIcon('ti ti-list')->setTitle('Hasil');
        $resultTable = $resultWidget->addTable();
        $resultTable->setDataFromArray(carr::get($result, 'rows', []));
        $resultTable->setAjax(false);
        $resultTable->setApplyDataTable(false);
        foreach (carr::get($result, 'columns', []) as $column) {
            $resultTable->addColumn($column)->setLabel(cstr::humanize($column));
        }

        return $app;
    }

    /**
     * @param string $model
     *
     * @return CApp
     */
    public function import($model) {
        try {
            $model::makeAllSearchable();
            cmsg::add('success', 'Index ' . $model . ' berhasil dibangun ulang.');
        } catch (Throwable $ex) {
            cmsg::add('error', 'Gagal membangun index: ' . $ex->getMessage());
        }

        return c::redirect($this->controllerUrl());
    }

    /**
     * @param string $model
     *
     * @return CApp
     */
    public function flush($model) {
        try {
            $model::removeAllFromSearch();
            cmsg::add('success', 'Index ' . $model . ' berhasil dikosongkan.');
        } catch (Throwable $ex) {
            cmsg::add('error', 'Gagal mengosongkan index: ' . $ex->getMessage());
        }

        return c::redirect($this->controllerUrl());
    }

    /**
     * @param CApp $app
     *
     * @return void
     */
    protected function addScoutSummary($app, array $summary) {
        $widget = $app->addWidget()->addClass('mb-3')->setIcon('ti ti-server')->setTitle('Engine');
        $row = $widget->addDiv()->addClass('row');

        $items = [
            'Driver' => carr::get($summary, 'driver') ?: '-',
            'Model' => carr::get($summary, 'models'),
            'DB Records' => number_format(carr::get($summary, 'rows_total')),
            'Indexed Records' => number_format(carr::get($summary, 'rows_indexed')),
        ];
        foreach ($items as $label => $value) {
            $col = $row->addDiv()->addClass('col-md-3');
            $col->addDiv()->setAttr('style', 'font-size:0.7rem;font-weight:700;text-transform:uppercase;color:#94a3b8;')->add($label);
            $col->addDiv()->setAttr('style', 'font-size:1.1rem;font-weight:700;')->add(c::e((string) $value));
        }

        $outOfSync = carr::get($summary, 'out_of_sync');
        if ($outOfSync > 0) {
            $app->addAlert()->setTypeWarning()->add(
                '<b>' . $outOfSync . '</b> index tidak sinkron dengan basis data. Jalankan Import pada model yang bersangkutan.'
            );
        }

        $widget->addDiv()->addClass('mt-3')->setAttr('style', 'font-size:0.75rem;color:#94a3b8;word-break:break-all;')
            ->add('Storage: ' . c::e((string) carr::get($summary, 'storage')));
    }

    /**
     * @param string $class
     *
     * @return array
     */
    protected function indexStatus($class) {
        $model = new $class();
        $indexName = $model->searchableAs() . '.index';
        $rowsIndexed = 0;

        try {
            $tnt = $this->loadTNTEngine($model);
            $tnt->selectIndex($indexName);
            $rowsIndexed = $tnt->totalDocumentsInCollection();
        } catch (IndexNotFoundException $ex) {
            $rowsIndexed = 0;
        } catch (Throwable $ex) {
            //index tidak terbaca (mis. pdo_sqlite tidak ada) - dilaporkan
            //sebagai 0 supaya halamannya tetap terbuka dan masalahnya terlihat
            //sebagai selisih, bukan sebagai halaman galat
            $rowsIndexed = 0;
        }

        $rowsTotal = $model->count();
        $indexedColumns = '';
        $first = $rowsTotal ? $model->first() : null;
        if ($first != null && method_exists($first, 'toSearchableArray')) {
            $indexedColumns = implode(', ', array_keys($first->toSearchableArray()));
        }

        $indexFile = rtrim($this->getIndexStorage(), '/') . '/' . $indexName;

        return [
            'searchable' => $class,
            'index' => $indexName,
            'columns' => $indexedColumns,
            'rows_indexed' => $rowsIndexed,
            'rows_total' => $rowsTotal,
            'difference' => $rowsTotal - $rowsIndexed,
            'index_size' => is_file($indexFile) ? filesize($indexFile) : 0,
            'index_updated' => is_file($indexFile) ? date('Y-m-d H:i:s', filemtime($indexFile)) : null,
        ];
    }

    /**
     * Runs one search with explicit matching settings.
     *
     * TNTSearch reads searchBoolean/fuzziness off its own config at search
     * time, so they are set per attempt and restored afterwards.
     *
     * @param string $model
     * @param string $keyword
     * @param bool   $allWords
     * @param bool   $fuzzy
     *
     * @return array
     */
    protected function runTesterSearch($model, $keyword, $allWords, $fuzzy) {
        $config = c::config();
        $previousBoolean = $config->get('model.scout.tntsearch.searchBoolean');
        $previousFuzziness = $config->get('model.scout.tntsearch.fuzziness');
        $start = microtime(true);

        try {
            $config->set('model.scout.tntsearch.searchBoolean', $allWords);
            $config->set('model.scout.tntsearch.fuzziness', $fuzzy);

            $ids = $model::search($keyword)->keys()->all();
            $elapsed = round((microtime(true) - $start) * 1000, 1);

            $rows = [];
            $columns = [];
            foreach (array_slice($ids, 0, $this->getTesterLimit()) as $id) {
                $record = $model::find($id);
                if ($record == null) {
                    //id ada di index tetapi barisnya sudah tidak ada - justru
                    //temuan yang berguna, ditampilkan apa adanya
                    $rows[] = ['id' => $id, 'keterangan' => '(baris sudah tidak ada di basis data)'];

                    continue;
                }
                $searchable = $record->toSearchableArray();
                $columns = array_keys($searchable);
                $rows[] = $searchable;
            }
            if (count($columns) == 0) {
                $columns = ['id', 'keterangan'];
            }

            return ['ids' => $ids, 'rows' => $rows, 'columns' => $columns, 'elapsed' => $elapsed];
        } catch (Throwable $ex) {
            return ['error' => $ex->getMessage()];
        } finally {
            $config->set('model.scout.tntsearch.searchBoolean', $previousBoolean);
            $config->set('model.scout.tntsearch.fuzziness', $previousFuzziness);
        }
    }

    /**
     * @param int $bytes
     *
     * @return string
     */
    protected function formatIndexSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;
        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return round($bytes, $index > 1 ? 1 : 0) . ' ' . $units[$index];
    }

    /**
     * @return string
     */
    protected function getIndexStorage() {
        $storage = CF::config('model.scout.tntsearch.storage');
        if ($storage == null) {
            $storage = DOCROOT . 'temp/scout/tnt/' . CF::appCode() . '/';
        }

        return $storage;
    }

    /**
     * @param CModel $model
     *
     * @return TNTSearch
     */
    private function loadTNTEngine($model) {
        /** @var CModel_Scout_EngineManager $scoutManager */
        $scoutManager = c::container(CModel_Scout_EngineManager::class);

        return $scoutManager->createTntsearchEngine();
    }
}
