# Psalm JSON to JUNIT

[![Packagist](https://img.shields.io/packagist/dt/m50/psalm-json-to-junit)](https://packagist.org/packages/m50/psalm-json-to-junit)
[![Packagist Version](https://img.shields.io/packagist/v/m50/psalm-json-to-junit)](https://packagist.org/packages/m50/psalm-json-to-junit)
[![Build Status](https://travis-ci.org/m50/psalm-json-to-junit.svg?branch=master)](https://travis-ci.org/m50/psalm-json-to-junit)
[![StyleCI](https://github.styleci.io/repos/226521609/shield?branch=master)](https://github.styleci.io/repos/226521609)
[![GitHub](https://img.shields.io/github/license/m50/psalm-json-to-junit)](LICENSE)

Converts [vimeo/psalm](https://github.com/vimeo/psalm)'s JSON report to a junit XML report.

## Install

Install the package in dev:

```sh
composer install --dev m50/psalm-json-to-junit
```

## Usage

First run psalm generating a json report:

```sh
vendor/bin/psalm --report="report.json"
```

Then, you can run the convertor:

```sh
vendor/bin/psalm-to-junit convert:json report.json report.xml
```

That will generate a new report.xml which you can use in your CI report system.

## Credits

* [Marisa Clardy](https://github.com/m50)

  ![Twitter Follow](https://img.shields.io/twitter/follow/MarisaCodes?label=Follow&style=social)
