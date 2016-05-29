<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 5/29/2016
 * Time: 13:21
 */
namespace Petrica\StatsdSystem\Model\Process;

class Process
{
    private $pid;

    private $name;

    private $cpu;

    private $memory;

    public function __construct($name, $pid, $cpu, $memory)
    {
        $this->name = $name;
        $this->pid = $pid;
        $this->cpu = $cpu;
        $this->memory = $memory;
    }

    /**
     * @return mixed
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @param mixed $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCpu()
    {
        return $this->cpu;
    }

    /**
     * @param mixed $cpu
     */
    public function setCpu($cpu)
    {
        $this->cpu = $cpu;
    }

    /**
     * @return mixed
     */
    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * @param mixed $memory
     */
    public function setMemory($memory)
    {
        $this->memory = $memory;
    }
}
