<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/30/2016
 * Time: 23:06
 */
namespace Petrica\StatsdSystem\Tests\Collection;

use Petrica\StatsdSystem\Collection\ValuesCollection;

class ValuesCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $collection = new ValuesCollection(array('test'));
        $collection->add('another', 1);
        $collection->offsetSet('offset', 10);
        $collection->offsetSet('missing', 5);
        $collection->offsetUnset('missing');

        $expected = array('test', 'another' => 1, 'offset' => 10);

        $iterator = new \ArrayIterator($expected);

        $this->assertEquals($expected, $collection->getValues());
        $this->assertEquals($iterator, $collection->getIterator());
        $this->assertEquals(3, $collection->count());
        $this->assertTrue($collection->offsetExists('another'));
        $this->assertFalse($collection->offsetExists('not_exist'));
        $this->assertEquals('test', $collection->offsetGet(0));
    }
}