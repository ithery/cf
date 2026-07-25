<?php
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ModelMorphOneTest extends TestCase {
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
        $this->parent->shouldReceive('getMorphClass')->andReturn('ModelMorphOneModelStub');
        $this->parent->shouldReceive('getAttribute')->once()->with('id')->andReturn(1);

        $this->builder->shouldReceive('where')->once()->with('morphable_type', 'ModelMorphOneModelStub');
        $this->builder->shouldReceive('where')->once()->with('morphable_id', '=', 1);
        $this->builder->shouldReceive('whereNotNull')->once()->with('morphable_id');

        new CModel_Relation_MorphOne($this->builder, $this->parent, 'morphable_type', 'morphable_id', 'id');

        $this->addToAssertionCount(1);
    }

    public function testEagerConstraintsAreProperlyAdded() {
        $relation = $this->getRelation();
        $relation->getParent()->shouldReceive('getKeyName')->andReturn('id');
        $relation->getParent()->shouldReceive('getKeyType')->andReturn('int');
        $relation->getQuery()->shouldReceive('whereIntegerInRaw')->once()->with('morphable_id', [1, 2]);

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
        $relation->getQuery()->shouldReceive('whereIn')->once()->with('morphable_id', ['abc']);

        $model1 = m::mock(CModel::class);
        $model1->shouldReceive('getAttribute')->with('id')->andReturn('abc');

        $relation->addEagerConstraints([$model1]);

        $this->addToAssertionCount(1);
    }

    public function testGetResultsReturnsNullWhenParentKeyIsNull() {
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
        $newModel = m::mock(CModel::class);
        $newModel->shouldReceive('setAttribute')->once()->with('morphable_id', 1)->andReturnSelf();
        $newModel->shouldReceive('setAttribute')->once()->with('morphable_type', 'ModelMorphOneModelStub')->andReturnSelf();
        $relation->getRelated()->shouldReceive('newInstance')->once()->andReturn($newModel);

        $this->assertSame($newModel, $relation->getResults());
    }

    public function testMorphOneWithDefault() {
        $relation = $this->getRelation()->withDefault();
        $relation->getQuery()->shouldReceive('first')->once()->andReturnNull();
        $newModel = new ModelMorphOneModelStub();
        $relation->getRelated()->shouldReceive('newInstance')->once()->andReturn($newModel);

        $this->assertSame($newModel, $relation->getResults());
    }

    public function testMorphOneWithDynamicDefault() {
        $relation = $this->getRelation()->withDefault(function ($newModel) {
            $newModel->username = 'taylor';
        });
        $relation->getQuery()->shouldReceive('first')->once()->andReturnNull();
        $newModel = new ModelMorphOneModelStub();
        $relation->getRelated()->shouldReceive('newInstance')->once()->andReturn($newModel);

        $this->assertSame($newModel, $relation->getResults());
        $this->assertSame('taylor', $newModel->username);
    }

    public function testMorphOneWithArrayDefault() {
        $relation = $this->getRelation()->withDefault(['username' => 'taylor']);
        $relation->getQuery()->shouldReceive('first')->once()->andReturnNull();
        $newModel = new ModelMorphOneModelStub();
        $relation->getRelated()->shouldReceive('newInstance')->once()->andReturn($newModel);

        $this->assertSame($newModel, $relation->getResults());
        $this->assertSame('taylor', $newModel->username);
    }

    public function testRelationIsProperlyInitializedWithNoDefaultModel() {
        $relation = $this->getRelation();
        $model = m::mock(CModel::class);
        $model->shouldReceive('setRelation')->once()->with('foo', null);
        $models = $relation->initRelation([$model], 'foo');

        $this->assertEquals([$model], $models);
    }

    public function testModelsAreProperlyMatchedToParents() {
        $relation = $this->getRelation();

        $result1 = m::mock(stdClass::class);
        $result1->morphable_id = 1;
        $result2 = m::mock(stdClass::class);
        $result2->morphable_id = 2;

        $model1 = m::mock(CModel::class);
        $model1->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $model1->shouldReceive('setRelation')->once()->with('foo', $result1);

        $model2 = m::mock(CModel::class);
        $model2->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $model2->shouldReceive('setRelation')->once()->with('foo', $result2);

        $model3 = m::mock(CModel::class);
        $model3->shouldReceive('getAttribute')->with('id')->andReturn(3);
        $model3->shouldReceive('setRelation')->never();

        $models = $relation->match(
            [$model1, $model2, $model3],
            new CModel_Collection([$result1, $result2]),
            'foo'
        );

        $this->assertEquals([$model1, $model2, $model3], $models);
    }

    public function testSaveMethodSetsForeignKeyAndMorphTypeOnModel() {
        $relation = $this->getRelation();
        $model = m::mock(CModel::class);
        $model->shouldReceive('setAttribute')->once()->with('morphable_id', 1);
        $model->shouldReceive('setAttribute')->once()->with('morphable_type', 'ModelMorphOneModelStub');
        $model->shouldReceive('save')->once()->andReturn(true);

        $result = $relation->save($model);

        $this->assertSame($model, $result);
    }

    public function testCreateMethodProperlyCreatesNewMorphModel() {
        $relation = $this->getRelation();
        $created = m::mock(CModel::class);
        $created->shouldReceive('setAttribute')->once()->with('morphable_id', 1);
        $created->shouldReceive('setAttribute')->once()->with('morphable_type', 'ModelMorphOneModelStub');
        $created->shouldReceive('save')->once()->andReturn(true);
        $relation->getRelated()->shouldReceive('newInstance')->once()->with(['name' => 'taylor'])->andReturn($created);

        $this->assertSame($created, $relation->create(['name' => 'taylor']));
    }

    public function testMakeMethodDoesNotSaveNewModel() {
        $relation = $this->getRelation();
        $instance = m::mock(CModel::class);
        $instance->shouldReceive('setAttribute')->once()->with('morphable_id', 1);
        $instance->shouldReceive('setAttribute')->once()->with('morphable_type', 'ModelMorphOneModelStub');
        $instance->shouldReceive('save')->never();
        $relation->getRelated()->shouldReceive('newInstance')->once()->with(['name' => 'taylor'])->andReturn($instance);

        $this->assertSame($instance, $relation->make(['name' => 'taylor']));
    }

    public function testForceCreateSetsForeignKeyAndMorphTypeAndCallsRelatedForceCreate() {
        $relation = $this->getRelation();
        $created = m::mock(CModel::class);
        $relation->getRelated()->shouldReceive('forceCreate')->once()->with([
            'name' => 'taylor',
            'morphable_id' => 1,
            'morphable_type' => 'ModelMorphOneModelStub',
        ])->andReturn($created);

        $this->assertSame($created, $relation->forceCreate(['name' => 'taylor']));
    }

    public function testNewRelatedInstanceForSetsForeignKeyAndMorphType() {
        $relation = $this->getRelation();
        $newModel = m::mock(CModel::class);
        $newModel->shouldReceive('setAttribute')->once()->with('morphable_id', 'parent-local-value')->andReturnSelf();
        $newModel->shouldReceive('setAttribute')->once()->with('morphable_type', 'ModelMorphOneModelStub')->andReturnSelf();
        $relation->getRelated()->shouldReceive('newInstance')->once()->andReturn($newModel);

        $someParent = m::mock(CModel::class);
        $someParent->shouldReceive('getAttribute')->once()->with('id')->andReturn('parent-local-value');

        $result = $relation->newRelatedInstanceFor($someParent);

        $this->assertSame($newModel, $result);
    }

    public function testGetMorphTypeAndMorphClassAccessors() {
        $relation = $this->getRelation();

        $this->assertSame('morphable_type', $relation->getMorphType());
        $this->assertSame('morphable_type', $relation->getQualifiedMorphType());
        $this->assertSame('ModelMorphOneModelStub', $relation->getMorphClass());
    }

    protected function getRelation($parent = null) {
        $this->builder = m::mock(CModel_Query::class);
        $this->builder->shouldReceive('where')->with('morphable_type', 'ModelMorphOneModelStub');
        $this->builder->shouldReceive('where')->with('morphable_id', '=', 1);
        $this->builder->shouldReceive('whereNotNull')->with('morphable_id');
        $this->related = m::mock(CModel::class);
        $this->builder->shouldReceive('getModel')->andReturn($this->related);
        $this->parent = $parent ?: m::mock(CModel::class);

        if (!$parent) {
            $this->parent->shouldReceive('getMorphClass')->andReturn('ModelMorphOneModelStub');
            $this->parent->shouldReceive('getAttribute')->with('id')->andReturn(1);
        }

        return new CModel_Relation_MorphOne($this->builder, $this->parent, 'morphable_type', 'morphable_id', 'id');
    }

    protected function getRelationForNullParentKey() {
        $this->builder = m::mock(CModel_Query::class);
        $this->builder->shouldReceive('where');
        $this->builder->shouldReceive('whereNotNull');
        $this->related = m::mock(CModel::class);
        $this->builder->shouldReceive('getModel')->andReturn($this->related);
        $this->parent = m::mock(CModel::class);
        $this->parent->shouldReceive('getMorphClass')->andReturn('ModelMorphOneModelStub');
        $this->parent->shouldReceive('getAttribute')->with('id')->andReturnNull();

        return new CModel_Relation_MorphOne($this->builder, $this->parent, 'morphable_type', 'morphable_id', 'id');
    }
}
// @codingStandardsIgnoreStart
class ModelMorphOneModelStub extends CModel {
    public $morphable_id = 'foreign.value';
}
