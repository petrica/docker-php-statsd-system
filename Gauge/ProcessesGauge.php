<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 5/28/2016
 * Time: 23:53
 */
namespace Petrica\StatsdSystem\Gauge;

use Petrica\StatsdSystem\Collection\ValuesCollection;
use Petrica\StatsdSystem\Model\Process\Process;
use Petrica\StatsdSystem\Model\Process\TopProcessParser;
use Petrica\StatsdSystem\Model\TopCommand;

class ProcessesGauge implements GaugeInterface
{
    protected $cpuAbove;

    protected $memoryAbove;

    /**
     * @var TopCommand
     */
    protected $command;

    /**
     * ProcessesGauge constructor.
     *
     * Track CPU and memory of processes
     *
     * @param float $cpuAbove
     * @param float $memoryAbove
     */
    public function __construct($cpuAbove = 5.0, $memoryAbove = 1.0, $sshString, $sshIdentityFile)
    {
        $this->cpuAbove = $cpuAbove;
        $this->memoryAbove = $memoryAbove;

        $this->command = new TopCommand();
    }

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
        $processes = $this->getProcesses();

        $cpu = $this->aggregateCpu($processes);
        $cpu = $this->filterAbove($cpu, $this->cpuAbove);

        $memory = $this->aggregateMemory($processes);
        $memory = $this->filterAbove($memory, $this->memoryAbove);

        $collection = new ValuesCollection();

        foreach ($cpu as $name => $value) {
            $collection->add($name . '.cpu.value', $value);
        }

        foreach ($memory as $name => $value) {
            $collection->add($name . '.memory.value', $value);
        }

        return $collection;
    }

    /**
     * Return parsed data for processes
     */
    protected function getProcesses()
    {
        $data = $this->getCommand()->run();

        $parser = new TopProcessParser($data);
        $parser->parse();

        return $parser->getProcesses();
    }

    /**
     * Return only those processes above certail value
     *
     * @param $processes Process[]
     * @return array
     */
    protected function filterAbove($processes, $gate)
    {
        return array_filter($processes, function ($item) use ($gate) {
            return $item >= $gate;
        });
    }

    /**
     * Aggregate CPU for processes with the same name
     *
     * @param $processes Process[]
     */
    protected function aggregateCpu($processes)
    {
        $cpus = array();

        foreach ($processes as $process) {
            if (isset($cpus[$process->getName()])) {
                $cpus[$process->getName()] += $process->getCpu();
            }
            else {
                $cpus[$process->getName()] = $process->getCpu();
            }
        }

        return $cpus;
    }

    /**
     * Aggregate memory for processes with the same name
     *
     * @param $processes Process[]
     * @return array
     */
    protected function aggregateMemory($processes)
    {
        $memory = array();

        foreach ($processes as $process) {
            if (isset($memory[$process->getName()])) {
                $memory[$process->getName()] += $process->getMemory();
            }
            else {
                $memory[$process->getName()] = $process->getMemory();
            }
        }

        return $memory;
    }

    /**
     * Return command object for top utility
     *
     * @return TopCommand
     */
    protected function getCommand()
    {
        return $this->command;
    }

}