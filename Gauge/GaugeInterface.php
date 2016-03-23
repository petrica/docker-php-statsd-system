<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/22/2016
 * Time: 23:01
 */
namespace Petrica\StatsdSystem\Gauge;

interface GaugeInterface
{
    /**
     * Interval in seconds between each value reported to statsd
     *
     * @return integer
     */
    public function getSamplingPeriod();

    /**
     * Return gauge path as namespace.path.to.gauge
     *
     * @return string
     */
    public function getPath();

    /**
     * Return a numerical value
     *
     * @return mixed
     */
    public function getValue();
}