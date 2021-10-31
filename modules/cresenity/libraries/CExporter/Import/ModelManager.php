<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Maatwebsite\Excel\Concerns\SkipsOnError;

use Throwable;

class ModelManager {

    /**
     * @var array
     */
    private $rows = [];

    /**
     * @var RowValidator
     */
    private $validator;

    /**
     * @param RowValidator $validator
     */
    public function __construct() {
        $this->validator = CExporter_Validator_RowValidator::instance();
    }

    /**
     * @param int   $row
     * @param array $attributes
     */
    public function add($row, array $attributes) {
        $this->rows[$row] = $attributes;
    }

    /**
     * @param ToModel $import
     * @param bool    $massInsert
     *
     * @throws ValidationException
     */
    public function flush(CExporter_Concern_ToModel $import, bool $massInsert = false) {
        if ($import instanceof CExporter_Concern_WithValidation) {
            $this->validateRows($import);
        }

        if ($massInsert) {
            $this->massFlush($import);
        } else {
            $this->singleFlush($import);
        }

        $this->rows = [];
    }

    /**
     * @param ToModel $import
     * @param array   $attributes
     *
     * @return Model[]|Collection
     */
    public function toModels(CExporter_Concern_ToModel $import, array $attributes) {
        $model = $import->model($attributes);

        if (null !== $model) {
            return \is_array($model) ? new CCollection($model) : new CCollection([$model]);
        }

        return new CCollection([]);
    }

    /**
     * @param ToModel $import
     */
    private function massFlush(CExporter_Concern_ToModel $import) {
        $this->rows()
                ->flatMap(function (array $attributes) use ($import) {
                    return $this->toModels($import, $attributes);
                })
                ->mapToGroups(function ($model) {
                    return [\get_class($model) => $this->prepare($model)->getAttributes()];
                })
                ->each(function (CCollection $models, string $model) use ($import) {
                    try {
                        /* @var Model $model */
                        $model::query()->insert($models->toArray());
                    } catch (Throwable $e) {
                        if ($import instanceof CExporter_Concern_SkipsOnError) {
                            $import->onError($e);
                        } else {
                            throw $e;
                        }
                    }
                });
    }

    /**
     * @param ToModel $import
     */
    private function singleFlush(CExporter_Concern_ToModel $import) {
        $this
                ->rows()
                ->each(function (array $attributes) use ($import) {
                    $this->toModels($import, $attributes)->each(function (Model $model) use ($import) {
                        try {
                            $model->saveOrFail();
                        } catch (Throwable $e) {
                            if ($import instanceof SkipsOnError) {
                                $import->onError($e);
                            } else {
                                throw $e;
                            }
                        }
                    });
                });
    }

    /**
     * @param Model $model
     *
     * @return Model
     */
    private function prepare(CModel $model) {
        if ($model->usesTimestamps()) {
            $time = $model->freshTimestamp();

            $updatedAtColumn = $model->getUpdatedAtColumn();

            // If model has updated at column and not manually provided.
            if ($updatedAtColumn && null === $model->{$updatedAtColumn}) {
                $model->setUpdatedAt($time);
            }

            $createdAtColumn = $model->getCreatedAtColumn();

            // If model has created at column and not manually provided.
            if ($createdAtColumn && null === $model->{$createdAtColumn}) {
                $model->setCreatedAt($time);
            }
        }

        return $model;
    }

    /**
     * @param CExporter_Concern_WithValidation $import
     *
     * @throws CExporter_Validator_ValidationException
     */
    private function validateRows(CExporter_Concern_WithValidation $import) {
        try {
            $this->validator->validate($this->rows, $import);
        } catch (CExporter_Exception_RowSkippedException $e) {
            foreach ($e->skippedRows() as $row) {
                unset($this->rows[$row]);
            }
        }
    }

    /**
     * @return CCollection
     */
    private function rows() {
        return new CCollection($this->rows);
    }

}
