<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 5/29/2016
 * Time: 0:11
 */
namespace Petrica\StatsdSystem\Tests\Model;

use Petrica\StatsdSystem\Model\TopCommand;

class TopCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnNonZeroExistCode()
    {
        /** @var TopCommand $topCommand */
        $topCommand = $this->getMockBuilder('Petrica\StatsdSystem\Model\TopCommand')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'getCommand'
            ))
            ->getMock();

        $command = $this->getMockBuilder('Tivie\Command')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'run'
            ))
            ->getMock();

        $result = $this->getMockBuilder('Tivie\Command\Result')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'getExitCode',
                'getStdErr'
            ))
            ->getMock();

        /**
         * Simulate command not found
         */
        $result->method('getExitCode')
            ->willReturn(1);
        $result->method('getStdErr')
            ->willReturn('Does not exist.');

        $command->expects($this->once())
            ->method('run')
            ->willReturn($result);

        $topCommand->expects($this->once())
            ->method('getCommand')
            ->willReturn($command);

        $this->setExpectedException(
            '\RuntimeException',
            'Command failed. Exit code 1, output Does not exist.'
        );

        $topCommand->run();
    }
}