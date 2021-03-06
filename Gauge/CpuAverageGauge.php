<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/22/2016
 * Time: 23:03
 */
namespace Petrica\StatsdSystem\Gauge;

use Petrica\StatsdSystem\Collection\ValuesCollection;

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
     * {@inheritdoc}
     */
    public function getCollection()
    {
        $collection = new ValuesCollection();

        $load = $this->getLoadAverage();

        $value = null;
        if (!empty($load) && isset($load[0])) {
            $value = $load[0];
        }

        return $collection->add('load.average', $value);
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
