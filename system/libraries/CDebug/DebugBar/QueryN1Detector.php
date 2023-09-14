<?php
use DebugBar\DataCollector\MessagesCollector;

/**
 * Collects data about SQL statements executed with PDO.
 */
class CDebug_DebugBar_QueryN1Detector extends MessagesCollector {
    /**
     * @var CCollection
     */
    private $queries;

    public function __construct() {
        $this->queries = CCollection::make();
    }

    public function logQuery($query, CCollection $backtrace, $sources) {
        $sources = array_values($sources);
        $modelTrace = $backtrace->first(function ($trace) {
            return carr::get($trace, 'object') instanceof CModel_Query;
        });

        // The query is coming from an Eloquent model
        if (!is_null($modelTrace)) {
            /*
             * Relations get resolved by either calling the "getRelationValue" method on the model,
             * or if the class itself is a Relation.
             */
            $relation = $backtrace->first(function ($trace) {
                return carr::get($trace, 'function') === 'getRelationValue' || carr::get($trace, 'class') === CModel_Relation::class;
            });

            // We try to access a relation
            if (is_array($relation) && isset($relation['object'])) {
                if ($relation['class'] === CModel_Relation::class) {
                    $model = get_class($relation['object']->getParent());
                    $relationName = get_class($relation['object']->getRelated());
                    $relatedModel = $relationName;
                } else {
                    $model = get_class($relation['object']);
                    $relationName = $relation['args'][0];
                    $relatedModel = $relationName;
                }

                $key = md5($query->sql . $model . $relationName . $sources[0]->name . $sources[0]->line);

                $count = carr::get($this->queries, $key . '.count', 0);
                $time = carr::get($this->queries, $key . '.time', 0);
                $detectedQuery = [
                    'count' => ++$count,
                    'time' => $time + $query->time,
                    'query' => $query->sql,
                    'model' => $model,
                    'relatedModel' => $relatedModel,
                    'relation' => $relationName,
                ];
                $this->queries[$key] = $detectedQuery;

                if ($count > 1) {
                    return sprintf(
                        'Model: %s => Relation: %s - You should add `with(%s)` to eager-load this relation.',
                        $detectedQuery['model'],
                        $detectedQuery['relation'],
                        $detectedQuery['relation']
                    );
                }

                return null;
            }
        }
    }
}
