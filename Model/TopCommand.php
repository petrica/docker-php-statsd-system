<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 5/29/2016
 * Time: 0:01
 */
namespace Petrica\StatsdSystem\Model;

use Tivie\Command\Argument;
use Tivie\Command\Command;

/**
 * Class TopCommand
 *
 * Return parsed unix top command information
 *
 * @package Petrica\StatsdTop\Model
 */
class TopCommand
{
    /**
     * Command to run in order to return git tags
     *
     * @var null|Command
     */
    protected $command = null;

    public function __construct()
    {
        $this->command = $this->buildCommand();
    }

    /**
     * Run configured command
     *
     * @return string
     */
    public function run()
    {
        $result = $this->getCommand()->run();

        if ($result->getExitCode() != 0) {
            throw new \RuntimeException(sprintf(
                'Command failed. Exit code %d, output %s',
                $result->getExitCode(),
                $result->getStdErr()));
        }

        return $this->parse($result->getStdOut());
    }

    /**
     * Parse top utility command output
     *
     * Returns an array of lines with the same top columns
     *
     * @param $output
     * @return array
     */
    protected function parse($output)
    {
        $lines = explode(PHP_EOL, $output);
        $stats = array();
        foreach ($lines as $line) {
            $data = explode(' ', $line);
            array_walk($data, 'trim');
            $stats[] = array_values(array_filter($data, function ($value) {
                return strlen($value) > 0;
            }));
        }

        return $stats;
    }

    /**
     * Build commit command
     *
     * @return Command
     * @throws \Tivie\Command\Exception\Exception
     * @throws \Tivie\Command\Exception\InvalidArgumentException
     */
    protected function buildCommand()
    {
        $command = new Command(\Tivie\Command\ESCAPE);
        $command
            ->setCommand('top')
            ->addArgument(new Argument('-b'))
            ->addArgument(new Argument('-n', 1));

        return $command;
    }

    /**
     * @return null|Command
     */
    protected function getCommand()
    {
        return $this->command;
    }
}