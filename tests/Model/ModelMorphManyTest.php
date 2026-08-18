<?php
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ModelMorphManyTest extends TestCase {
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
        $this->parent->shouldReceive('getMorphClass')->andReturn('ModelMorphManyModelStub');
        $this->parent->shouldReceive('getAttribute')->once()->with('id')->andReturn(1);

        $this->builder->shouldReceive('where')->once()->with('morphable_type', 'ModelMorphManyModelStub');
        $this->builder->shouldReceive('where')->once()->with('morphable_id', '=', 1);
        $this->builder->shouldReceive('whereNotNull')->once()->with('morphable_id');

        new CModel_Relation_MorphMany($this->builder, $this->parent, 'morphable_type', 'morphable_id', 'id');

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

    public function testGetResultsReturnsEmptyCollectionWhenParentKeyIsNull() {
        $relation = $this->getRelationForNullParentKey();
        $relation->getRelated()->shouldReceive('newCollection')->once()->andReturn($empty = new CModel_Collection());

        $this->assertSame($empty, $relation->getResults());
    }

    public function testGetResultsReturnsQueryResultsWhenParentKeyIsNotNull() {
        $relation = $this->getRelation();
        $results = new CModel_Collection(['foo']);
        $relation->getQuery()->shouldReceive('get')->once()->andReturn($results);

        $this->assertSame($results, $relation->getResults());
    }

    public function testRelationIsProperlyInitializedWithEmptyCollection() {
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
        $result1->morphable_id = 1;
        $result2 = m::mock(stdClass::class);
        $result2->morphable_id = 1;
        $result3 = m::mock(stdClass::class);
        $result3->morphable_id = 2;

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

    public function testSaveMethodSetsForeignKeyAndMorphTypeOnModel() {
        $relation = $this->getRelation();
        $model = m::mock(CModel::class);
        $model->shouldReceive('setAttribute')->once()->with('morphable_id', 1);
        $model->shouldReceive('setAttribute')->once()->with('morphable_type', 'ModelMorphManyModelStub');
        $model->shouldReceive('save')->once()->andReturn(true);

        $result = $relation->save($model);

        $this->assertSame($model, $result);
    }

    public function testSaveManyMethodSetsForeignKeyAndMorphTypeOnEachModel() {
        $relation = $this->getRelation();

        $model1 = m::mock(CModel::class);
        $model1->shouldReceive('setAttribute')->once()->with('morphable_id', 1);
        $model1->shouldReceive('setAttribute')->once()->with('morphable_type', 'ModelMorphManyModelStub');
        $model1->shouldReceive('save')->once()->andReturn(true);

        $model2 = m::mock(CModel::class);
        $model2->shouldReceive('setAttribute')->once()->with('morphable_id', 1);
        $model2->shouldReceive('setAttribute')->once()->with('morphable_type', 'ModelMorphManyModelStub');
        $model2->shouldReceive('save')->once()->andReturn(true);

        $result = $relation->saveMany([$model1, $model2]);

        $this->assertEquals([$model1, $model2], $result);
    }

    public function testCreateMethodProperlyCreatesNewMorphModel() {
        $relation = $this->getRelation();
        $created = m::mock(CModel::class);
        $created->shouldReceive('setAttribute')->once()->with('morphable_id', 1);
        $created->shouldReceive('setAttribute')->once()->with('morphable_type', 'ModelMorphManyModelStub');
        $created->shouldReceive('save')->once()->andReturn(true);
        $relation->getRelated()->shouldReceive('newInstance')->once()->with(['name' => 'taylor'])->andReturn($created);

        $this->assertSame($created, $relation->create(['name' => 'taylor']));
    }

    public function testCreateManyMethodProperlyCreatesNewMorphModels() {
        $relation = $this->getRelation();

        $created1 = m::mock(CModel::class);
        $created1->shouldReceive('setAttribute')->once()->with('morphable_id', 1);
        $created1->shouldReceive('setAttribute')->once()->with('morphable_type', 'ModelMorphManyModelStub');
        $created1->shouldReceive('save')->once()->andReturn(true);

        $created2 = m::mock(CModel::class);
        $created2->shouldReceive('setAttribute')->once()->with('morphable_id', 1);
        $created2->shouldReceive('setAttribute')->once()->with('morphable_type', 'ModelMorphManyModelStub');
        $created2->shouldReceive('save')->once()->andReturn(true);

        $relation->getRelated()->shouldReceive('newInstance')->once()->with(['name' => 'taylor'])->andReturn($created1);
        $relation->getRelated()->shouldReceive('newInstance')->once()->with(['name' => 'otwell'])->andReturn($created2);
        $relation->getRelated()->shouldReceive('newCollection')->once()->andReturnUsing(function ($items = []) {
            return new CModel_Collection($items);
        });

        $result = $relation->createMany([['name' => 'taylor'], ['name' => 'otwell']]);

        $this->assertSame([$created1, $created2], $result->all());
    }

    public function testMakeMethodDoesNotSaveNewModel() {
        $relation = $this->getRelation();
        $instance = m::mock(CModel::class);
        $instance->shouldReceive('setAttribute')->once()->with('morphable_id', 1);
        $instance->shouldReceive('setAttribute')->once()->with('morphable_type', 'ModelMorphManyModelStub');
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
            'morphable_type' => 'ModelMorphManyModelStub',
        ])->andReturn($created);

        $this->assertSame($created, $relation->forceCreate(['name' => 'taylor']));
    }

    public function testGetMorphTypeAndMorphClassAccessors() {
        $relation = $this->getRelation();

        $this->assertSame('morphable_type', $relation->getMorphType());
        $this->assertSame('morphable_type', $relation->getQualifiedMorphType());
        $this->assertSame('ModelMorphManyModelStub', $relation->getMorphClass());
    }

    protected function getRelation($parent = null) {
        $this->builder = m::mock(CModel_Query::class);
        $this->builder->shouldReceive('where')->with('morphable_type', 'ModelMorphManyModelStub');
        $this->builder->shouldReceive('where')->with('morphable_id', '=', 1);
        $this->builder->shouldReceive('whereNotNull')->with('morphable_id');
        $this->related = m::mock(CModel::class);
        $this->builder->shouldReceive('getModel')->andReturn($this->related);
        $this->parent = $parent ?: m::mock(CModel::class);

        if (!$parent) {
            $this->parent->shouldReceive('getMorphClass')->andReturn('ModelMorphManyModelStub');
            $this->parent->shouldReceive('getAttribute')->with('id')->andReturn(1);
        }

        return new CModel_Relation_MorphMany($this->builder, $this->parent, 'morphable_type', 'morphable_id', 'id');
    }

    protected function getRelationForNullParentKey() {
        $this->builder = m::mock(CModel_Query::class);
        $this->builder->shouldReceive('where');
        $this->builder->shouldReceive('whereNotNull');
        $this->related = m::mock(CModel::class);
        $this->builder->shouldReceive('getModel')->andReturn($this->related);
        $this->parent = m::mock(CModel::class);
        $this->parent->shouldReceive('getMorphClass')->andReturn('ModelMorphManyModelStub');
        $this->parent->shouldReceive('getAttribute')->with('id')->andReturnNull();

        return new CModel_Relation_MorphMany($this->builder, $this->parent, 'morphable_type', 'morphable_id', 'id');
    }
}
// @codingStandardsIgnoreStart
class ModelMorphManyModelStub extends CModel {
    public $morphable_id = 'foreign.value';
}
