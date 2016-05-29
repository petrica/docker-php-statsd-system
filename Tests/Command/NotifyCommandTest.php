<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/30/2016
 * Time: 23:17
 */
namespace Petrica\StatsdSystem\Tests\Command;

use Domnikl\Statsd\Client;
use Domnikl\Statsd\Connection\UdpSocket;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\vfsStreamWrapper;
use Petrica\StatsdSystem\Command\NotifyCommand;
use Petrica\StatsdSystem\Gauge\CpuAverageGauge;
use Petrica\StatsdSystem\Gauge\MemoryGauge;

class NotifyCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var vfsStreamFile
     */
    private $validFile;

    /**
     * @var vfsStreamFile
     */
    private $invalidFile;

    public function setUp()
    {
        parent::setUp();

        vfsStreamWrapper::register();
        $root = vfsStreamWrapper::setRoot(new vfsStreamDirectory('root'));

        // Create configuration yml
        $yml = <<<'DOC'
gauges:
    cpu:
        class: Petrica\StatsdSystem\Gauge\CpuAverageGauge
        arguments: ~

    memory:
        class: Petrica\StatsdSystem\Gauge\MemoryGauge
DOC;

        $this->validFile = vfsStream::newFile('gauges.yml')
            ->withContent($yml)
            ->at($root);


        $yml = <<<'DOC'
gauges:
    cpu:
        class: Petrica\StatsdSystem\Gauge\CpuAverageGauge
        arguments: ~

    memory:
DOC;
        $this->invalidFile = vfsStream::newFile('wrong_gauges.yml')
            ->withContent($yml)
            ->at($root);
    }

    public function testGetConfig()
    {
        /** @var NotifyCommand $command */
        $command = $this->getMockBuilder('Petrica\StatsdSystem\Command\NotifyCommand')
            ->disableOriginalConstructor()
            ->getMock();

        $class = new \ReflectionClass('Petrica\StatsdSystem\Command\NotifyCommand');
        $getConfig = $class->getMethod('getConfiguration');
        $getConfig->setAccessible(true);

        $input = $this->getInputInterface();

        $input->method('getArgument')
            ->willReturn($this->validFile->url());

        $loader = $getConfig->invokeArgs($command, array($input));

        $this->assertEquals(array(
            'cpu' => array(
                'class' => 'Petrica\StatsdSystem\Gauge\CpuAverageGauge',
                'arguments' => null
            ),
            'memory' => array(
                'class' => 'Petrica\StatsdSystem\Gauge\MemoryGauge'
            )
        ), $loader);

        $input = $this->getInputInterface();

        $input->method('getArgument')
            ->willReturn($this->invalidFile->url());

        // Test wrong configuration exception
        $this->setExpectedException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');
        $getConfig->invokeArgs($command, array($input));
    }

    public function testGetGauges()
    {
        $command = $this->getMockBuilder('Petrica\StatsdSystem\Command\NotifyCommand')
            ->disableOriginalConstructor()
            ->getMock();

        $config = array(
            'cpu' => array(
                'class' => 'Petrica\StatsdSystem\Gauge\CpuAverageGauge',
                'arguments' => array('test' => 1)
            ),
            'memory' => array(
                'class' => 'Petrica\StatsdSystem\Gauge\MemoryGauge'
            )
        );

        $class = new \ReflectionClass('Petrica\StatsdSystem\Command\NotifyCommand');
        $getGauges = $class->getMethod('getGauges');
        $getGauges->setAccessible(true);

        $gauges = $getGauges->invokeArgs($command, array($config));

        $expects = array(
            'cpu' => new CpuAverageGauge(),
            'memory' => new MemoryGauge()
        );

        $this->assertEquals($expects, $gauges);
    }

    public function testGetStatsd()
    {
        $command = $this->getMockBuilder('Petrica\StatsdSystem\Command\NotifyCommand')
            ->disableOriginalConstructor()
            ->getMock();

        $input = $this->getInputInterface();

        $map = array(
            [ 'statsd-host', 'localhost' ],
            [ 'statsd-port', '8125' ],
            [ 'statsd-namespace', 'test' ]
        );

        $input->expects($this->atLeastOnce())
            ->method('getOption')
            ->will($this->returnValueMap($map));

        $class = new \ReflectionClass('Petrica\StatsdSystem\Command\NotifyCommand');
        $getStatsd = $class->getMethod('getStatsd');
        $getStatsd->setAccessible(true);

        $statsd = $getStatsd->invokeArgs($command, array($input));

        $connection = new UdpSocket('localhost', 8125);
        $expects = new Client($connection, 'test');

        $this->assertEquals($expects, $statsd);

        // Not equal host
        $connection = new UdpSocket('localhost1', 8125);
        $expects = new Client($connection, 'test');

        $statsd = $getStatsd->invokeArgs($command, array($input));

        $this->assertNotEquals($expects, $statsd);

        // Not equal port
        $connection = new UdpSocket('localhost', 8126);
        $expects = new Client($connection, 'test');

        $statsd = $getStatsd->invokeArgs($command, array($input));

        $this->assertNotEquals($expects, $statsd);

        // Not equal namespace
        $connection = new UdpSocket('localhost', 8125);
        $expects = new Client($connection, 'test1');

        $statsd = $getStatsd->invokeArgs($command, array($input));

        $this->assertNotEquals($expects, $statsd);
    }

    /**
     * Return a mock object of the inputinterface
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getInputInterface()
    {
        return $this->getMockBuilder('Symfony\Component\Console\Input\ArrayInput')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'getArgument',
                'getOption'
            ))
            ->getMock();
    }
}