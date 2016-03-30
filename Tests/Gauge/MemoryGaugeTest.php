<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/30/2016
 * Time: 22:51
 */
namespace Petrica\StatsdSystem\Tests\Gauge;

class MemoryGaugeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetGauge()
    {
        $gauge = $this->getMockBuilder('Petrica\StatsdSystem\Gauge\MemoryGauge')
            ->setMethods(array(
                'getSystemMemoryInfo'
            ))
            ->getMock();

        $gauge->expects($this->exactly(4))
            ->method('getSystemMemoryInfo')
            ->willReturnOnConsecutiveCalls(
                array(),
                array('MemTotal' => 5),
                array('MemFree' => 15),
                array('MemTotal' => 20, 'MemFree' => 5)
            );

        # 1st call
        $collection = $gauge->getCollection();
        $values = $collection->getValues();

        $this->assertEquals(array(), $values);

        # 2nd call
        $collection = $gauge->getCollection();
        $values = $collection->getValues();

        $this->assertEquals(array(), $values);

        # 3rd call
        $collection = $gauge->getCollection();
        $values = $collection->getValues();

        $this->assertEquals(array(), $values);

        # 4th call
        $collection = $gauge->getCollection();
        $values = $collection->getValues();

        $this->assertEquals(array(
            'total.value' => 20,
            'used.value' => 15,
            'free.value' => 5
        ), $values);
    }
}