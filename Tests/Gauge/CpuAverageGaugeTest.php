<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/22/2016
 * Time: 23:26
 */
namespace Petrica\StatsdSystem\Tests\Gauge;

use Petrica\StatsdSystem\Gauge\CpuAverageGauge;

class CpuAverageGagugeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test load average
     */
    public function testGetGauge()
    {
        $gauge = $this->getMockBuilder('Petrica\StatsdSystem\Gauge\CpuAverageGauge')
            ->setMethods(array(
                'getLoadAverage'
            ))
            ->getMock();

        $gauge->expects($this->exactly(2))
            ->method('getLoadAverage')
            ->willReturnOnConsecutiveCalls(
                false,
                array(0.5, 1, 1)
            );

        $value = $gauge->getGauge();
        // First value failed
        $this->assertEquals(null, $value);

        $value = $gauge->getGauge();
        // Second call succeeded
        $this->assertEquals(0.5, $value);
    }
}