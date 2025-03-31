<?php

use Illuminate\Support\Collection;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class CModel_Repository_BaseRepository.
 */
abstract class CModel_Repository_BaseRepository implements CModel_Repository_Contract_RepositoryInterface, CModel_Repository_Contract_RepositoryCriteriaInterface {
    /**
     * @var CModel
     */
    protected $model;

    /**
     * @var array
     */
    protected $fieldSearchable = [];

    /**
     * @var CModel_Repository_Contract_PresenterInterface
     */
    protected $presenter;

    /**
     * @var CModel_Validator_Contract_ValidatorInterface
     */
    protected $validator;

    /**
     * Validation Rules.
     *
     * @var array
     */
    protected $rules = null;

    /**
     * Collection of Criteria.
     *
     * @var CCollection
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * @var bool
     */
    protected $skipPresenter = false;

    /**
     * @var \Closure
     */
    protected $scopeQuery = null;

    public function __construct() {
        $this->criteria = new CCollection();
        $this->makeModel();
        $this->makePresenter();
        $this->makeValidator();
        $this->boot();
    }

    public function boot() {
    }

    /**
     * Returns the current Model instance.
     *
     * @return CModel
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * @throws CModel_Repository_Exception_RepositoryException
     */
    public function resetModel() {
        $this->makeModel();
    }

    /**
     * Specify Model class name.
     *
     * @return string
     */
    abstract public function model();

    /**
     * Specify Presenter class name.
     *
     * @return string
     */
    public function presenter() {
        return null;
    }

    /**
     * Specify Validator class name of Prettus\Validator\Contracts\ValidatorInterface.
     *
     * @throws Exception
     *
     * @return null
     */
    public function validator() {
        if (isset($this->rules) && !is_null($this->rules) && is_array($this->rules) && !empty($this->rules)) {
            $validator = new CModel_Validator_ModelValidator();
            if ($validator instanceof CModel_Validator_Contract_ValidatorInterface) {
                $validator->setRules($this->rules);

                return $validator;
            }
        }

        return null;
    }

    /**
     * Set Presenter.
     *
     * @param $presenter
     *
     * @return $this
     */
    public function setPresenter($presenter) {
        $this->makePresenter($presenter);

        return $this;
    }

