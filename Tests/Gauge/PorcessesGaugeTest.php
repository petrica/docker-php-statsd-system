<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 5/29/2016
 * Time: 13:10
 */
namespace Petrica\StatsdSystem\Tests\Gauge;

use Petrica\StatsdSystem\Collection\ValuesCollection;

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

        $collection = $gauge->getCollection();

        $expected = new ValuesCollection();
        $expected->add('process_name.cpu.value', 14);
        $expected->add('other_process.cpu.value', 5);
        $expected->add('process_name.memory.value', 7);
        $expected->add('other_process.memory.value', 2.5);

        $this->assertEquals($expected, $collection);
    }
}
