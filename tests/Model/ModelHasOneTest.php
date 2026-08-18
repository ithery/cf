<?php
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ModelHasOneTest extends TestCase {
    protected $builder;

    protected $related;

    protected $parent;

    protected function tearDown() {
        m::close();
    }

    public function testConstraintsAreProperlyAdded() {
        $this->builder = m::mock(CModel_Query::class);
        $this->related = m::mock(CModel::class);
        $this->builder->shouldReceive('getModel')->andReturn($this->related);
        $this->parent = m::mock(CModel::class);
        $this->parent->shouldReceive('getAttribute')->once()->with('id')->andReturn(1);

        $this->builder->shouldReceive('where')->once()->with('foreign_key', '=', 1);
        $this->builder->shouldReceive('whereNotNull')->once()->with('foreign_key');

        new CModel_Relation_HasOne($this->builder, $this->parent, 'foreign_key', 'id');

        $this->addToAssertionCount(1);
    }

    public function testSaveMethodSetsForeignKeyOnModel() {
        $relation = $this->getRelation();
        $model = m::mock(CModel::class);
        $model->shouldReceive('setAttribute')->once()->with('foreign_key', 1);
        $model->shouldReceive('save')->once()->andReturn(true);

        $result = $relation->save($model);

        $this->assertSame($model, $result);
    }

    public function testCreateMethodProperlyCreatesNewModel() {
        $relation = $this->getRelation();
        $created = m::mock(CModel::class);
        $created->shouldReceive('setAttribute')->once()->with('foreign_key', 1);
        $created->shouldReceive('save')->once()->andReturn(true);
        $relation->getRelated()->shouldReceive('newInstance')->once()->with(['name' => 'taylor'])->andReturn($created);

        $this->assertSame($created, $relation->create(['name' => 'taylor']));
    }

    public function testMakeMethodDoesNotSaveNewModel() {
        $relation = $this->getRelation();
        $instance = m::mock(CModel::class);
        $instance->shouldReceive('setAttribute')->once()->with('foreign_key', 1);
        $instance->shouldReceive('save')->never();
        $relation->getRelated()->shouldReceive('newInstance')->once()->with(['name' => 'taylor'])->andReturn($instance);

        $this->assertSame($instance, $relation->make(['name' => 'taylor']));
    }

    public function testFindOrNewMethodFindsModel() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('find')->once()->with('foo', ['*'])->andReturn($model = m::mock(CModel::class));
        $model->shouldReceive('setAttribute')->never();

        $this->assertSame($model, $relation->findOrNew('foo'));
    }

    public function testFindOrNewMethodReturnsNewModel() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('find')->once()->with('foo', ['*'])->andReturnNull();
        $relation->getRelated()->shouldReceive('newInstance')->once()->andReturn($newModel = m::mock(CModel::class));
        $newModel->shouldReceive('setAttribute')->once()->with('foreign_key', 1);

        $this->assertSame($newModel, $relation->findOrNew('foo'));
    }

    public function testFirstOrNewMethodFindsFirstModel() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('where')->once()->with(['foo'])->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('first')->once()->andReturn($model = m::mock(CModel::class));
        $model->shouldReceive('setAttribute')->never();

        $this->assertSame($model, $relation->firstOrNew(['foo']));
    }

    public function testFirstOrNewMethodReturnsNewModel() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('where')->once()->with(['foo'])->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('first')->once()->andReturnNull();
        $relation->getRelated()->shouldReceive('newInstance')->once()->with(['foo'])->andReturn($newModel = m::mock(CModel::class));
        $newModel->shouldReceive('setAttribute')->once()->with('foreign_key', 1);

        $this->assertSame($newModel, $relation->firstOrNew(['foo']));
    }

    public function testFirstOrCreateMethodFindsFirstModel() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('where')->once()->with(['foo'])->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('first')->once()->andReturn($model = m::mock(CModel::class));
        $model->shouldReceive('save')->never();

        $this->assertSame($model, $relation->firstOrCreate(['foo']));
    }

    public function testFirstOrCreateMethodCreatesNewModel() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('where')->once()->with(['foo'])->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('first')->once()->andReturnNull();
        $relation->getRelated()->shouldReceive('newInstance')->once()->with(['foo'])->andReturn($created = m::mock(CModel::class));
        $created->shouldReceive('setAttribute')->once()->with('foreign_key', 1);
        $created->shouldReceive('save')->once()->andReturn(true);

        $this->assertSame($created, $relation->firstOrCreate(['foo']));
    }

    public function testUpdateOrCreateMethodFindsFirstModelAndUpdates() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('where')->once()->with(['foo'])->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('first')->once()->andReturn($model = m::mock(CModel::class));
        $model->shouldReceive('fill')->once()->with(['bar'])->andReturnSelf();
        $model->shouldReceive('save')->once();

        $this->assertSame($model, $relation->updateOrCreate(['foo'], ['bar']));
    }

    public function testUpdateOrCreateMethodCreatesNewModel() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('where')->once()->with(['foo'])->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('first')->once()->andReturnNull();
        $relation->getRelated()->shouldReceive('newInstance')->once()->with(['foo'])->andReturn($newModel = m::mock(CModel::class));
        $newModel->shouldReceive('setAttribute')->once()->with('foreign_key', 1);
        $newModel->shouldReceive('fill')->once()->with(['bar'])->andReturnSelf();
        $newModel->shouldReceive('save')->once();

        $this->assertSame($newModel, $relation->updateOrCreate(['foo'], ['bar']));
    }

    public function testRelationIsProperlyInitializedWithNoDefaultModel() {
        $relation = $this->getRelation();
        $model = m::mock(CModel::class);
        $model->shouldReceive('setRelation')->once()->with('foo', null);
        $models = $relation->initRelation([$model], 'foo');

        $this->assertEquals([$model], $models);
    }

    public function testEagerConstraintsAreProperlyAdded() {
        $relation = $this->getRelation();
        $relation->getParent()->shouldReceive('getKeyName')->andReturn('id');
        $relation->getParent()->shouldReceive('getKeyType')->andReturn('int');
        $relation->getQuery()->shouldReceive('whereIntegerInRaw')->once()->with('foreign_key', [1, 2]);

        $model1 = m::mock(CModel::class);
        $model1->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $model2 = m::mock(CModel::class);
        $model2->shouldReceive('getAttribute')->with('id')->andReturn(2);

        $relation->addEagerConstraints([$model1, $model2]);

        $this->addToAssertionCount(1);
    }

    public function testEagerConstraintsAreProperlyAddedWithNonIntKeyType() {
        $relation = $this->getRelation();
        $relation->getParent()->shouldReceive('getKeyName')->andReturn('id');
        $relation->getParent()->shouldReceive('getKeyType')->andReturn('string');
        $relation->getQuery()->shouldReceive('whereIn')->once()->with('foreign_key', ['abc']);

        $model1 = m::mock(CModel::class);
        $model1->shouldReceive('getAttribute')->with('id')->andReturn('abc');

        $relation->addEagerConstraints([$model1]);

        $this->addToAssertionCount(1);
    }

    public function testModelsAreProperlyMatchedToParents() {
        $relation = $this->getRelation();

        $result1 = m::mock(stdClass::class);
        $result1->foreign_key = 1;
        $result2 = m::mock(stdClass::class);
        $result2->foreign_key = 2;

        $model1 = m::mock(CModel::class);
        $model1->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $model1->shouldReceive('setRelation')->once()->with('foo', $result1);

        $model2 = m::mock(CModel::class);
        $model2->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $model2->shouldReceive('setRelation')->once()->with('foo', $result2);

        $model3 = m::mock(CModel::class);
        $model3->shouldReceive('getAttribute')->with('id')->andReturn(3);
        $model3->shouldReceive('setRelation')->never();

        $models = $relation->match([$model1, $model2, $model3], new CModel_Collection([$result1, $result2]), 'foo');

        $this->assertEquals([$model1, $model2, $model3], $models);
    }

    public function testGetResultsReturnsDefaultWhenParentKeyIsNull() {
        $relation = $this->getRelationForNullParentKey();

        $this->assertNull($relation->getResults());
    }

    public function testGetResultsReturnsFirstResult() {
        $relation = $this->getRelation();
        $model = m::mock(CModel::class);
        $relation->getQuery()->shouldReceive('first')->once()->andReturn($model);

        $this->assertSame($model, $relation->getResults());
    }

    public function testGetResultsReturnsDefaultModelWhenQueryReturnsNoResults() {
        $relation = $this->getRelation()->withDefault();
        $relation->getQuery()->shouldReceive('first')->once()->andReturnNull();
        $newModel = new ModelHasOneModelStub();
        $relation->getRelated()->shouldReceive('newInstance')->once()->andReturn($newModel);

        $this->assertSame($newModel, $relation->getResults());
    }

    public function testHasOneWithDefault() {
        $relation = $this->getRelation()->withDefault();
        $relation->getQuery()->shouldReceive('first')->once()->andReturnNull();
        $newModel = new ModelHasOneModelStub();
        $relation->getRelated()->shouldReceive('newInstance')->once()->andReturn($newModel);

        $this->assertSame($newModel, $relation->getResults());
    }

    public function testHasOneWithDynamicDefault() {
        $relation = $this->getRelation()->withDefault(function ($newModel) {
            $newModel->username = 'taylor';
        });
        $relation->getQuery()->shouldReceive('first')->once()->andReturnNull();
        $newModel = new ModelHasOneModelStub();
        $relation->getRelated()->shouldReceive('newInstance')->once()->andReturn($newModel);

        $this->assertSame($newModel, $relation->getResults());
        $this->assertSame('taylor', $newModel->username);
    }

    public function testHasOneWithArrayDefault() {
        $relation = $this->getRelation()->withDefault(['username' => 'taylor']);
        $relation->getQuery()->shouldReceive('first')->once()->andReturnNull();
        $newModel = new ModelHasOneModelStub();
        $relation->getRelated()->shouldReceive('newInstance')->once()->andReturn($newModel);

        $this->assertSame($newModel, $relation->getResults());
        $this->assertSame('taylor', $newModel->username);
    }

    protected function getRelation() {
        $this->builder = m::mock(CModel_Query::class);
        $this->builder->shouldReceive('where')->with('foreign_key', '=', 1);
        $this->builder->shouldReceive('whereNotNull')->with('foreign_key');
        $this->related = m::mock(CModel::class);
        $this->builder->shouldReceive('getModel')->andReturn($this->related);
        $this->parent = m::mock(CModel::class);
        $this->parent->shouldReceive('getAttribute')->with('id')->andReturn(1);

        return new CModel_Relation_HasOne($this->builder, $this->parent, 'foreign_key', 'id');
    }

    protected function getRelationForNullParentKey() {
        $this->builder = m::mock(CModel_Query::class);
        $this->builder->shouldReceive('where');
        $this->builder->shouldReceive('whereNotNull');
        $this->related = m::mock(CModel::class);
        $this->builder->shouldReceive('getModel')->andReturn($this->related);
        $this->parent = m::mock(CModel::class);
        $this->parent->shouldReceive('getAttribute')->with('id')->andReturnNull();

        return new CModel_Relation_HasOne($this->builder, $this->parent, 'foreign_key', 'id');
    }
}
// @codingStandardsIgnoreStart
class ModelHasOneModelStub extends CModel {
    public $foreign_key = 'foreign.value';
}