    /**
     * @throws CModel_Repository_Exception_RepositoryException
     *
     * @return CModel
     */
    public function makeModel() {
        $model = CContainer::getInstance()->make($this->model());

        if (!$model instanceof CModel) {
            throw new CModel_Repository_Exception_RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * @param null $presenter
     *
     * @throws CModel_Repository_Exception_RepositoryException
     *
     * @return CModel_Repository_Contract_PresenterInterface
     */
    public function makePresenter($presenter = null) {
        $presenter = !is_null($presenter) ? $presenter : $this->presenter();

        if (!is_null($presenter)) {
            $this->presenter = is_string($presenter) ? CContainer::getInstance()->make($presenter) : $presenter;

            if (!$this->presenter instanceof CModel_Repository_Contract_PresenterInterface) {
                throw new CModel_Repository_Exception_RepositoryException("Class {$presenter} must be an instance of Prettus\\Repository\\Contracts\\PresenterInterface");
            }

            return $this->presenter;
        }

        return null;
    }

    /**
     * @param null $validator
     *
     * @throws CModel_Repository_Exception_RepositoryException
     *
     * @return null|CModel_Validator_Contract_ValidatorInterface
     */
    public function makeValidator($validator = null) {
        $validator = !is_null($validator) ? $validator : $this->validator();

        if (!is_null($validator)) {
            $this->validator = is_string($validator) ? CContainer::getInstance()->make($validator) : $validator;

            if (!$this->validator instanceof CModel_Validator_Contract_ValidatorInterface) {
                throw new CModel_Repository_Exception_RepositoryException("Class {$validator} must be an instance of Prettus\\Validator\\Contracts\\ValidatorInterface");
            }

            return $this->validator;
        }

        return null;
    }

    /**
     * Get Searchable Fields.
     *
     * @return array
     */
    public function getFieldsSearchable() {
        return $this->fieldSearchable;
    }

    /**
     * Query Scope.
     *
     * @param \Closure $scope
     *
     * @return $this
     */
    public function scopeQuery(Closure $scope) {
        $this->scopeQuery = $scope;

        return $this;
    }

    /**
     * Retrieve data array for populate field select.
     *
     * @param string      $column
     * @param null|string $key
     *
     * @return \CCollection|array
     */
    public function lists($column, $key = null) {
        $this->applyCriteria();

        return $this->model->lists($column, $key);
    }

    /**
     * Retrieve data array for populate field select
     * Compatible with Laravel 5.3.
     *
     * @param string      $column
     * @param null|string $key
     *
     * @return \CCollection|array
     */
    public function pluck($column, $key = null) {
        $this->applyCriteria();

        return $this->model->pluck($column, $key);
    }

    /**
     * Sync relations.
     *
     * @param      $id
     * @param      $relation
     * @param      $attributes
     * @param bool $detaching
     *
     * @return mixed
     */
    public function sync($id, $relation, $attributes, $detaching = true) {
        return $this->find($id)->{$relation}()->sync($attributes, $detaching);
    }

    /**
     * SyncWithoutDetaching.
     *
     * @param $id
     * @param $relation
     * @param $attributes
     *
     * @return mixed
     */
    public function syncWithoutDetaching($id, $relation, $attributes) {
        return $this->sync($id, $relation, $attributes, false);
    }

    /**
     * Retrieve all data of repository.
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*']) {
        $this->applyCriteria();
        $this->applyScope();

        if ($this->model instanceof CModel_Query) {
            $results = $this->model->get($columns);
        } else {
            $results = $this->model->all($columns);
        }

        $this->resetModel();
        $this->resetScope();

        return $this->parserResult($results);
    }

    /**
     * Count results of repository.
     *
     * @param array  $where
     * @param string $columns
     *
     * @return int
     */
    public function count(array $where = [], $columns = '*') {
        $this->applyCriteria();
        $this->applyScope();

        if ($where) {
            $this->applyConditions($where);
        }

        $result = $this->model->count($columns);

        $this->resetModel();
        $this->resetScope();

        return $result;
    }

    /**
     * Alias of All method.
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function get($columns = ['*']) {
        return $this->all($columns);
    }

    /**
     * Retrieve first data of repository.
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function first($columns = ['*']) {
        $this->applyCriteria();
        $this->applyScope();

        $results = $this->model->first($columns);

        $this->resetModel();

        return $this->parserResult($results);
    }

    /**
     * Retrieve first data of repository, or return new Entity.
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function firstOrNew(array $attributes = []) {
        $this->applyCriteria();
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = $this->model->firstOrNew($attributes);
        $this->skipPresenter($temporarySkipPresenter);

        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Retrieve first data of repository, or create new Entity.
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function firstOrCreate(array $attributes = []) {
        $this->applyCriteria();
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = $this->model->firstOrCreate($attributes);
        $this->skipPresenter($temporarySkipPresenter);

        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Retrieve data of repository with limit applied.
     *
     * @param int   $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function limit($limit, $columns = ['*']) {
        // Shortcut to all with `limit` applied on query via `take`
        $this->take($limit);

        return $this->all($columns);
    }

    /**
     * Retrieve all data of repository, paginated.
     *
     * @param null|int $limit
     * @param array    $columns
     * @param string   $method
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'], $method = 'paginate') {
        $this->applyCriteria();
        $this->applyScope();
        $limit = is_null($limit) ? CF::config('model.repository.pagination.limit', 15) : $limit;
        $results = $this->model->{$method}($limit, $columns);
        $results->appends(c::request()->query());
        $this->resetModel();

        return $this->parserResult($results);
    }

    /**
     * Retrieve all data of repository, simple paginated.
     *
     * @param null|int $limit
     * @param array    $columns
     *
     * @return mixed
     */
    public function simplePaginate($limit = null, $columns = ['*']) {
        return $this->paginate($limit, $columns, 'simplePaginate');
    }

    /**
     * Find data by id.
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*']) {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->findOrFail($id, $columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Find data by field and value.
     *
     * @param       $field
     * @param       $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findByField($field, $value = null, $columns = ['*']) {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->where($field, '=', $value)->get($columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Find data by multiple fields.
     *
     * @param array $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere(array $where, $columns = ['*']) {
        $this->applyCriteria();
        $this->applyScope();

        $this->applyConditions($where);

        $model = $this->model->get($columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Find data by multiple values in one field.
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhereIn($field, array $values, $columns = ['*']) {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->whereIn($field, $values)->get($columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Find data by excluding multiple values in one field.
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhereNotIn($field, array $values, $columns = ['*']) {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->whereNotIn($field, $values)->get($columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Find data by between values in one field.
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhereBetween($field, array $values, $columns = ['*']) {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->model->whereBetween($field, $values)->get($columns);
        $this->resetModel();

        return $this->parserResult($model);
    }

    /**
     * Save a new entity in repository.
     *
     * @param array $attributes
     *
     * @throws ValidatorException
     *
     * @return mixed
     */
    public function create(array $attributes) {
        if (!is_null($this->validator)) {
            // we should pass data that has been casts by the model
            // to make sure data type are same because validator may need to use
            // this data to compare with data that fetch from database.

            $attributes = $this->model->newInstance()->forceFill($attributes)->makeVisible($this->model->getHidden())->toArray();

            $this->validator->with($attributes)->passesOrFail(CModel_Validator_Contract_ValidatorInterface::RULE_CREATE);
        }

        c::event(new CModel_Repository_Event_RepositoryEntityCreating($this, $attributes));

        $model = $this->model->newInstance($attributes);
        $model->save();
        $this->resetModel();

        c::event(new CModel_Repository_Event_RepositoryEntityCreated($this, $model));

        return $this->parserResult($model);
    }

    /**
     * Update a entity in repository by id.
     *
     * @param array $attributes
     * @param       $id
     *
     * @throws ValidatorException
     *
     * @return mixed
     */
    public function update(array $attributes, $id) {
        $this->applyScope();

        if (!is_null($this->validator)) {
            // we should pass data that has been casts by the model
            // to make sure data type are same because validator may need to use
            // this data to compare with data that fetch from database.
            $model = $this->model->newInstance();
            $model->setRawAttributes([]);
            $model->setAppends([]);

            $attributes = $model->forceFill($attributes)->makeVisible($this->model->getHidden())->toArray();

            $this->validator->with($attributes)->setId($id)->passesOrFail(CModel_Validator_Contract_ValidatorInterface::RULE_UPDATE);
        }

        $temporarySkipPresenter = $this->skipPresenter;

        $this->skipPresenter(true);

        $model = $this->model->findOrFail($id);

        c::event(new CModel_Repository_Event_RepositoryEntityUpdating($this, $model));

        $model->fill($attributes);
        $model->save();

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        c::event(new CModel_Repository_Event_RepositoryEntityUpdated($this, $model));

        return $this->parserResult($model);
    }

    /**
     * Update or Create an entity in repository.
     *
     * @param array $attributes
     * @param array $values
     *
     * @throws ValidatorException
     *
     * @return mixed
     */
    public function updateOrCreate(array $attributes, array $values = []) {
        $this->applyScope();

        if (!is_null($this->validator)) {
            $this->validator->with(array_merge($attributes, $values))->passesOrFail(CModel_Validator_Contract_ValidatorInterface::RULE_CREATE);
        }

        $temporarySkipPresenter = $this->skipPresenter;

        $this->skipPresenter(true);

        c::event(new CModel_Repository_Event_RepositoryEntityCreating($this, $attributes));

        $model = $this->model->updateOrCreate($attributes, $values);

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        c::event(new CModel_Repository_Event_RepositoryEntityUpdated($this, $model));

        return $this->parserResult($model);
    }

    /**
     * Delete a entity in repository by id.
     *
     * @param $id
     *
     * @return int
     */
    public function delete($id) {
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = $this->find($id);
        $originalModel = clone $model;

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        c::event(new CModel_Repository_Event_RepositoryEntityDeleting($this, $model));

        $deleted = $model->delete();

        c::event(new CModel_Repository_Event_RepositoryEntityDeleted($this, $originalModel));

        return $deleted;
    }

    /**
     * Delete multiple entities by given criteria.
     *
     * @param array $where
     *
     * @return int
     */
    public function deleteWhere(array $where) {
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $this->applyConditions($where);

        c::event(new CModel_Repository_Event_RepositoryEntityDeleting($this, $this->model->getModel()));

        $deleted = $this->model->delete();

        c::event(new CModel_Repository_Event_RepositoryEntityDeleted($this, $this->model->getModel()));

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        return $deleted;
    }

    /**
     * Check if entity has relation.
     *
     * @param string $relation
     *
     * @return $this
     */
    public function has($relation) {
        $this->model = $this->model->has($relation);

        return $this;
    }

    /**
     * Load relations.
     *
     * @param array|string $relations
     *
     * @return $this
     */
    public function with($relations) {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * Add subselect queries to count the relations.
     *
     * @param mixed $relations
     *
     * @return $this
     */
    public function withCount($relations) {
        $this->model = $this->model->withCount($relations);

        return $this;
    }

    /**
     * Load relation with closure.
     *
     * @param string  $relation
     * @param closure $closure
     *
     * @return $this
     */
    public function whereHas($relation, $closure) {
        $this->model = $this->model->whereHas($relation, $closure);

        return $this;
    }

    /**
     * Set hidden fields.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function hidden(array $fields) {
        $this->model->setHidden($fields);

        return $this;
    }

    /**
     * Set the "orderBy" value of the query.
     *
     * @param mixed  $column
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($column, $direction = 'asc') {
        $this->model = $this->model->orderBy($column, $direction);

        return $this;
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function take($limit) {
        // Internally `take` is an alias to `limit`
        $this->model = $this->model->limit($limit);

        return $this;
    }

    /**
     * Set visible fields.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function visible(array $fields) {
        $this->model->setVisible($fields);

        return $this;
    }

    /**
     * Push Criteria for filter the query.
     *
     * @param $criteria
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return $this
     */
    public function pushCriteria($criteria) {
        if (is_string($criteria)) {
            $criteria = new $criteria();
        }
        if (!$criteria instanceof CModel_Repository_Contract_CriteriaInterface) {
            throw new CModel_Repository_Exception_RepositoryException('Class ' . get_class($criteria) . ' must be an instance of Prettus\\Repository\\Contracts\\CriteriaInterface');
        }
        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * Pop Criteria.
     *
     * @param $criteria
     *
     * @return $this
     */
    public function popCriteria($criteria) {
        $this->criteria = $this->criteria->reject(function ($item) use ($criteria) {
            if (is_object($item) && is_string($criteria)) {
                return get_class($item) === $criteria;
            }

            if (is_string($item) && is_object($criteria)) {
                return $item === get_class($criteria);
            }

            return get_class($item) === get_class($criteria);
        });

        return $this;
    }

    /**
     * Get Collection of Criteria.
     *
     * @return Collection
     */
    public function getCriteria() {
        return $this->criteria;
    }

    /**
     * Find data by Criteria.
     *
     * @param CModel_Repository_Contract_CriteriaInterface $criteria
     *
     * @return mixed
     */
    public function getByCriteria(CModel_Repository_Contract_CriteriaInterface $criteria) {
        $this->model = $criteria->apply($this->model, $this);
        $results = $this->model->get();
        $this->resetModel();

        return $this->parserResult($results);
    }

    /**
     * Skip Criteria.
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true) {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * Reset all Criterias.
     *
     * @return $this
     */
    public function resetCriteria() {
        $this->criteria = new CCollection();

        return $this;
    }

    /**
     * Reset Query Scope.
     *
     * @return $this
     */
    public function resetScope() {
        $this->scopeQuery = null;

        return $this;
    }

    /**
     * Apply scope in current Query.
     *
     * @return $this
     */
    protected function applyScope() {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback = $this->scopeQuery;
            $this->model = $callback($this->model);
        }

        return $this;
    }

    /**
     * Apply criteria in current Query.
     *
     * @return $this
     */
    protected function applyCriteria() {
        if ($this->skipCriteria === true) {
            return $this;
        }

        $criteria = $this->getCriteria();

        if ($criteria) {
            foreach ($criteria as $c) {
                if ($c instanceof CModel_Repository_Contract_CriteriaInterface) {
                    $this->model = $c->apply($this->model, $this);
                }
            }
        }

        return $this;
    }

    /**
     * Applies the given where conditions to the model.
     *
     * @param array $where
     *
     * @return void
     */
    protected function applyConditions(array $where) {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                //smooth input
                $condition = preg_replace('/\s\s+/', ' ', trim($condition));

                //split to get operator, syntax: "DATE >", "DATE =", "DAY <"
                $operator = explode(' ', $condition);
                if (count($operator) > 1) {
                    $condition = $operator[0];
                    $operator = $operator[1];
                } else {
                    $operator = null;
                }
                switch (strtoupper($condition)) {
                    case 'IN':
                        if (!is_array($val)) {
                            throw new CModel_Repository_Exception_RepositoryException("Input {$val} mus be an array");
                        }
                        $this->model = $this->model->whereIn($field, $val);

                        break;
                    case 'NOTIN':
                        if (!is_array($val)) {
                            throw new CModel_Repository_Exception_RepositoryException("Input {$val} mus be an array");
                        }
                        $this->model = $this->model->whereNotIn($field, $val);

                        break;
                    case 'DATE':
                        if (!$operator) {
                            $operator = '=';
                        }
                        $this->model = $this->model->whereDate($field, $operator, $val);

                        break;
                    case 'DAY':
                        if (!$operator) {
                            $operator = '=';
                        }
                        $this->model = $this->model->whereDay($field, $operator, $val);

                        break;
                    case 'MONTH':
                        if (!$operator) {
                            $operator = '=';
                        }
                        $this->model = $this->model->whereMonth($field, $operator, $val);

                        break;
                    case 'YEAR':
                        if (!$operator) {
                            $operator = '=';
                        }
                        $this->model = $this->model->whereYear($field, $operator, $val);

                        break;
                    case 'EXISTS':
                        if (!($val instanceof Closure)) {
                            throw new CModel_Repository_Exception_RepositoryException("Input {$val} must be closure function");
                        }
                        $this->model = $this->model->whereExists($val);

                        break;
                    case 'HAS':
                        if (!($val instanceof Closure)) {
                            throw new CModel_Repository_Exception_RepositoryException("Input {$val} must be closure function");
                        }
                        $this->model = $this->model->whereHas($field, $val);

                        break;
                    case 'HASMORPH':
                        if (!($val instanceof Closure)) {
                            throw new CModel_Repository_Exception_RepositoryException("Input {$val} must be closure function");
                        }
                        $this->model = $this->model->whereHasMorph($field, $val);

                        break;
                    case 'DOESNTHAVE':
                        if (!($val instanceof Closure)) {
                            throw new CModel_Repository_Exception_RepositoryException("Input {$val} must be closure function");
                        }
                        $this->model = $this->model->whereDoesntHave($field, $val);

                        break;
                    case 'DOESNTHAVEMORPH':
                        if (!($val instanceof Closure)) {
                            throw new CModel_Repository_Exception_RepositoryException("Input {$val} must be closure function");
                        }
                        $this->model = $this->model->whereDoesntHaveMorph($field, $val);

                        break;
                    case 'BETWEEN':
                        if (!is_array($val)) {
                            throw new CModel_Repository_Exception_RepositoryException("Input {$val} mus be an array");
                        }
                        $this->model = $this->model->whereBetween($field, $val);

                        break;
                    case 'BETWEENCOLUMNS':
                        if (!is_array($val)) {
                            throw new CModel_Repository_Exception_RepositoryException("Input {$val} mus be an array");
                        }
                        $this->model = $this->model->whereBetweenColumns($field, $val);

                        break;
                    case 'NOTBETWEEN':
                        if (!is_array($val)) {
                            throw new CModel_Repository_Exception_RepositoryException("Input {$val} mus be an array");
                        }
                        $this->model = $this->model->whereNotBetween($field, $val);

                        break;
                    case 'NOTBETWEENCOLUMNS':
                        if (!is_array($val)) {
                            throw new CModel_Repository_Exception_RepositoryException("Input {$val} mus be an array");
                        }
                        $this->model = $this->model->whereNotBetweenColumns($field, $val);

                        break;
                    case 'RAW':
                        $this->model = $this->model->whereRaw($val);

                        break;
                    default:
                        $this->model = $this->model->where($field, $condition, $val);
                }
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }

    /**
     * Skip Presenter Wrapper.
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipPresenter($status = true) {
        $this->skipPresenter = $status;

        return $this;
    }

    /**
     * Wrapper result data.
     *
     * @param mixed $result
     *
     * @return mixed
     */
    public function parserResult($result) {
        if ($this->presenter instanceof CModel_Repository_Contract_PresenterInterface) {
            if ($result instanceof CCollection || $result instanceof LengthAwarePaginator) {
                $result->each(function ($model) {
                    if ($model instanceof CModel_Repository_Contract_Presentable) {
                        $model->setPresenter($this->presenter);
                    }

                    return $model;
                });
            } elseif ($result instanceof CModel_Repository_Contract_Presentable) {
                $result = $result->setPresenter($this->presenter);
            }

            if (!$this->skipPresenter) {
                return $this->presenter->present($result);
            }
        }

        return $result;
    }

    /**
     * Trigger static method calls to the model.
     *
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    public static function __callStatic($method, $arguments) {
        return call_user_func_array([new static(), $method], $arguments);
    }

    /**
     * Trigger method calls to the model.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments) {
        $this->applyCriteria();
        $this->applyScope();

        return call_user_func_array([$this->model, $method], $arguments);
    }
}
