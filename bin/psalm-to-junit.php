#!/usr/bin/env php
<?php

use m50\PsalmJsonToJunit\JsonConverter;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

require __DIR__ . "/../vendor/autoload.php";

$app = new Application('Psalm\'s JSON to JUNIT converter', '1.0.0');

$app->register('convert:json')
    ->setDescription('Convert a json file to a junit output.')
    ->addArgument(
        'input-file',
        InputArgument::REQUIRED,
        'The file you wish to input from. Must be a json file.'
    )
    ->addArgument(
        'output-file',
        InputArgument::OPTIONAL,
        'The file you wish to output to. Will output to STDOUT if not specified.'
    )
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $inputFile = $input->getArgument('input-file');
        if (strpos($inputFile, 'json') === false) {
            $output->writeln("<error>input-file must be a json file.</error>");

            return 1;
        }
        $outputFile = $input->getArgument('output-file');

        $json = file_get_contents($inputFile);
        if ($json === false) {
            $output->writeln("<error>Failed to get contents from $inputFile.</error>");

            return 1;
        }

        $converter = new JsonConverter((string) $json);
        $xml = $converter->getXML();

        if (is_null($outputFile)) {
            $output->write($xml);
        } else {
            file_put_contents($outputFile, $xml);
            $output->writeln('<info>Success.</info>');
        }
    });

$app->run();
