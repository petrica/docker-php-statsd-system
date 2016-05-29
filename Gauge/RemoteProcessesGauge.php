<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 5/29/2016
 * Time: 16:24
 */
namespace Petrica\StatsdSystem\Gauge;

use Petrica\StatsdSystem\Model\RemoteTopCommand;

class RemoteProcessesGauge extends ProcessesGauge
{
    /**
     * SSH Connection string
     *
     * @var string
     */
    protected $sshString;

    /**
     * Path to private key
     *
     * @var string
     */
    protected $sshIdentityFile;

    /**
     * Ssh port
     *
     * @var string
     */
    protected $sshPort;


    public function __construct($sshString, $sshPort = null, $sshIdentityFile = null, $cpuAbove = 5.0, $memoryAbove = 1.0)
    {
        parent::__construct($cpuAbove, $memoryAbove);

        $this->sshString = $sshString;
        $this->sshIdentityFile = $sshIdentityFile;
        $this->sshPort = $sshPort;

        $this->command = new RemoteTopCommand($this->sshString, $this->sshPort, $this->sshIdentityFile);
    }
}