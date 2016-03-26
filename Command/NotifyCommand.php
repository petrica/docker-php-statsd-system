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
use Petrica\StatsdSystem\Config\ConfigLoader;
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

    /**
     * @var null
     */
    protected $config = null;

    protected function configure()
    {
        $this
            ->setName('statsd:notify')
            ->setDescription('Collect and send defined gauges to statsd server')
            ->addArgument(
                'config',
                InputArgument::REQUIRED,
                'Path to yaml configuration file.'
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
        $config = $this->getConfiguration($input);
        $gauges = $this->getGauges($config);
        $statsd = $this->getStatsd($input);

        $iterations = $input->getOption('iterations');
        $count = 0;
        while($count < $iterations)
        {
            foreach($gauges as $path => $gauge) {
                // Sampling period attained for current gauge?
                if (fmod($count, $gauge->getSamplingPeriod()) == 0) {
                    $value = $gauge->getValue();
                    if (null !== $value) {
                        $statsd->gauge($path, $value);
                    }

                    if ($input->getOption('verbose')) {
                        $output->writeln(sprintf('%s: %s', $path, $gauge->getValue()));
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
     * Instantiate selected gauges
     *
     * @param array $config
     * @return GaugeInterface[] array
     */
    protected function getGauges($config)
    {
        if (null === $this->gauges) {
            $this->gauges = array();

            foreach ($config as $path => $details) {
                $className = $details['class'];
                if (class_exists($className)) {
                    $reflection = new \ReflectionClass($className);

                    if ($reflection->getConstructor()) {
                        $this->gauges[$path] = $reflection->newInstanceArgs(
                            $details['arguments'] ? $details['arguments'] : array());
                    }
                    else {
                        $this->gauges[$path] = $reflection->newInstance();
                    }
                }
                else {
                    throw new \RuntimeException(sprintf(
                        'Class does not exists %s'
                    ), $className);
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

    /**
     * Read Yaml configuration file
     * and returns array map
     *
     * @param InputInterface $input
     * @return array
     */
    protected function getConfiguration(InputInterface $input)
    {
        $loader = new ConfigLoader($input->getArgument('config'));

        return $loader->load();
    }
}
