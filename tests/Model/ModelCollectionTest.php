<?php
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ModelCollectionTest extends TestCase {
    protected function tearDown(): void {
        m::close();
    }

    public function testAddingItemsToCollection() {
        $c = new CModel_Collection(['foo']);
        $c->add('bar')->add('baz');
        $this->assertEquals(['foo', 'bar', 'baz'], $c->all());
    }

    public function testGettingMaxItemsFromCollection() {
        $c = new CModel_Collection([(object) ['foo' => 10], (object) ['foo' => 20]]);
        $this->assertEquals(20, $c->max('foo'));
    }

    public function testGettingMinItemsFromCollection() {
        $c = new CModel_Collection([(object) ['foo' => 10], (object) ['foo' => 20]]);
        $this->assertEquals(10, $c->min('foo'));
    }

    public function testContainsWithMultipleArguments() {
        $c = new CModel_Collection([['id' => 1], ['id' => 2]]);
        $this->assertTrue($c->contains('id', 1));
        $this->assertTrue($c->contains('id', '>=', 2));
        $this->assertFalse($c->contains('id', '>', 2));
    }

    public function testContainsIndicatesIfModelInArray() {
        $mockModel = m::mock(CModel::class);
        $mockModel->shouldReceive('is')->with($mockModel)->andReturn(true);
        $mockModel->shouldReceive('is')->andReturn(false);
        $mockModel2 = m::mock(CModel::class);
        $mockModel2->shouldReceive('is')->with($mockModel2)->andReturn(true);
        $mockModel2->shouldReceive('is')->andReturn(false);
        $mockModel3 = m::mock(CModel::class);
        $mockModel3->shouldReceive('is')->with($mockModel3)->andReturn(true);
        $mockModel3->shouldReceive('is')->andReturn(false);
        $c = new CModel_Collection([$mockModel, $mockModel2]);

        $this->assertTrue($c->contains($mockModel));
        $this->assertTrue($c->contains($mockModel2));
        $this->assertFalse($c->contains($mockModel3));
    }

    public function testContainsIndicatesIfDifferentModelInArray() {
        $mockModelFoo = m::namedMock('Foo', CModel::class);
        $mockModelFoo->shouldReceive('is')->with($mockModelFoo)->andReturn(true);
        $mockModelFoo->shouldReceive('is')->andReturn(false);
        $mockModelBar = m::namedMock('Bar', CModel::class);
        $mockModelBar->shouldReceive('is')->with($mockModelBar)->andReturn(true);
        $mockModelBar->shouldReceive('is')->andReturn(false);
        $c = new CModel_Collection([$mockModelFoo]);

        $this->assertTrue($c->contains($mockModelFoo));
        $this->assertFalse($c->contains($mockModelBar));
    }

    public function testContainsIndicatesIfKeyedModelInArray() {
        $mockModel = m::mock(CModel::class);
        $mockModel->shouldReceive('getKey')->andReturn('1');
        $c = new CModel_Collection([$mockModel]);
        $mockModel2 = m::mock(CModel::class);
        $mockModel2->shouldReceive('getKey')->andReturn('2');
        $c->add($mockModel2);

        $this->assertTrue($c->contains(1));
        $this->assertTrue($c->contains(2));
        $this->assertFalse($c->contains(3));
    }

    public function testContainsKeyAndValueIndicatesIfModelInArray() {
        $mockModel1 = m::mock(CModel::class);
        $mockModel1->shouldReceive('offsetExists')->with('name')->andReturn(true);
        $mockModel1->shouldReceive('offsetGet')->with('name')->andReturn('Taylor');
        $mockModel2 = m::mock(CModel::class);
        $mockModel2->shouldReceive('offsetExists')->andReturn(true);
        $mockModel2->shouldReceive('offsetGet')->with('name')->andReturn('Abigail');
        $c = new CModel_Collection([$mockModel1, $mockModel2]);

        $this->assertTrue($c->contains('name', 'Taylor'));
        $this->assertTrue($c->contains('name', 'Abigail'));
        $this->assertFalse($c->contains('name', 'Dayle'));
    }

    public function testContainsClosureIndicatesIfModelInArray() {
        $mockModel1 = m::mock(CModel::class);
        $mockModel1->shouldReceive('getKey')->andReturn(1);
        $mockModel2 = m::mock(CModel::class);
        $mockModel2->shouldReceive('getKey')->andReturn(2);
        $c = new CModel_Collection([$mockModel1, $mockModel2]);

        $this->assertTrue($c->contains(function ($model) {
            return $model->getKey() < 2;
        }));
        $this->assertFalse($c->contains(function ($model) {
            return $model->getKey() > 2;
        }));
    }

    public function testFindMethodFindsModelById() {
        $mockModel = m::mock(CModel::class);
        $mockModel->shouldReceive('getKey')->andReturn(1);
        $c = new CModel_Collection([$mockModel]);

        $this->assertSame($mockModel, $c->find(1));
        $this->assertSame('taylor', $c->find(2, 'taylor'));
    }

    public function testFindMethodFindsManyModelsById() {
        $model1 = (new TestModelCollectionModel())->forceFill(['id' => 1]);
        $model2 = (new TestModelCollectionModel())->forceFill(['id' => 2]);
        $model3 = (new TestModelCollectionModel())->forceFill(['id' => 3]);

        $c = new CModel_Collection();
        $this->assertInstanceOf(CModel_Collection::class, $c->find([]));
        $this->assertCount(0, $c->find([1]));

        $c->push($model1);
        $this->assertCount(1, $c->find([1]));
        $this->assertEquals(1, $c->find([1])->first()->id);
        $this->assertCount(0, $c->find([2]));

        $c->push($model2)->push($model3);
        $this->assertCount(1, $c->find([2]));
        $this->assertEquals(2, $c->find([2])->first()->id);
        $this->assertCount(2, $c->find([2, 3, 4]));
        $this->assertCount(2, $c->find(c::collect([2, 3, 4])));
        $this->assertEquals([2, 3], $c->find(c::collect([2, 3, 4]))->pluck('id')->all());
        $this->assertEquals([2, 3], $c->find([2, 3, 4])->pluck('id')->all());
    }

    public function testLoadMethodEagerLoadsGivenRelationships() {
        $c = $this->getMockBuilder(CModel_Collection::class)->onlyMethods(['first'])->setConstructorArgs([['foo']])->getMock();
        $mockItem = m::mock(stdClass::class);
        $c->expects($this->once())->method('first')->willReturn($mockItem);
        $mockItem->shouldReceive('newQueryWithoutRelationships')->once()->andReturn($mockItem);
        $mockItem->shouldReceive('with')->with(['bar', 'baz'])->andReturn($mockItem);
        $mockItem->shouldReceive('eagerLoadRelations')->once()->with(['foo'])->andReturn(['results']);
        $c->load('bar', 'baz');

        $this->assertEquals(['results'], $c->all());
    }

    public function testCollectionDictionaryReturnsModelKeys() {
        $one = m::mock(CModel::class);
        $one->shouldReceive('getKey')->andReturn(1);

        $two = m::mock(CModel::class);
        $two->shouldReceive('getKey')->andReturn(2);

        $three = m::mock(CModel::class);
        $three->shouldReceive('getKey')->andReturn(3);

        $c = new CModel_Collection([$one, $two, $three]);

        $this->assertEquals([1, 2, 3], $c->modelKeys());
    }

    public function testCollectionMergesWithGivenCollection() {
        $one = m::mock(CModel::class);
        $one->shouldReceive('getKey')->andReturn(1);

        $two = m::mock(CModel::class);
        $two->shouldReceive('getKey')->andReturn(2);

        $three = m::mock(CModel::class);
        $three->shouldReceive('getKey')->andReturn(3);

        $c1 = new CModel_Collection([$one, $two]);
        $c2 = new CModel_Collection([$two, $three]);

        $this->assertEquals(new CModel_Collection([$one, $two, $three]), $c1->merge($c2));
    }

    public function testMap() {
        $one = m::mock(CModel::class);
        $two = m::mock(CModel::class);

        $c = new CModel_Collection([$one, $two]);

        $cAfterMap = $c->map(function ($item) {
            return $item;
        });

        $this->assertEquals($c->all(), $cAfterMap->all());
        $this->assertInstanceOf(CModel_Collection::class, $cAfterMap);
    }

    public function testMappingToNonModelsReturnsABaseCollection() {
        $one = m::mock(CModel::class);
        $two = m::mock(CModel::class);

        $c = (new CModel_Collection([$one, $two]))->map(function ($item) {
            return 'not-a-model';
        });

        $this->assertEquals(CCollection::class, get_class($c));
    }

    public function testMapWithKeys() {
        $one = m::mock(CModel::class);
        $two = m::mock(CModel::class);

        $c = new CModel_Collection([$one, $two]);

        $key = 0;
        $cAfterMap = $c->mapWithKeys(function ($item) use (&$key) {
            return [$key++ => $item];
        });

        $this->assertEquals($c->all(), $cAfterMap->all());
        $this->assertInstanceOf(CModel_Collection::class, $cAfterMap);
    }

    public function testMapWithKeysToNonModelsReturnsABaseCollection() {
        $one = m::mock(CModel::class);
        $two = m::mock(CModel::class);

        $key = 0;
        $c = (new CModel_Collection([$one, $two]))->mapWithKeys(function ($item) use (&$key) {
            return [$key++ => 'not-a-model'];
        });

        $this->assertEquals(CCollection::class, get_class($c));
    }

    public function testCollectionDiffsWithGivenCollection() {
        $one = m::mock(CModel::class);
        $one->shouldReceive('getKey')->andReturn(1);

        $two = m::mock(CModel::class);
        $two->shouldReceive('getKey')->andReturn(2);

        $three = m::mock(CModel::class);
        $three->shouldReceive('getKey')->andReturn(3);

        $c1 = new CModel_Collection([$one, $two]);
        $c2 = new CModel_Collection([$two, $three]);

        $this->assertEquals(new CModel_Collection([$one]), $c1->diff($c2));
    }

    public function testCollectionReturnsDuplicateBasedOnlyOnKeys() {
        $one = new TestModelCollectionModel();
        $two = new TestModelCollectionModel();
        $three = new TestModelCollectionModel();
        $four = new TestModelCollectionModel();
        $one->id = 1;
        $one->someAttribute = '1';
        $two->id = 1;
        $two->someAttribute = '2';
        $three->id = 1;
        $three->someAttribute = '3';
        $four->id = 2;
        $four->someAttribute = '4';

        $duplicates = CModel_Collection::make([$one, $two, $three, $four])->duplicates()->all();
        $this->assertSame([1 => $two, 2 => $three], $duplicates);
        $duplicates = CModel_Collection::make([$one, $two, $three, $four])->duplicatesStrict()->all();
        $this->assertSame([1 => $two, 2 => $three], $duplicates);
    }

    public function testCollectionIntersectWithNull() {
        $one = m::mock(CModel::class);
        $one->shouldReceive('getKey')->andReturn(1);

        $two = m::mock(CModel::class);
        $two->shouldReceive('getKey')->andReturn(2);

        $three = m::mock(CModel::class);
        $three->shouldReceive('getKey')->andReturn(3);

        $c1 = new CModel_Collection([$one, $two, $three]);

        $this->assertEquals([], $c1->intersect(null)->all());
    }

    public function testCollectionIntersectsWithGivenCollection() {
        $one = m::mock(CModel::class);
        $one->shouldReceive('getKey')->andReturn(1);

        $two = m::mock(CModel::class);
        $two->shouldReceive('getKey')->andReturn(2);

        $three = m::mock(CModel::class);
        $three->shouldReceive('getKey')->andReturn(3);

        $c1 = new CModel_Collection([$one, $two]);
        $c2 = new CModel_Collection([$two, $three]);

        $this->assertEquals(new CModel_Collection([$two]), $c1->intersect($c2));
    }

    public function testCollectionReturnsUniqueItems() {
        $one = m::mock(CModel::class);
        $one->shouldReceive('getKey')->andReturn(1);

        $two = m::mock(CModel::class);
        $two->shouldReceive('getKey')->andReturn(2);

        $c = new CModel_Collection([$one, $two, $two]);

        $this->assertEquals(new CModel_Collection([$one, $two]), $c->unique());
    }

    public function testCollectionReturnsUniqueStrictBasedOnKeysOnly() {
        $one = new TestModelCollectionModel();
        $two = new TestModelCollectionModel();
        $three = new TestModelCollectionModel();
        $four = new TestModelCollectionModel();
        $one->id = 1;
        $one->someAttribute = '1';
        $two->id = 1;
        $two->someAttribute = '2';
        $three->id = 1;
        $three->someAttribute = '3';
        $four->id = 2;
        $four->someAttribute = '4';

        $uniques = CModel_Collection::make([$one, $two, $three, $four])->unique()->all();
        $this->assertSame([$three, $four], $uniques);
        $uniques = CModel_Collection::make([$one, $two, $three, $four])->unique(null, true)->all();
        $this->assertSame([$three, $four], $uniques);
    }

    public function testOnlyReturnsCollectionWithGivenModelKeys() {
        $one = m::mock(CModel::class);
        $one->shouldReceive('getKey')->andReturn(1);

        $two = m::mock(CModel::class);
        $two->shouldReceive('getKey')->andReturn(2);

        $three = m::mock(CModel::class);
        $three->shouldReceive('getKey')->andReturn(3);

        $c = new CModel_Collection([$one, $two, $three]);

        $this->assertEquals($c, $c->only(null));
        $this->assertEquals(new CModel_Collection([$one]), $c->only(1));
        $this->assertEquals(new CModel_Collection([$two, $three]), $c->only([2, 3]));
    }

    public function testExceptReturnsCollectionWithoutGivenModelKeys() {
        $one = m::mock(CModel::class);
        $one->shouldReceive('getKey')->andReturn(1);

        $two = m::mock(CModel::class);
        $two->shouldReceive('getKey')->andReturn('2');

        $three = m::mock(CModel::class);
        $three->shouldReceive('getKey')->andReturn(3);

        $c = new CModel_Collection([$one, $two, $three]);

        $this->assertEquals(new CModel_Collection([$one, $three]), $c->except(2));
        $this->assertEquals(new CModel_Collection([$one]), $c->except([2, 3]));
    }

    public function testMakeHiddenAddsHiddenOnEntireCollection() {
        $c = new CModel_Collection([new TestModelCollectionModel()]);
        $c = $c->makeHidden(['visible']);

        $this->assertEquals(['hidden', 'visible'], $c[0]->getHidden());
    }

    public function testMakeVisibleRemovesHiddenFromEntireCollection() {
        $c = new CModel_Collection([new TestModelCollectionModel()]);
        $c = $c->makeVisible(['hidden']);

        $this->assertEquals([], $c[0]->getHidden());
    }

    public function testAppendsAddsTestOnEntireCollection() {
        $c = new CModel_Collection([new TestModelCollectionModel()]);
        $c = $c->makeVisible('test');
        $c = $c->append('test');

        $this->assertEquals(['test' => 'test'], $c[0]->toArray());
    }

    public function testNonModelRelatedMethods() {
        $a = new CModel_Collection([['foo' => 'bar'], ['foo' => 'baz']]);
        $b = new CModel_Collection(['a', 'b', 'c']);
        $this->assertEquals(CCollection::class, get_class($a->pluck('foo')));
        $this->assertEquals(CCollection::class, get_class($a->keys()));
        $this->assertEquals(CCollection::class, get_class($a->collapse()));
        $this->assertEquals(CCollection::class, get_class($a->flatten()));
        $this->assertEquals(CCollection::class, get_class($a->zip(['a', 'b'], ['c', 'd'])));
        $this->assertEquals(CCollection::class, get_class($b->flip()));
    }

    public function testMakeVisibleRemovesHiddenAndIncludesVisible() {
        $c = new CModel_Collection([new TestModelCollectionModel()]);
        $c = $c->makeVisible('hidden');

        $this->assertEquals([], $c[0]->getHidden());
        $this->assertEquals(['visible', 'hidden'], $c[0]->getVisible());
    }

    public function testQueueableCollectionImplementation() {
        $c = new CModel_Collection([new TestModelCollectionModel(), new TestModelCollectionModel()]);
        $this->assertEquals(TestModelCollectionModel::class, $c->getQueueableClass());
    }

    public function testQueueableCollectionImplementationThrowsExceptionOnMultipleModelTypes() {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Queueing collections with multiple model types is not supported.');

        $c = new CModel_Collection([new TestModelCollectionModel(), (object) ['id' => 'something']]);
        $c->getQueueableClass();
    }

    public function testQueueableRelationshipsReturnsOnlyRelationsCommonToAllModels() {
        // This is needed to prevent loading non-existing relationships on polymorphic model collections (#26126)
        $c = new CModel_Collection([
            new class() {
                public function getQueueableRelations() {
                    return ['user'];
                }
            },
            new class() {
                public function getQueueableRelations() {
                    return ['user', 'comments'];
                }
            },
        ]);

        $this->assertEquals(['user'], $c->getQueueableRelations());
    }

    public function testQueueableRelationshipsIgnoreCollectionKeys() {
        $c = new CModel_Collection([
            'foo' => new class() {
                public function getQueueableRelations() {
                    return [];
                }
            },
            'bar' => new class() {
                public function getQueueableRelations() {
                    return [];
                }
            },
        ]);

        $this->assertEquals([], $c->getQueueableRelations());
    }

    public function testEmptyCollectionStayEmptyOnFresh() {
        $c = new CModel_Collection();
        $this->assertEquals($c, $c->fresh());
    }

    public function testCanConvertCollectionOfModelsToModelQueryBuilder() {
        $one = m::mock(CModel::class);
        $one->shouldReceive('getKey')->andReturn(1);

        $two = m::mock(CModel::class);
        $two->shouldReceive('getKey')->andReturn(2);

        $c = new CModel_Collection([$one, $two]);

        $mocBuilder = m::mock(CModel_Query::class);
        $one->shouldReceive('newModelQuery')->once()->andReturn($mocBuilder);
        $mocBuilder->shouldReceive('whereKey')->once()->with($c->modelKeys())->andReturn($mocBuilder);
        $this->assertInstanceOf(CModel_Query::class, $c->toQuery());
    }

    public function testConvertingEmptyCollectionToQueryThrowsException() {
        $this->expectException(LogicException::class);

        $c = new CModel_Collection();
        $c->toQuery();
    }
}
// @codingStandardsIgnoreStart
class TestModelCollectionModel extends CModel {
    protected $visible = ['visible'];

    protected $hidden = ['hidden'];

    public function getTestAttribute() {
        return 'test';
    }
}
