<?php
/**
 * Created by PhpStorm.
 * User: Petrica
 * Date: 3/26/2016
 * Time: 1:23
 */
namespace Petrica\StatsdSystem\Config;

use Petrica\StatsdSystem\Config\Definition\ConfigDefinition;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Parser;

class ConfigLoader
{
    /**
     * Filepath to Yaml configuration file
     * @var String
     */
    private $filepath;

    /**
     * ConfigLoader constructor.
     * @param $filepath
     */
    public function __construct($filepath)
    {
        $this->filepath = $filepath;
    }

    /**
     * Process configuration and make sure the configuration format is as expected
     *
     * @return array
     */
    public function load()
    {
        if (file_exists($this->filepath) && ($contents = file_get_contents($this->filepath))) {
            $yaml = new Parser();
            $config = $yaml->parse($contents);

            if (null === $config) {
                $config = array();
            }

            $processor = new Processor();
            $configDefinition = new ConfigDefinition();
            $processedConfiguration = $processor->processConfiguration(
                $configDefinition,
                $config
            );

            if (empty($processedConfiguration)) {
                throw new Exception(
                    'You need to specify at least one gaguge in the configuration file'
                );
            }

            return $processedConfiguration;
        }
        else {
            throw new \RuntimeException(sprintf('Configuration file does not exist or is not accessible %s',
                $this->filepath));
        }

    }
}
