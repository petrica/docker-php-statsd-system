<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 5/29/2016
 * Time: 13:47
 */
namespace Petrica\StatsdSystem\Model\Process;

/**
 * Class TopProcessParser
 *
 * Takes out put from top command and parse it into separate process objects
 *
 * @package Petrica\StatsdSystem\Model\Process
 */
class TopProcessParser
{
    private $raw;

    private $processes;

    /**
     * TopProcessParser constructor.
     *
     * @param $raw
     */
    public function __construct($raw)
    {
        $this->raw = $raw;
        $this->processes = array();
    }

    /**
     * Run the parsing process
     */
    public function parse()
    {
        $raw = $this->getRaw();
        $count = count($raw);
        for ($i = 7; $i < $count; $i++) {
            $line = $raw[$i];

            $process = new Process(
                $line[11],
                $line[0],
                floatval($line[8]),
                floatval($line[9])
            );

            $this->processes[] = $process;
        }
    }

    /**
     * Parsed processes
     *
     * @return Process[]
     */
    public function getProcesses()
    {
        return $this->processes;
    }

    /**
     * @return mixed
     */
    public function getRaw()
    {
        return $this->raw;
    }
}