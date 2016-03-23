<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/24/2016
 * Time: 1:17
 */
namespace Petrica\StatsdSystem\Gauge;

class MemoryUsedGauge extends AbstractMemoryGauge
{
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return 'memory.used.value';
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        $info = $this->getSystemMemoryInfo();

        if (isset($info['MemFree'])
            && isset($info['MemTotal'])) {
            return $info['MemTotal'] - $info['MemFree'];
        }

        return false;
    }
}