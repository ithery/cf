<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 28, 2019, 9:34:34 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_Search_ModelSearchAspect extends CModel_Search_SearchAspect {

    /** @var CModel */
    protected $model;

    /** @var array */
    protected $attributes = [];

    public static function forModel(/* string $model, ...$attributes */) {
        $args = func_get_args();
        $model = $args[0];
        $attributes = array_slice($args, 1);
        return new self($model, $attributes);
    }

    /**
     * @param string $model
     * @param array|\Closure $attributes
     *
     */
    public function __construct($model, $attributes = []) {
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
        $this->attributes[] = SearchableAttribute::create($attribute, $partial);
        return $this;
    }

    public function addExactSearchableAttribute($attribute) {
        $this->attributes[] = SearchableAttribute::createExact($attribute);
        return $this;
    }

    public function getType() {
        $model = new $this->model();
        if (property_exists($model, 'searchableType')) {
            return $model->searchableType;
        }
        return $model->getTable();
    }

    public function getResults($term, $user = null) {
        if (empty($this->attributes)) {
            throw CModel_Search_Exception_InvalidModelSearchAspectException::noSearchableAttributes($this->model);
        }
        $query = call_user_func(array($this->model, 'query'));
        $this->addSearchConditions($query, $term);

        return $query->get();
    }

    protected function addSearchConditions(CModel_Query $query, $term) {
        $attributes = $this->attributes;
        $searchTerms = explode(' ', $term);
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
