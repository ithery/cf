<?php
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ModelHasManyThroughTest extends TestCase {
    protected $builder;

    protected $related;

    protected $farParent;

    protected $throughParent;

    protected function tearDown() {
        m::close();
    }

    public function testConstraintsAreProperlyAdded() {
        $this->builder = m::mock(CModel_Query::class);
        $this->related = m::mock(CModel::class);
        $this->related->shouldReceive('getTable')->andReturn('posts');
        $this->related->shouldReceive('qualifyColumn')->andReturnUsing(function ($column) {
            return 'posts.' . $column;
        });
        $this->builder->shouldReceive('getModel')->andReturn($this->related);

        $this->throughParent = new ModelHasManyThroughUserStub();
        $this->throughParent->setTable('users');
        $this->farParent = new ModelHasManyThroughCountryStub();
        $this->farParent->setTable('countries');
        $this->farParent->setAttribute('id', 1);

        $this->builder->shouldReceive('join')->once()->with('users', 'users.id', '=', 'posts.user_id');
        $this->builder->shouldReceive('where')->once()->with('users.country_id', '=', 1);

        new CModel_Relation_HasManyThrough($this->builder, $this->farParent, $this->throughParent, 'country_id', 'user_id', 'id', 'id');

        $this->addToAssertionCount(1);
    }

    public function testEagerConstraintsAreProperlyAdded() {
        $relation = $this->getRelation();

        $relation->getQuery()->shouldReceive('whereIntegerInRaw')->once()->with('users.country_id', [1, 2]);

        $model1 = m::mock(CModel::class);
        $model1->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $model2 = m::mock(CModel::class);
        $model2->shouldReceive('getAttribute')->with('id')->andReturn(2);

        $relation->addEagerConstraints([$model1, $model2]);

        $this->addToAssertionCount(1);
    }

    public function testEagerConstraintsAreProperlyAddedWithNonIntKeyType() {
        $relation = $this->getRelation();
        $this->farParent->setKeyType('string');

        $relation->getQuery()->shouldReceive('whereIn')->once()->with('users.country_id', ['abc']);

        $model1 = m::mock(CModel::class);
        $model1->shouldReceive('getAttribute')->with('id')->andReturn('abc');

        $relation->addEagerConstraints([$model1]);

        $this->addToAssertionCount(1);
    }

    public function testInitRelationSetsEmptyCollectionOnEachModel() {
        $relation = $this->getRelation();
        $relation->getRelated()->shouldReceive('newCollection')->once()->andReturn($collection = new CModel_Collection());

        $model = m::mock(CModel::class);
        $model->shouldReceive('setRelation')->once()->with('foo', $collection);

        $models = $relation->initRelation([$model], 'foo');

        $this->assertEquals([$model], $models);
    }

    public function testModelsAreProperlyMatchedToParents() {
        $relation = $this->getRelation();
        $relation->getRelated()->shouldReceive('newCollection')->andReturnUsing(function ($items = []) {
            return new CModel_Collection($items);
        });

        $result1 = m::mock(stdClass::class);
        $result1->model_through_key = 1;
        $result2 = m::mock(stdClass::class);
        $result2->model_through_key = 1;
        $result3 = m::mock(stdClass::class);
        $result3->model_through_key = 2;

        $model1 = m::mock(CModel::class);
        $model1->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $model1->shouldReceive('setRelation')->once()->with('foo', m::on(function ($collection) use ($result1, $result2) {
            return $collection->all() === [$result1, $result2];
        }));

        $model2 = m::mock(CModel::class);
        $model2->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $model2->shouldReceive('setRelation')->once()->with('foo', m::on(function ($collection) use ($result3) {
            return $collection->all() === [$result3];
        }));

        $model3 = m::mock(CModel::class);
        $model3->shouldReceive('getAttribute')->with('id')->andReturn(3);
        $model3->shouldReceive('setRelation')->never();

        $models = $relation->match(
            [$model1, $model2, $model3],
            new CModel_Collection([$result1, $result2, $result3]),
            'foo'
        );

        $this->assertEquals([$model1, $model2, $model3], $models);
    }

    public function testGetResultsReturnsEmptyCollectionWhenLocalKeyIsNull() {
        $relation = $this->getRelation();
        $this->farParent->setAttribute('id', null);
        $relation->getRelated()->shouldReceive('newCollection')->once()->andReturn($empty = new CModel_Collection());

        $this->assertSame($empty, $relation->getResults());
    }

    public function testGetResultsReturnsCollectionOfHydratedModelsWhenLocalKeyIsNotNull() {
        $relation = $this->getRelation();

        $queryStub = new stdClass();
        $queryStub->columns = null;
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryStub);
        $relation->getQuery()->shouldReceive('applyScopes')->once()->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('addSelect')->once()->with(['posts.*', 'users.country_id as model_through_key'])->andReturn($relation->getQuery());

        $model = m::mock(CModel::class);
        $relation->getQuery()->shouldReceive('getModels')->once()->andReturn([$model]);
        $relation->getQuery()->shouldReceive('eagerLoadRelations')->once()->with([$model])->andReturn([$model]);
        $relation->getRelated()->shouldReceive('newCollection')->once()->with([$model])->andReturn($collection = new CModel_Collection([$model]));

        $this->assertSame($collection, $relation->getResults());
    }

    public function testGetReturnsEmptyCollectionWhenNoModelsFound() {
        $relation = $this->getRelation();

        $queryStub = new stdClass();
        $queryStub->columns = null;
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryStub);
        $relation->getQuery()->shouldReceive('applyScopes')->once()->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('addSelect')->once()->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('getModels')->once()->andReturn([]);
        $relation->getQuery()->shouldReceive('eagerLoadRelations')->never();
        $relation->getRelated()->shouldReceive('newCollection')->once()->with([])->andReturn($empty = new CModel_Collection());

        $this->assertSame($empty, $relation->get());
    }

    public function testFirstOrNewMethodFindsFirstModel() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('where')->once()->with(['foo'])->andReturn($relation->getQuery());
        $model = m::mock(CModel::class);
        $this->mockThroughFirst($relation, [$model]);

        $this->assertSame($model, $relation->firstOrNew(['foo']));
    }

    public function testFirstOrNewMethodReturnsNewModelWhenNotFound() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('where')->once()->with(['foo'])->andReturn($relation->getQuery());
        $this->mockThroughFirst($relation, []);
        $relation->getRelated()->shouldReceive('newInstance')->once()->with(['foo'])->andReturn($newModel = m::mock(CModel::class));

        $this->assertSame($newModel, $relation->firstOrNew(['foo']));
    }

    public function testUpdateOrCreateMethodUpdatesExistingModel() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('where')->once()->with(['foo'])->andReturn($relation->getQuery());
        $model = m::mock(CModel::class);
        $this->mockThroughFirst($relation, [$model]);
        $model->shouldReceive('fill')->once()->with(['bar'])->andReturnSelf();
        $model->shouldReceive('save')->once();

        $this->assertSame($model, $relation->updateOrCreate(['foo'], ['bar']));
    }

    public function testFindMethodFindsModelById() {
        $relation = $this->getRelation();
        $relation->getRelated()->shouldReceive('getQualifiedKeyName')->andReturn('posts.id');
        $relation->getQuery()->shouldReceive('where')->once()->with('posts.id', '=', 1)->andReturn($relation->getQuery());
        $model = m::mock(CModel::class);
        $this->mockThroughFirst($relation, [$model]);

        $this->assertSame($model, $relation->find(1));
    }

    public function testFindManyMethodReturnsEmptyCollectionForEmptyIds() {
        $relation = $this->getRelation();
        $relation->getRelated()->shouldReceive('newCollection')->once()->andReturn($empty = new CModel_Collection());

        $this->assertSame($empty, $relation->findMany([]));
    }

    public function testOneConvertsRelationshipToHasOneThrough() {
        $relation = $this->getRelation();
        $one = $relation->one();

        $this->assertInstanceOf(CModel_Relation_HasOneThrough::class, $one);
        $this->assertSame($relation->getQualifiedFirstKeyName(), $one->getQualifiedFirstKeyName());
        $this->assertSame($relation->getForeignKeyName(), $one->getForeignKeyName());
        $this->assertSame($relation->getLocalKeyName(), $one->getLocalKeyName());
        $this->assertSame($relation->getSecondLocalKeyName(), $one->getSecondLocalKeyName());
    }

    public function testThroughParentSoftDeletesReturnsFalseWhenTraitNotUsed() {
        $relation = $this->getRelation();

        $this->assertFalse($relation->throughParentSoftDeletes());
    }

    public function testWithTrashedParentsRemovesGlobalScopeAndReturnsSelf() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('withoutGlobalScope')->once()->with('SoftDeletableHasManyThrough');

        $this->assertSame($relation, $relation->withTrashedParents());
    }

    public function testQualifiedKeyNameGetters() {
        $relation = $this->getRelation();

        $this->assertSame('country_id', $relation->getFirstKeyName());
        $this->assertSame('users.country_id', $relation->getQualifiedFirstKeyName());
        $this->assertSame('user_id', $relation->getForeignKeyName());
        $this->assertSame('posts.user_id', $relation->getQualifiedForeignKeyName());
        $this->assertSame('posts.user_id', $relation->getQualifiedFarKeyName());
        $this->assertSame('id', $relation->getLocalKeyName());
        $this->assertSame('countries.id', $relation->getQualifiedLocalKeyName());
        $this->assertSame('id', $relation->getSecondLocalKeyName());
        $this->assertSame('users.id', $relation->getQualifiedParentKeyName());
    }

    public function testHasOneThroughGetResultsReturnsFirstResult() {
        $relation = $this->getOneRelation();
        $this->farParent->exists = true;

        $queryStub = new stdClass();
        $queryStub->columns = null;
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryStub);
        $relation->getQuery()->shouldReceive('applyScopes')->once()->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('addSelect')->once()->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('take')->once()->with(1)->andReturn($relation->getQuery());

        $model = m::mock(CModel::class);
        $relation->getQuery()->shouldReceive('getModels')->once()->andReturn([$model]);
        $relation->getQuery()->shouldReceive('eagerLoadRelations')->once()->with([$model])->andReturn([$model]);
        $relation->getRelated()->shouldReceive('newCollection')->once()->with([$model])->andReturn(new CModel_Collection([$model]));

        $this->assertSame($model, $relation->getResults());
    }

    public function testHasOneThroughGetResultsReturnsNullWithoutDefault() {
        $relation = $this->getOneRelation();
        $this->farParent->exists = true;

        $queryStub = new stdClass();
        $queryStub->columns = null;
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryStub);
        $relation->getQuery()->shouldReceive('applyScopes')->once()->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('addSelect')->once()->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('take')->once()->with(1)->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('getModels')->once()->andReturn([]);
        $relation->getQuery()->shouldReceive('eagerLoadRelations')->never();
        $relation->getRelated()->shouldReceive('newCollection')->once()->with([])->andReturn(new CModel_Collection());

        $this->assertNull($relation->getResults());
    }

    public function testHasOneThroughGetResultsReturnsDefaultModelWhenGivenWithDefault() {
        $relation = $this->getOneRelation()->withDefault();
        $this->farParent->exists = true;

        $queryStub = new stdClass();
        $queryStub->columns = null;
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryStub);
        $relation->getQuery()->shouldReceive('applyScopes')->once()->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('addSelect')->once()->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('take')->once()->with(1)->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('getModels')->once()->andReturn([]);
        $relation->getRelated()->shouldReceive('newCollection')->once()->with([])->andReturn(new CModel_Collection());

        $newModel = new ModelHasManyThroughPostStub();
        $relation->getRelated()->shouldReceive('newInstance')->once()->andReturn($newModel);

        $this->assertSame($newModel, $relation->getResults());
    }

    public function testHasOneThroughInitRelationSetsNullWithoutDefault() {
        $relation = $this->getOneRelation();
        $model = m::mock(CModel::class);
        $model->shouldReceive('setRelation')->once()->with('foo', null);

        $models = $relation->initRelation([$model], 'foo');

        $this->assertEquals([$model], $models);
    }

    public function testHasOneThroughModelsAreProperlyMatchedToParents() {
        $relation = $this->getOneRelation();

        $result1 = m::mock(stdClass::class);
        $result1->model_through_key = 1;
        $result2 = m::mock(stdClass::class);
        $result2->model_through_key = 2;

        $model1 = m::mock(CModel::class);
        $model1->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $model1->shouldReceive('setRelation')->once()->with('foo', $result1);

        $model2 = m::mock(CModel::class);
        $model2->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $model2->shouldReceive('setRelation')->once()->with('foo', $result2);

        $models = $relation->match([$model1, $model2], new CModel_Collection([$result1, $result2]), 'foo');

        $this->assertEquals([$model1, $model2], $models);
    }

    public function testHasOneThroughNewRelatedInstanceForDoesNotSetForeignKey() {
        $relation = $this->getOneRelation();
        $newModel = new ModelHasManyThroughPostStub();
        $relation->getRelated()->shouldReceive('newInstance')->once()->andReturn($newModel);

        $result = $relation->newRelatedInstanceFor($this->farParent);

        $this->assertSame($newModel, $result);
        $this->assertNull($result->user_id);
    }

    /**
     * Mock the take(1)->get() chain used internally by HasManyThrough::first().
     *
     * @param CModel_Relation_HasManyThrough $relation
     * @param array                          $models
     *
     * @return void
     */
    protected function mockThroughFirst($relation, array $models) {
        $queryStub = new stdClass();
        $queryStub->columns = null;
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryStub);
        $relation->getQuery()->shouldReceive('applyScopes')->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('addSelect')->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('take')->once()->with(1)->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('getModels')->once()->andReturn($models);

        if (count($models) > 0) {
            $relation->getQuery()->shouldReceive('eagerLoadRelations')->once()->with($models)->andReturn($models);
        }

        $relation->getRelated()->shouldReceive('newCollection')->once()->with($models)->andReturn(new CModel_Collection($models));
    }

    protected function getRelation() {
        $this->builder = m::mock(CModel_Query::class);
        $this->related = m::mock(CModel::class);
        $this->related->shouldReceive('getTable')->andReturn('posts');
        $this->related->shouldReceive('qualifyColumn')->andReturnUsing(function ($column) {
            return 'posts.' . $column;
        });
        $this->builder->shouldReceive('getModel')->andReturn($this->related);

        $this->throughParent = new ModelHasManyThroughUserStub();
        $this->throughParent->setTable('users');
        $this->farParent = new ModelHasManyThroughCountryStub();
        $this->farParent->setTable('countries');
        $this->farParent->setAttribute('id', 1);

        $this->builder->shouldReceive('join')->with('users', 'users.id', '=', 'posts.user_id');
        $this->builder->shouldReceive('where')->with('users.country_id', '=', 1);

        return new CModel_Relation_HasManyThrough($this->builder, $this->farParent, $this->throughParent, 'country_id', 'user_id', 'id', 'id');
    }

    protected function getOneRelation() {
        $this->builder = m::mock(CModel_Query::class);
        $this->related = m::mock(CModel::class);
        $this->related->shouldReceive('getTable')->andReturn('posts');
        $this->related->shouldReceive('qualifyColumn')->andReturnUsing(function ($column) {
            return 'posts.' . $column;
        });
        $this->builder->shouldReceive('getModel')->andReturn($this->related);

        $this->throughParent = new ModelHasManyThroughUserStub();
        $this->throughParent->setTable('users');
        $this->farParent = new ModelHasManyThroughCountryStub();
        $this->farParent->setTable('countries');
        $this->farParent->setAttribute('id', 1);

        $this->builder->shouldReceive('join')->with('users', 'users.id', '=', 'posts.user_id');
        $this->builder->shouldReceive('where')->with('users.country_id', '=', 1);

        return new CModel_Relation_HasOneThrough($this->builder, $this->farParent, $this->throughParent, 'country_id', 'user_id', 'id', 'id');
    }
}
// @codingStandardsIgnoreStart
class ModelHasManyThroughUserStub extends CModel {
}

class ModelHasManyThroughCountryStub extends CModel {
}

class ModelHasManyThroughPostStub extends CModel {
    public $user_id;
}
