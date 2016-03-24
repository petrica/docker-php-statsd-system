# PHP statsd system metric collector

[![Build Status](https://travis-ci.org/petrica/php-statsd-system.svg?branch=master)](https://travis-ci.org/petrica/php-statsd-system)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/petrica/php-statsd-system/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/petrica/php-statsd-system/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/petrica/php-statsd-system/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/petrica/php-statsd-system/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/petrica/statsd-system/v/stable)](https://packagist.org/packages/petrica/statsd-system)
[![Total Downloads](https://poser.pugx.org/petrica/statsd-system/downloads)](https://packagist.org/packages/petrica/statsd-system)
[![Latest Unstable Version](https://poser.pugx.org/petrica/statsd-system/v/unstable)](https://packagist.org/packages/petrica/statsd-system)
[![License](https://poser.pugx.org/petrica/statsd-system/license)](https://packagist.org/packages/petrica/statsd-system)

System metrics collector for statsd written in PHP.

## Install using composer

```bash
composer require petrica/statsd-system dev-master
```

## Run with

```bash
vendor/bin/statsd-console statsd:notify --verbose CpuAverageGauge,MemoryFreeGauge,MemoryTotalGauge,MemoryUsedGauge
```

In progress!
