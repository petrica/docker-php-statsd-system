<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/24/2016
 * Time: 1:17
 */
namespace Petrica\StatsdSystem\Gauge;

use Petrica\StatsdSystem\Collection\ValuesCollection;

class MemoryGauge implements GaugeInterface
{
    const MEMINFO_PATH = '/proc/meminfo';

    /**
     * {@inheritdoc}
     */
    public function getSamplingPeriod()
    {
        return 10;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        $collection = new ValuesCollection();

        $info = $this->getSystemMemoryInfo();

        if (!empty($info) &&
            isset($info['MemTotal']) &&
            isset($info['MemFree'])) {

            $collection->add('total.value', $info['MemTotal']);
            $collection->add('free.value', $info['MemFree']);
            $collection->add('used.value', $this->getUsedMemory($info));
        }

        return $collection;
    }

    /**
     * Only for linux/unix OS
     *
     * @return array
     */
    protected function getSystemMemoryInfo()
    {
        $meminfo = array();

        if (file_exists(static::MEMINFO_PATH)) {
            $data = explode("\n", file_get_contents(static::MEMINFO_PATH));
            $meminfo = array();
            foreach ($data as $line) {
                if (!empty($line)) {
                    list($key, $val) = explode(":", $line);

                    $meminfo[$key] = intval($val);
                }
            }
        }

        return $meminfo;
    }

    /**
     * Return used memory
     *
     * @param $info
     * @return null
     */
    protected function getUsedMemory($info)
    {
        if (isset($info['MemFree'])
            && isset($info['MemTotal'])) {
            return $info['MemTotal'] - $info['MemFree'];
        }

        return null;
    }
}
