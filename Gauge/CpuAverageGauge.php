<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/22/2016
 * Time: 23:03
 */
namespace Petrica\StatsdSystem\Gauge;

class CpuAverageGauge implements GaugeInterface
{
    /**
     * Report every 60 seconds
     * Load average is calculated every 1 minute
     *
     * @return int
     */
    public function getSamplingPeriod()
    {
        return 60;
    }

    /**
     * CPU average load path reported to statsd
     *
     * @return string
     */
    public function getPath()
    {
        return 'cpu.load.average';
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        $load = $this->getLoadAverage();

        $value = null;
        if ($load && isset($load[0])) {
            $value = $load[0];
        }

        return $value;
    }

    /**
     * Return CPU load average
     *
     * @return array
     */
    protected function getLoadAverage()
    {
        return sys_getloadavg();
    }
}