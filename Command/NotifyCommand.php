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
use Petrica\StatsdSystem\Gauge\GaugeInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyCommand extends Command
{
    /**
     * @var Client
     */
    protected $statsd = null;

    /**
     * Instantiated gauges
     *
     * @var array
     */
    protected $gauges = null;

    protected function configure()
    {
        $this
            ->setName('statsd:notify')
            ->setDescription('Collect and send defined gauges to statsd server')
            ->addArgument(
                'gauges-class',
                InputArgument::REQUIRED,
                'PHP class name of gauges separate by comma eg: Petrica\StatsdSystem\Gauge\CpuAverageGauge,Petrica\StatsdSystem\Gauge\MemoryGauge'
            )
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
                3600
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $gauges = $this->getGauges($input);

        $statsd = $this->getStatsd($input);

        $iterations = $input->getOption('iterations');
        $count = 0;
        while($count < $iterations)
        {
            foreach($gauges as $gauge) {
                // Sampling period attained for current gauge?
                if (fmod($count, $gauge->getSamplingPeriod()) == 0) {
                    $value = $gauge->getValue();
                    if (null !== $value) {
                        $statsd->gauge($gauge->getPath(), $value);
                    }

                    if ($input->getOption('verbose')) {
                        $output->writeln(sprintf('%s: %s', $gauge->getPath(), $gauge->getValue()));
                    }
                }
            }

            sleep(1);
            $count ++;
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

    /**
     * Instantiate selected gauges
     *
     * @param InputInterface $input
     * @return GaugeInterface[] array
     */
    public function getGauges(InputInterface $input)
    {
        if (null === $this->gauges) {
            $this->gauges = array();
            $proprietaryPrefix = 'Petrica\StatsdSystem\Gauge';
            $classes = explode(',', $input->getArgument('gauges-class'));

            foreach ($classes as $class) {
                $class = trim($class);
                $proprietaryClass = $proprietaryPrefix . '\\' . $class;

                if (class_exists($proprietaryClass)) {
                    $class = $proprietaryClass;
                }

                if (class_exists($class) &&
                    in_array('Petrica\StatsdSystem\Gauge\GaugeInterface', class_implements($class))) {

                    $this->gauges[] = new $class();
                }
            }

            if (empty($this->gauges)) {
                throw new \RuntimeException(
                    sprintf('No gauges found for provided string %s', $input->getArgument('gauges-class'))
                );
            }
        }

        return $this->gauges;
    }
}