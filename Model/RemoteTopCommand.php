<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 5/29/2016
 * Time: 16:35
 */
namespace Petrica\StatsdSystem\Model;

use Tivie\Command\Argument;
use Tivie\Command\Command;

class RemoteTopCommand extends TopCommand
{
    /**
     * like user@ip
     *
     * @var string
     */
    private $sshString;

    private $sshIdentityFile;

    private $sshPort;

    public function __construct($sshString, $sshPort = null, $sshIdentityFile = null)
    {
        $this->sshString = $sshString;
        $this->sshIdentityFile = $sshIdentityFile;
        $this->sshPort = $sshPort;

        parent::__construct();
    }


    /**
     * @return Command
     */
    protected function buildCommand()
    {
        $command = new Command();
        $command
            ->setCommand('ssh')
            ->addArgument(new Argument($this->sshString));

        if (null !== $this->sshPort) {
            $command->addArgument(new Argument('-p', $this->sshPort));
        }

        if (null !== $this->sshIdentityFile) {
            $command->addArgument(new Argument('-i', $this->sshIdentityFile));
        }

        $command
            ->addArgument(new Argument('top', null, null, false))
            ->addArgument(new Argument('-b'))
            ->addArgument(new Argument('-n', 1));

        return $command;
    }
}