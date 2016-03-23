<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/24/2016
 * Time: 1:17
 */
namespace Petrica\StatsdSystem\Gauge;

class MemoryFreeGauge extends AbstractMemoryGauge
{
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return 'memory.free.value';
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        $info = $this->getSystemMemoryInfo();

        if (isset($info['MemFree'])) {
            return $info['MemFree'];
        }

        return false;
    }
}