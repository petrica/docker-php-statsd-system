<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/22/2016
 * Time: 23:01
 */
namespace Petrica\StatsdSystem\Gauge;

interface InterfaceGauge
{
    /**
     * Return a numerical value
     *
     * @return mixed
     */
    public function getGauge();
}