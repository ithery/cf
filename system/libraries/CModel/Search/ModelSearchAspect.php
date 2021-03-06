<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 28, 2019, 9:34:34 PM
 */
class CModel_Search_ModelSearchAspect extends CModel_Search_SearchAspect {
    /**
     * @var CModel
     */
    protected $model;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var array
     */
    protected $attributes = [];

    protected $explodeWord = false;

    public static function forModel(/* string $model, ...$attributes */) {
        $args = func_get_args();
        $model = $args[0];
        $attributes = array_slice($args, 1);
        return new self($model, $attributes);
    }

    /**
     * @param string         $model
     * @param array|\Closure $attributes
     * @param null|mixed     $callback
     */
    public function __construct($model, $attributes = [], $callback = null) {
        $this->callback = $callback;

        if (!is_subclass_of($model, CModel::class)) {
            throw CModel_Search_Exception_InvalidSearchableModelException::notAModel($model);
        }
        if (!is_subclass_of($model, CModel_SearchableInterface::class)) {
            throw CModel_Search_Exception_InvalidSearchableModelException::modelDoesNotImplementSearchable($model);
        }
        $this->model = $model;
        if (is_array($attributes)) {
            $this->attributes = CModel_Search_SearchableAttribute::createMany($attributes);
            return;
        }
        if (is_string($attributes)) {
            $this->attributes = CModel_Search_SearchableAttribute::create($attributes);
            return;
        }
        if (is_callable($attributes)) {
            $callable = $attributes;
            $callable($this);
            return;
        }
    }

    public function addSearchableAttribute($attribute, $partial = true) {
        $this->attributes[] = CModel_Search_SearchableAttribute::create($attribute, $partial);
        return $this;
    }

    public function addExactSearchableAttribute($attribute) {
        $this->attributes[] = CModel_Search_SearchableAttribute::createExact($attribute);
        return $this;
    }

    public function getType() {
        $model = new $this->model();
        if (property_exists($model, 'searchableType')) {
            return $model->searchableType;
        }
        return $model->getTable();
    }

    public function getResults($term, $user = null, $page = null, $perPage = null) {
        if (empty($this->attributes)) {
            throw CModel_Search_Exception_InvalidModelSearchAspectException::noSearchableAttributes($this->model);
        }
        $query = call_user_func([$this->model, 'query']);

        $this->addSearchConditions($query, $term);
        if (is_callable($this->callback)) {
            $query = call_user_func_array($this->callback, [$query]);
        }

        if ($page != null && $perPage != null) {
            return $query->paginate($perPage, ['*'], 'page', $page);
        }
        return $query->get();
    }

    protected function addSearchConditions(CModel_Query $query, $term) {
        $attributes = $this->attributes;
        $searchTerms = $term;
        if ($this->explodeWord) {
            $searchTerms = explode(' ', $term);
        }
        if (!is_array($searchTerms)) {
            $searchTerms = [$searchTerms];
        }
        $query->where(function (CModel_Query $query) use ($attributes, $term, $searchTerms) {
            foreach (carr::wrap($attributes) as $attribute) {
                foreach ($searchTerms as $searchTerm) {
                    $sql = "LOWER({$attribute->getAttribute()}) LIKE ?";
                    $searchTerm = mb_strtolower($searchTerm, 'UTF8');
                    $attribute->isPartial() ? $query->orWhereRaw($sql, ["%{$searchTerm}%"]) : $query->orWhere($attribute->getAttribute(), $searchTerm);
                }
            }
        });
    }
}
