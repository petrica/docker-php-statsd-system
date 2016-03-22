<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/22/2016
 * Time: 22:45
 */
namespace Petrica\StatsdSystem\Command;

use Domnikl\Statsd\Client;
use Domnikl\Statsd\Connection\UdpSocket;
use Petrica\StatsdSystem\Gauge\CpuAverageGauge;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyCommand extends Command
{
    /**
     * @var Client
     */
    protected $statsd = null;

    protected function configure()
    {
        $this
            ->setName('statsd:notify:cpu')
            ->setDescription('Collect and send CPU stats to statsd server')
            ->addOption(
                'statsd-host',
                null,
                InputOption::VALUE_OPTIONAL,
                'Statsd server hostname',
                'localhost'
            )
            ->addOption(
                'statsd-port',
                null,
                InputOption::VALUE_OPTIONAL,
                'Statsd server port',
                '8125'
            )
            ->addOption(
                'statsd-namespace',
                null,
                InputOption::VALUE_OPTIONAL,
                'Gauge namespace sent to statsd',
                'system'
            )
            ->addOption(
                'iterations',
                null,
                InputOption::VALUE_OPTIONAL,
                'The number of times the job collects stats and sends them to statsd server before exiting.',
                60
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Output CPU details to console. Do not send the stats to statsd server.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $isDryRun = $input->getOption('dry-run');

        if ($isDryRun) {
            $output->writeln('Dry run, will only display the average load for the last minute.');

            $output->writeln(print_r($this->getMetrics(), true));

            return;
        }

        $statsd = $this->getStatsd($input);

        $iterations = $input->getOption('iterations');
        $count = 0;
        while($count < $iterations)
        {
            $metrics = $this->getMetrics();
            foreach($metrics as $key => $value) {
                $statsd->gauge($key, $value);
            }

            $output->writeln(print_r($this->getMetrics(), true));
            sleep(1);
        }
    }

    /**
     * Statically cache statsd client and return a statsd client
     *
     * @param InputInterface $input
     * @return Client
     */
    protected function getStatsd(InputInterface $input)
    {
        if (null === $this->statsd) {
            $connection = new UdpSocket(
                $input->getOption('statsd-host'),
                $input->getOption('statsd-port')
            );
            $this->statsd = new Client(
                $connection,
                $input->getOption('statsd-namespace')
            );
        }

        return $this->statsd;
    }

    /**
     * Collect current metrics
     *
     * @return array
     */
    protected function getMetrics()
    {
        $cpuLoadGauge = new CpuAverageGauge();

        $stats = array(
            'cpu.load.average' => $cpuLoadGauge->getGauge()
        );

        return $stats;
    }
}