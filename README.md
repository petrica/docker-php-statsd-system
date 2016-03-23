# PHP statsd system metric collector

[![Build Status](https://travis-ci.org/petrica/php-statsd-system.svg?branch=master)](https://travis-ci.org/petrica/php-statsd-system)

System metrics collector for statsd written in PHP.

## Install using composer

```bash
composer require petrica/statsd-system
```

## Run with

```bash
vendor/bin/statsd-console statsd:notify --verbose CpuAverageGauge,MemoryFreeGauge,MemoryTotalGauge,MemoryUsedGauge
```

In progress!
