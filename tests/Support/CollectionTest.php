<?php

namespace Mvdnbrk\MyParcel\Tests\Collection;

use PHPUnit\Framework\TestCase;
use Mvdnbrk\MyParcel\Support\Collection;

class CollectionTest extends TestCase
{
    /** @test */
    public function offset_access()
    {
        $c = new Collection(['name' => 'john']);
        $this->assertEquals('john', $c['name']);

        $c['name'] = 'jane';
        $this->assertEquals('jane', $c['name']);

        $this->assertTrue(isset($c['name']));
        unset($c['name']);
        $this->assertFalse(isset($c['name']));

        $c[] = 'jason';
        $this->assertEquals('jason', $c[0]);
    }

    /** @test */
    public function array_access_offset_exists()
    {
        $c = new Collection(['foo', 'bar']);
        $this->assertTrue($c->offsetExists(0));
        $this->assertTrue($c->offsetExists(1));
        $this->assertFalse($c->offsetExists(1000));
    }

    /** @test */
    public function array_access_offset_get()
    {
        $c = new Collection(['foo', 'bar']);
        $this->assertEquals('foo', $c->offsetGet(0));
        $this->assertEquals('bar', $c->offsetGet(1));
    }

    /** @test */
    public function testArrayAccessOffsetSet()
    {
        $c = new Collection(['foo', 'foo']);
        $c->offsetSet(1, 'bar');
        $this->assertEquals('bar', $c[1]);
        $c->offsetSet(null, 'qux');
        $this->assertEquals('qux', $c[2]);
    }

    /** @test */
    public function array_access_offset_unset()
    {
        $c = new Collection(['foo', 'bar']);
        $c->offsetUnset(1);
        $this->assertFalse(isset($c[1]));
    }

    /** @test */
    public function to_array_returns_an_array()
    {
        $c = new Collection(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $c->toArray());

        $c = new Collection(['key' => 'value']);
        $this->assertEquals(['key' => 'value'], $c->toArray());
    }

    /** @test */
    public function change_key_case_returns_array_with_changed_key_case()
    {
        $c = new Collection(['FOO' => 'BAR', 'bAr' => 'bAz']);
        $this->assertEquals(['foo' => 'BAR', 'bar' => 'bAz'], $c->changeKeyCase()->all());
    }

    /** @test */
    public function first_returns_first_item_in_collection()
    {
        $c = new Collection(['foo', 'bar']);
        $this->assertEquals('foo', $c->first());
    }

    /** @test */
    public function first_with_default()
    {
        $data = new Collection;
        $result = $data->first('default');
        $this->assertEquals('default', $result);
    }

    /** @test */
    public function get_with_default()
    {
        $data = new Collection;
        $result = $data->get('foo', 'default');
        $this->assertEquals('default', $result);
    }

    /** @test */
    public function last_returns_last_item_in_collection()
    {
        $c = new Collection(['foo', 'bar']);
        $this->assertEquals('bar', $c->last());
    }

    /** @test */
    public function last_with_default()
    {
        $data = new Collection;
        $result = $data->last('default');
        $this->assertEquals('default', $result);
    }

    /** @test */
    public function collection_is_countable()
    {
        $c = new Collection(['foo', 'bar']);
        $this->assertCount(2, $c);
    }

    /** @test */
    public function each()
    {
        $c = new Collection($original = [1, 2, 'foo' => 'bar', 'bam' => 'baz']);

        $result = [];
        $c->each(function ($item, $key) use (&$result) {
            $result[$key] = $item;
        });
        $this->assertEquals($original, $result);

        $result = [];
        $c->each(function ($item, $key) use (&$result) {
            $result[$key] = $item;
            if (is_string($key)) {
                return false;
            }
        });
        $this->assertEquals([1, 2, 'foo' => 'bar'], $result);
    }

    /** @test */
    public function make_method()
    {
        $collection = Collection::make(['foo']);
        $this->assertEquals(['foo'], $collection->all());
    }

    /** @test */
    public function map()
    {
        $data = new Collection(['first' => 'john', 'last' => 'doe']);
        $data = $data->map(function ($item, $key) {
            return $key.'-'.strrev($item);
        });

        $this->assertEquals(['first' => 'first-nhoj', 'last' => 'last-eod'], $data->all());
    }

    /** @test */
    public function map_with_keys()
    {
        $data = new Collection([
            ['name' => 'Blastoise', 'type' => 'Water', 'idx' => 9],
            ['name' => 'Charmander', 'type' => 'Fire', 'idx' => 4],
            ['name' => 'Dragonair', 'type' => 'Dragon', 'idx' => 148],
        ]);
        $data = $data->mapWithKeys(function ($pokemon) {
            return [$pokemon['name'] => $pokemon['type']];
        });

        $this->assertEquals(
            ['Blastoise' => 'Water', 'Charmander' => 'Fire', 'Dragonair' => 'Dragon'],
            $data->all()
        );
    }

    /** @test */
    public function push()
    {
        $data = new Collection();
        $data->push('value');

        $this->assertEquals(
            ['value'],
            $data->all()
        );
    }

    /** @test */
    public function slice_ffset()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8]);
        $this->assertEquals([4, 5, 6, 7, 8], $collection->slice(3)->values()->toArray());
    }

    /** @test */
    public function slice_negative_offset()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8]);
        $this->assertEquals([6, 7, 8], $collection->slice(-3)->values()->toArray());
    }

    /** @test */
    public function slice_offset_and_length()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8]);
        $this->assertEquals([4, 5, 6], $collection->slice(3, 3)->values()->toArray());
    }

    /** @test */
    public function slice_offset_and_negative_length()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8]);
        $this->assertEquals([4, 5, 6, 7], $collection->slice(3, -1)->values()->toArray());
    }

    /** @test */
    public function slice_negative_offset_and_length()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8]);
        $this->assertEquals([4, 5, 6], $collection->slice(-5, 3)->values()->toArray());
    }

    /** @test */
    public function slice_negative_offset_and_negative_length()
    {
        $collection = new Collection([1, 2, 3, 4, 5, 6, 7, 8]);
        $this->assertEquals([3, 4, 5, 6], $collection->slice(-6, -2)->values()->toArray());
    }

    /** @test */
    public function take()
    {
        $collection = new Collection(['john', 'jane', 'mike']);
        $collection = $collection->take(2);
        $this->assertEquals(['john', 'jane'], $collection->all());
    }
}
