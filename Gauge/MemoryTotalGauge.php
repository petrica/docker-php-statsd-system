<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/24/2016
 * Time: 1:17
 */
namespace Petrica\StatsdSystem\Gauge;

class MemoryTotalGauge extends AbstractMemoryGauge
{
    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        $info = $this->getSystemMemoryInfo();

        if (isset($info['MemTotal'])) {
            return $info['MemTotal'];
        }

        return false;
    }
}
