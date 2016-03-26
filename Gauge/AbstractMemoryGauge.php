<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/24/2016
 * Time: 1:17
 */
namespace Petrica\StatsdSystem\Gauge;

abstract class AbstractMemoryGauge implements GaugeInterface
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
}
