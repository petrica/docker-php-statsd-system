<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 5/29/2016
 * Time: 13:10
 */
namespace Petrica\StatsdSystem\Tests\Gauge;

use Petrica\StatsdSystem\Collection\ValuesCollection;
use Petrica\StatsdSystem\Gauge\ProcessesGauge;
use Petrica\StatsdSystem\Gauge\RemoteProcessesGauge;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class ProcessesGaugeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetGauge()
    {
        $gauge = $this->getMockBuilder('Petrica\StatsdSystem\Gauge\ProcessesGauge')
            ->setMethods(array(
                'getCommand'
            ))
            ->getMock();

        $command = $this->getMockBuilder('Petrica\StatsdSystem\Model\TopCommand')
            ->setMethods(array(
                'run'
            ))
            ->getMock();

        $command->method('run')
            ->willReturn(array(
                array(),
                array(),
                array(),
                array(),
                array(),
                array(),
                array(),
                array(
                    '1', 'root', '0', '0', '0', '0', '0', '0', '10.5', '5.5', '0:0', 'process_name'
                ),
                array(
                    '2', 'root', '0', '0', '0', '0', '0', '0', '3.5', '1.5', '0:0', 'process_name'
                ),
                array(
                    '3', 'root', '0', '0', '0', '0', '0', '0', '2', '0.5', '0:0', 'process_name_exclude'
                ),
                array(
                    '4', 'root', '0', '0', '0', '0', '0', '0', '5', '2.5', '0:0', 'other_process'
                )
            ));

        $gauge->method('getCommand')
            ->willReturn($command);

        $cache = new FilesystemAdapter('statsd.localhost');
        $item = $cache->getItem('collection');

        $cacheCollection = new ValuesCollection();
        $cacheCollection->add('test_cache', 5);

        $item->set($cacheCollection);
        $cache->save($item);

        $collection = $gauge->getCollection();

        $expected = new ValuesCollection();
        $expected->add('process_name.cpu.value', 14);
        $expected->add('other_process.cpu.value', 5);
        $expected->add('process_name.memory.value', 7);
        $expected->add('other_process.memory.value', 2.5);
        $expected->add('test_cache', 0);

        $this->assertEquals($expected, $collection);

        $cache->clear();
    }

    /**
     * Test cache system
     */
    public function testPersistCollection()
    {
        $gauge = new ProcessesGauge();

        $class = new \ReflectionClass('Petrica\StatsdSystem\Gauge\ProcessesGauge');
        $persistMethod = $class->getMethod('persistCollection');
        $persistMethod->setAccessible(true);

        $class = new \ReflectionClass('Petrica\StatsdSystem\Gauge\ProcessesGauge');
        $retrieveCollection = $class->getMethod('retrieveCollection');
        $retrieveCollection->setAccessible(true);

        $collection = new ValuesCollection();
        $collection->add('test_1', 1);
        $collection->add('test_2', 2);

        $persistMethod->invokeArgs($gauge, array($collection));

        $retreive = $retrieveCollection->invoke($gauge);

        $cache = new FilesystemAdapter('statsd.localhost');
        $cache->clear();

        $this->assertEquals($collection, $retreive);
    }

    /**
     * Test remote cache system
     */
    public function testPersistRemoteCollection()
    {
        $gauge = new RemoteProcessesGauge('root@127.0.0.1', 22);

        $class = new \ReflectionClass('Petrica\StatsdSystem\Gauge\ProcessesGauge');
        $persistMethod = $class->getMethod('persistCollection');
        $persistMethod->setAccessible(true);

        $class = new \ReflectionClass('Petrica\StatsdSystem\Gauge\ProcessesGauge');
        $retrieveCollection = $class->getMethod('retrieveCollection');
        $retrieveCollection->setAccessible(true);

        $collection = new ValuesCollection();
        $collection->add('test_1', 1);
        $collection->add('test_2', 2);

        $persistMethod->invokeArgs($gauge, array($collection));

        $retreive = $retrieveCollection->invoke($gauge);

        $cache = new FilesystemAdapter('statsd.127.0.0.1.22');
        $cache->clear();

        $this->assertEquals($collection, $retreive);
    }
}
