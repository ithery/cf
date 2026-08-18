<?php
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ModelMorphToTest extends TestCase {
    protected $builder;

    protected $related;

    protected $parent;

    protected function tearDown() {
        m::close();
        ModelMorphToRelatedStubOne::$mockQuery = null;
        ModelMorphToRelatedStubTwo::$mockQuery = null;
        ModelMorphToModelStub::$relationStub = null;
    }

    public function testMorphToWithDefault() {
        $relation = $this->getRelation()->withDefault();
        // MorphTo::newRelatedInstanceFor() (unlike BelongsTo's) re-resolves the relation
        // via $parent->{$relationName}() to find the related model, since a MorphTo has
        // no single fixed related class. The stub parent forwards that call to $relation.
        ModelMorphToModelStub::$relationStub = $relation;

        $this->builder->shouldReceive('first')->once()->andReturnNull();

        $newModel = new ModelMorphToModelStub();

        $this->related->shouldReceive('newInstance')->once()->andReturn($newModel);

        $this->assertSame($newModel, $relation->getResults());
    }

    public function testMorphToWithDynamicDefault() {
        $relation = $this->getRelation()->withDefault(function ($newModel) {
            $newModel->username = 'taylor';
        });
        ModelMorphToModelStub::$relationStub = $relation;

        $this->builder->shouldReceive('first')->once()->andReturnNull();

        $newModel = new ModelMorphToModelStub();

        $this->related->shouldReceive('newInstance')->once()->andReturn($newModel);

        $this->assertSame($newModel, $relation->getResults());
        $this->assertSame('taylor', $newModel->username);
    }

    public function testMorphToWithArrayDefault() {
        $relation = $this->getRelation()->withDefault(['username' => 'taylor']);
        ModelMorphToModelStub::$relationStub = $relation;

        $this->builder->shouldReceive('first')->once()->andReturnNull();

        $newModel = new ModelMorphToModelStub();

        $this->related->shouldReceive('newInstance')->once()->andReturn($newModel);

        $this->assertSame($newModel, $relation->getResults());
        $this->assertSame('taylor', $newModel->username);
    }

    public function testEagerConstraintsBuildDictionaryGroupedByTypeThenForeignKey() {
        $relation = $this->getRelation();

        $model1 = new ModelMorphToModelStub();
        $model1->foreign_key = 1;
        $model1->morph_type = 'TypeA';

        $model2 = new ModelMorphToModelStub();
        $model2->foreign_key = 2;
        $model2->morph_type = 'TypeA';

        $model3 = new ModelMorphToModelStub();
        $model3->foreign_key = 3;
        $model3->morph_type = 'TypeB';

        $relation->addEagerConstraints([$model1, $model2, $model3]);

        $dictionary = $relation->getDictionary();

        $this->assertSame([$model1], $dictionary['TypeA'][1]);
        $this->assertSame([$model2], $dictionary['TypeA'][2]);
        $this->assertSame([$model3], $dictionary['TypeB'][3]);
    }

    public function testEagerConstraintsSkipModelsWithoutAMorphTypeValue() {
        $relation = $this->getRelation();

        $model1 = new ModelMorphToModelStub();
        $model1->foreign_key = 1;
        $model1->morph_type = null;

        $relation->addEagerConstraints([$model1]);

        $this->assertSame([], $relation->getDictionary());
    }

    public function testMatchReturnsModelsUnchanged() {
        $relation = $this->getRelation();
        $models = [m::mock(CModel::class), m::mock(CModel::class)];

        $result = $relation->match($models, new CModel_Collection(), 'relation');

        $this->assertSame($models, $result);
    }

    public function testAssociateMethodSetsForeignKeyAndMorphTypeOnModel() {
        $parent = m::mock(CModel::class);
        $parent->shouldReceive('getAttribute')->once()->with('foreign_key')->andReturn('foreign.value');
        $relation = $this->getRelation($parent);

        $associate = m::mock(CModel::class);
        $associate->shouldReceive('getKey')->once()->andReturn(1);
        $associate->shouldReceive('getMorphClass')->once()->andReturn('ModelMorphToAssociatedStub');

        $parent->shouldReceive('setAttribute')->once()->with('foreign_key', 1);
        $parent->shouldReceive('setAttribute')->once()->with('morph_type', 'ModelMorphToAssociatedStub');
        $parent->shouldReceive('setRelation')->once()->with('relation', $associate)->andReturn($parent);

        $result = $relation->associate($associate);

        $this->assertSame($parent, $result);
    }

    public function testAssociateMethodWithNonModelClearsForeignKeyAndMorphTypeButStillSetsRelation() {
        $parent = m::mock(CModel::class);
        $parent->shouldReceive('getAttribute')->once()->with('foreign_key')->andReturn('foreign.value');
        $relation = $this->getRelation($parent);

        $parent->shouldReceive('setAttribute')->once()->with('foreign_key', null);
        $parent->shouldReceive('setAttribute')->once()->with('morph_type', null);
        // Unlike BelongsTo::associate(), MorphTo::associate() always calls setRelation
        // (even for a non-CModel value) instead of unsetRelation().
        $parent->shouldReceive('setRelation')->once()->with('relation', null);

        $relation->associate(null);

        $this->addToAssertionCount(1);
    }

    public function testDissociateMethodUnsetsForeignKeyAndMorphType() {
        $parent = m::mock(CModel::class);
        $parent->shouldReceive('getAttribute')->once()->with('foreign_key')->andReturn('foreign.value');
        $relation = $this->getRelation($parent);

        $parent->shouldReceive('setAttribute')->once()->with('foreign_key', null);
        $parent->shouldReceive('setAttribute')->once()->with('morph_type', null);
        $parent->shouldReceive('setRelation')->once()->with('relation', null);

        $relation->dissociate();

        $this->addToAssertionCount(1);
    }

    public function testGetMorphTypeAccessor() {
        $relation = $this->getRelation();

        $this->assertSame('morph_type', $relation->getMorphType());
    }

    public function testCreateModelByTypeReturnsInstanceOfGivenClass() {
        $relation = $this->getRelation();

        $instance = $relation->createModelByType(ModelMorphToRelatedStubOne::class);

        $this->assertInstanceOf(ModelMorphToRelatedStubOne::class, $instance);
    }

    public function testCreateModelByTypeSetsConnectionWhenInstanceHasNone() {
        $this->builder = m::mock(CModel_Query::class);
        $this->builder->shouldReceive('where')->with('relation.id', '=', 'foreign.value');
        $connection = m::mock();
        $connection->shouldReceive('getName')->once()->andReturn('secondary');
        $this->builder->shouldReceive('getConnection')->once()->andReturn($connection);
        $this->related = m::mock(CModel::class);
        $this->related->shouldReceive('getTable')->andReturn('relation');
        $this->builder->shouldReceive('getModel')->andReturn($this->related);
        $this->parent = new ModelMorphToModelStub();

        $relation = new CModel_Relation_MorphTo($this->builder, $this->parent, 'foreign_key', 'id', 'morph_type', 'relation');

        $instance = $relation->createModelByType(ModelMorphToNoConnectionStub::class);

        $this->assertSame('secondary', $instance->getConnectionName());
    }

    public function testMorphWithMorphWithCountAndConstrainStoreConfigurationAndReturnSelf() {
        $relation = $this->getRelation();

        $this->assertSame($relation, $relation->morphWith([ModelMorphToRelatedStubOne::class => ['comments']]));
        $this->assertSame($relation, $relation->morphWithCount([ModelMorphToRelatedStubOne::class => ['comments']]));
        $this->assertSame($relation, $relation->constrain([ModelMorphToRelatedStubOne::class => function () {
        }]));

        $eagerLoads = $this->getProtectedProperty($relation, 'morphableEagerLoads');
        $eagerLoadCounts = $this->getProtectedProperty($relation, 'morphableEagerLoadCounts');
        $constraints = $this->getProtectedProperty($relation, 'morphableConstraints');

        $this->assertSame(['comments'], $eagerLoads[ModelMorphToRelatedStubOne::class]);
        $this->assertSame(['comments'], $eagerLoadCounts[ModelMorphToRelatedStubOne::class]);
        $this->assertInstanceOf(Closure::class, $constraints[ModelMorphToRelatedStubOne::class]);
    }

    public function testCallBuffersSelectMacroForLaterReplay() {
        $relation = $this->getRelation();
        $this->builder->shouldReceive('select')->once()->with(['foo'])->andReturnSelf();

        $result = $relation->select(['foo']);

        $this->assertSame($relation, $result);

        $macroBuffer = $this->getProtectedProperty($relation, 'macroBuffer');
        $this->assertCount(1, $macroBuffer);
        $this->assertSame('select', $macroBuffer[0]['method']);
        $this->assertSame([['foo']], $macroBuffer[0]['parameters']);
    }

    public function testGetEagerDispatchesQueriesPerMorphTypeAndMatchesResultsBackToParents() {
        $this->builder = m::mock(CModel_Query::class);
        $this->builder->shouldReceive('where')->with('relation.id', '=', 'foreign.value');
        $this->builder->shouldReceive('getEagerLoads')->andReturn([]);
        $this->related = m::mock(CModel::class);
        $this->related->shouldReceive('getTable')->andReturn('relation');
        $this->builder->shouldReceive('getModel')->andReturn($this->related);
        $this->parent = new ModelMorphToModelStub();

        $relation = new CModel_Relation_MorphTo($this->builder, $this->parent, 'foreign_key', 'id', 'morph_type', 'relation');

        $modelOne = new ModelMorphToModelStub();
        $modelOne->foreign_key = 1;
        $modelOne->morph_type = ModelMorphToRelatedStubOne::class;

        $modelTwo = new ModelMorphToModelStub();
        $modelTwo->foreign_key = 2;
        $modelTwo->morph_type = ModelMorphToRelatedStubTwo::class;

        $relation->addEagerConstraints([$modelOne, $modelTwo]);

        $resultOne = new ModelMorphToRelatedStubOne();
        $resultOne->id = 1;

        $resultTwo = new ModelMorphToRelatedStubTwo();
        $resultTwo->id = 2;

        $queryOne = m::mock(CModel_Query::class);
        $queryOne->shouldReceive('mergeConstraintsFrom')->once()->with($this->builder)->andReturnSelf();
        $queryOne->shouldReceive('with')->once()->with([])->andReturnSelf();
        $queryOne->shouldReceive('withCount')->once()->with([])->andReturnSelf();
        $queryOne->shouldReceive('whereIntegerInRaw')->once()->with('stub_one.id', [1])->andReturnSelf();
        $queryOne->shouldReceive('get')->once()->andReturn(new CModel_Collection([$resultOne]));
        ModelMorphToRelatedStubOne::$mockQuery = $queryOne;

        $queryTwo = m::mock(CModel_Query::class);
        $queryTwo->shouldReceive('mergeConstraintsFrom')->once()->with($this->builder)->andReturnSelf();
        $queryTwo->shouldReceive('with')->once()->with([])->andReturnSelf();
        $queryTwo->shouldReceive('withCount')->once()->with([])->andReturnSelf();
        $queryTwo->shouldReceive('whereIntegerInRaw')->once()->with('stub_two.id', [2])->andReturnSelf();
        $queryTwo->shouldReceive('get')->once()->andReturn(new CModel_Collection([$resultTwo]));
        ModelMorphToRelatedStubTwo::$mockQuery = $queryTwo;

        $relation->getEager();

        $this->assertSame($resultOne, $modelOne->getRelation('relation'));
        $this->assertSame($resultTwo, $modelTwo->getRelation('relation'));
    }

    protected function getRelation($parent = null) {
        $this->builder = m::mock(CModel_Query::class);
        $this->builder->shouldReceive('where')->with('relation.id', '=', 'foreign.value');
        $this->related = m::mock(CModel::class);
        $this->related->shouldReceive('getKeyType')->andReturn('int');
        $this->related->shouldReceive('getKeyName')->andReturn('id');
        $this->related->shouldReceive('getTable')->andReturn('relation');
        $this->builder->shouldReceive('getModel')->andReturn($this->related);
        $this->parent = $parent ?: new ModelMorphToModelStub();

        return new CModel_Relation_MorphTo($this->builder, $this->parent, 'foreign_key', 'id', 'morph_type', 'relation');
    }

    protected function getProtectedProperty($object, $property) {
        $reflection = new ReflectionProperty(get_class($object), $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }
}
// @codingStandardsIgnoreStart
class ModelMorphToModelStub extends CModel {
    public $foreign_key = 'foreign.value';

    public $morph_type = 'ModelMorphToModelStub';

    /**
     * Used to stand in for the real relation-definition method (e.g. `commentable()`)
     * that CModel_Relation_MorphTo::newRelatedInstanceFor() calls back into.
     *
     * @var CModel_Relation_MorphTo
     */
    public static $relationStub;

    public function relation() {
        return static::$relationStub;
    }
}

class ModelMorphToNoConnectionStub extends CModel {
}

class ModelMorphToRelatedStubOne extends CModel {
    protected $table = 'stub_one';

    protected $primaryKey = 'id';

    protected $connection = 'testing';

    public static $mockQuery;

    public function newQuery() {
        return static::$mockQuery;
    }
}

class ModelMorphToRelatedStubTwo extends CModel {
    protected $table = 'stub_two';

    protected $primaryKey = 'id';

    protected $connection = 'testing';

    public static $mockQuery;

    public function newQuery() {
        return static::$mockQuery;
    }
}
