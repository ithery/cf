<?php
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ModelMorphToManyTest extends TestCase {
    protected $builder;

    protected $related;

    protected $parent;

    protected function tearDown() {
        m::close();
    }

    /**
     * @param bool $inverse
     *
     * @return array
     */
    protected function getRelationArguments($inverse = false, $parentKeyValue = 1) {
        $this->parent = m::mock(CModel::class);
        $this->parent->shouldReceive('getAttribute')->with('id')->andReturn($parentKeyValue);
        $this->parent->shouldReceive('getConnectionName')->andReturn(null);
        $this->parent->shouldReceive('getCreatedAtColumn')->andReturn('created_at');
        $this->parent->shouldReceive('getUpdatedAtColumn')->andReturn('updated_at');
        $this->parent->shouldReceive('touches')->andReturn(false);
        $this->parent->shouldReceive('freshTimestamp')->andReturn('2021-01-01 00:00:00');
        $this->parent->shouldReceive('getMorphClass')->andReturn('ParentModel');

        $this->related = m::mock(CModel::class);
        $this->related->shouldReceive('getTable')->andReturn('tags');
        $this->related->shouldReceive('getKeyName')->andReturn('id');
        $this->related->shouldReceive('getKeyType')->andReturn('int');
        $this->related->shouldReceive('qualifyColumn')->with('id')->andReturn('tags.id');
        $this->related->shouldReceive('touches')->andReturn(false);
        $this->related->shouldReceive('getMorphClass')->andReturn('RelatedModel');

        $this->builder = m::mock(CModel_Query::class);
        $this->builder->shouldReceive('getModel')->andReturn($this->related);
        $this->builder->shouldReceive('join')->once()->with('taggables', 'tags.id', '=', 'taggables.tag_id');
        $this->builder->shouldReceive('where')->once()->with('taggables.taggable_id', '=', $parentKeyValue);

        $morphClass = $inverse ? 'RelatedModel' : 'ParentModel';
        $this->builder->shouldReceive('where')->once()->with('taggables.taggable_type', $morphClass);

        return [
            $this->builder,
            $this->parent,
            'taggable',
            'taggables',
            'taggable_id',
            'tag_id',
            'id',
            'id',
            'relation_name',
            $inverse,
        ];
    }

    /**
     * @return CModel_Relation_MorphToMany
     */
    protected function getRelation($inverse = false, $parentKeyValue = 1) {
        return new CModel_Relation_MorphToMany(...$this->getRelationArguments($inverse, $parentKeyValue));
    }

    /**
     * @param array $methods
     *
     * @return CModel_Relation_MorphToMany|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockRelation(array $methods, $inverse = false) {
        return $this->getMockBuilder(CModel_Relation_MorphToMany::class)
            ->onlyMethods($methods)
            ->setConstructorArgs($this->getRelationArguments($inverse))
            ->getMock();
    }

    public function testConstructorSetsMorphTypeAndMorphClassFromParentByDefault() {
        $relation = $this->getRelation();

        $this->assertSame('taggable_type', $relation->getMorphType());
        $this->assertSame('ParentModel', $relation->getMorphClass());
        $this->assertFalse($relation->getInverse());
    }

    public function testConstructorUsesRelatedMorphClassWhenInverse() {
        $relation = $this->getRelation(true);

        $this->assertSame('taggable_type', $relation->getMorphType());
        $this->assertSame('RelatedModel', $relation->getMorphClass());
        $this->assertTrue($relation->getInverse());
    }

    public function testAddConstraintsAppliesBothForeignKeyAndMorphTypeWhere() {
        // Both ->once() "where" expectations declared in getRelationArguments() are
        // satisfied purely by constructing the relation (addConstraints() runs in the
        // CModel_Relation base constructor); Mockery verifies them at tearDown().
        $this->getRelation();

        $this->addToAssertionCount(1);
    }

    public function testEagerConstraintsAddBothWhereInAndMorphTypeWhere() {
        $relation = $this->getRelation();
        $relation->getParent()->shouldReceive('getKeyName')->andReturn('id');
        $relation->getParent()->shouldReceive('getKeyType')->andReturn('int');
        $relation->getQuery()->shouldReceive('whereIntegerInRaw')->once()->with('taggables.taggable_id', [1, 2]);
        $relation->getQuery()->shouldReceive('where')->once()->with('taggables.taggable_type', 'ParentModel');

        $model1 = new ModelMorphToManyModelStub();
        $model1->setRawAttributes(['id' => 1]);
        $model2 = new ModelMorphToManyModelStub();
        $model2->setRawAttributes(['id' => 2]);

        $relation->addEagerConstraints([$model1, $model2]);

        $this->addToAssertionCount(1);
    }

    public function testAliasedPivotColumnsIncludeMorphType() {
        $relation = $this->getRelation();

        $queryStub = new stdClass();
        $queryStub->columns = null;
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryStub);
        $relation->getQuery()->shouldReceive('applyScopes')->once()->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('addSelect')->once()->with([
            'tags.*',
            'taggables.taggable_id as pivot_taggable_id',
            'taggables.tag_id as pivot_tag_id',
            'taggables.taggable_type as pivot_taggable_type',
        ])->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('getModels')->once()->andReturn([]);
        $relation->getQuery()->shouldReceive('eagerLoadRelations')->never();
        $relation->getRelated()->shouldReceive('newCollection')->once()->with([])->andReturn($empty = new CModel_Collection());

        $this->assertSame($empty, $relation->get());
    }

    public function testBaseAttachRecordIncludesMorphTypeAndClass() {
        $relation = $this->getRelation();

        $queryBuilder = m::mock(stdClass::class);
        $insertQuery = m::mock(stdClass::class);
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('newQuery')->once()->andReturn($insertQuery);
        $insertQuery->shouldReceive('from')->once()->with('taggables')->andReturn($insertQuery);
        $insertQuery->shouldReceive('insert')->once()->with([[
            'tag_id' => 2,
            'taggable_id' => 1,
            'taggable_type' => 'ParentModel',
        ]])->andReturn(true);

        $relation->attach(2);

        $this->addToAssertionCount(1);
    }

    public function testNewPivotQueryAddsMorphTypeConstraint() {
        $relation = $this->getRelation();

        $queryBuilder = m::mock(stdClass::class);
        $pivotQuery = m::mock(stdClass::class);
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('newQuery')->once()->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('from')->once()->with('taggables')->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('where')->once()->with('taggable_id', 1)->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('where')->once()->with('taggable_type', 'ParentModel')->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('whereIn')->once()->with('taggables.tag_id', [1, 2])->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('delete')->once()->andReturn(2);

        $this->assertSame(2, $relation->detach([1, 2]));
    }

    public function testNewPivotSetsMorphTypeAndMorphClassOnPivotModel() {
        $relation = $this->getRelation();

        $pivot = $relation->newPivot(['tag_id' => 2, 'taggable_id' => 1, 'taggable_type' => 'ParentModel'], true);

        $this->assertInstanceOf(CModel_Relation_MorphPivot::class, $pivot);
        $this->assertSame('taggable_id', $pivot->getForeignKey());
        $this->assertSame('tag_id', $pivot->getRelatedKey());
    }

    public function testNewPivotUsesCustomPivotClassWhenUsingIsSet() {
        $relation = $this->getRelation();
        $relation->using(ModelMorphToManyPivotStub::class);

        $pivot = $relation->newPivot(['tag_id' => 2, 'taggable_id' => 1, 'taggable_type' => 'ParentModel'], true);

        $this->assertInstanceOf(ModelMorphToManyPivotStub::class, $pivot);
    }

    public function testGetCurrentlyAttachedPivotsAppliesMorphTypeAndClassToResults() {
        $relation = $this->getRelation();
        // The base trait's getCurrentlyAttachedPivots() only maps into MorphPivot when a
        // custom pivot class using CModel_Relation_MorphPivot is configured via using();
        // otherwise the mapped record stays a plain CModel_Relation_Pivot and MorphToMany's
        // override (which only reacts to "instanceof MorphPivot") is a no-op passthrough.
        $relation->using(ModelMorphToManyPivotStub::class);

        $queryBuilder = m::mock(stdClass::class);
        $pivotQuery = m::mock(stdClass::class);
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('newQuery')->once()->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('from')->once()->with('taggables')->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('where')->once()->with('taggable_id', 1)->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('where')->once()->with('taggable_type', 'ParentModel')->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('get')->once()->andReturn(new CCollection([
            (object) ['taggable_id' => 1, 'tag_id' => 2, 'taggable_type' => 'ParentModel'],
        ]));

        $method = new ReflectionMethod($relation, 'getCurrentlyAttachedPivots');
        $method->setAccessible(true);
        $pivots = $method->invoke($relation);

        $this->assertCount(1, $pivots);
        $this->assertInstanceOf(CModel_Relation_MorphPivot::class, $pivots->first());
    }

    public function testGetRelationExistenceQueryAddsMorphTypeConstraint() {
        $relation = $this->getRelation();

        $query = m::mock(CModel_Query::class);
        $parentQuery = m::mock(CModel_Query::class);
        $queryOfQuery = new stdClass();
        $queryOfQuery->from = 'other_table';
        $parentQueryOfQuery = new stdClass();
        $parentQueryOfQuery->from = 'parent_table';
        $query->shouldReceive('getQuery')->andReturn($queryOfQuery);
        $parentQuery->shouldReceive('getQuery')->andReturn($parentQueryOfQuery);

        $query->shouldReceive('join')->once()->with('taggables', 'tags.id', '=', 'taggables.tag_id');
        $query->shouldReceive('select')->once()->with(['*'])->andReturnSelf();
        $query->shouldReceive('whereColumn')->once()->andReturnSelf();
        $query->shouldReceive('where')->once()->with('taggables.taggable_type', 'ParentModel')->andReturnSelf();
        // BelongsToMany::getQualifiedParentKeyName() delegates to $parent->qualifyColumn(),
        // not $parent->getQualifiedKeyName() (which CModel_Relation's base getter would use).
        $relation->getParent()->shouldReceive('qualifyColumn')->once()->with('id')->andReturn('parent_table.id');

        $result = $relation->getRelationExistenceQuery($query, $parentQuery);

        $this->assertSame($query, $result);
    }

    public function testGetterHelpers() {
        $relation = $this->getRelation();

        $this->assertSame('taggable_type', $relation->getMorphType());
        $this->assertSame('ParentModel', $relation->getMorphClass());
        $this->assertFalse($relation->getInverse());
        $this->assertSame('taggables', $relation->getTable());
        $this->assertSame('taggable_id', $relation->getForeignPivotKeyName());
        $this->assertSame('tag_id', $relation->getRelatedPivotKeyName());
    }
}
// @codingStandardsIgnoreStart
class ModelMorphToManyModelStub extends CModel {
}

class ModelMorphToManyPivotStub extends CModel_Relation_MorphPivot {
}
