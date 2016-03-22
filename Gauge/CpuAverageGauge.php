<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/22/2016
 * Time: 23:03
 */
namespace Petrica\StatsdSystem\Gauge;

class CpuAverageGauge implements InterfaceGauge
{
    /**
     * {@inheritdoc}
     */
    public function getGauge()
    {
        $load = sys_getloadavg();

        $value = 0;
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
    public function getLoadAverage()
    {
        return sys_getloadavg();
    }
}