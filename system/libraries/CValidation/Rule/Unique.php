<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 12, 2019, 8:02:34 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class Unique {

    use DatabaseRule;

    /**
     * The ID that should be ignored.
     *
     * @var mixed
     */
    protected $ignore;

    /**
     * The name of the ID column.
     *
     * @var string
     */
    protected $idColumn = 'id';

    /**
     * Ignore the given ID during the unique check.
     *
     * @param  mixed  $id
     * @param  string|null  $idColumn
     * @return $this
     */
    public function ignore($id, $idColumn = null) {
        if ($id instanceof CModel) {
            return $this->ignoreModel($id, $idColumn);
        }

        $this->ignore = $id;
        $this->idColumn = $idColumn == null ? 'id' : $idColumn;

        return $this;
    }

    /**
     * Ignore the given model during the unique check.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string|null  $idColumn
     * @return $this
     */
    public function ignoreModel($model, $idColumn = null) {
        $this->idColumn = $idColumn == null ? $model->getKeyName() : $idColumn;
        $this->ignore = $model->{$this->idColumn};

        return $this;
    }

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     */
    public function __toString() {
        return rtrim(sprintf('unique:%s,%s,%s,%s,%s', $this->table, $this->column, $this->ignore ? '"' . $this->ignore . '"' : 'NULL', $this->idColumn, $this->formatWheres()
                ), ',');
    }

}
