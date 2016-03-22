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
        $gauge = new CpuAverageGauge();
        $gauge->getGauge();


    }
}