<?php

namespace m50\PsalmJsonToJunit;

use DOMDocument;
use DOMElement;
use Exception;

class JsonConverter
{
    /** @var array $data */
    private $data;

    /**
     * JsonProcessor constructor.
     *
     * @param string $json
     */
    public function __construct(string $json)
    {
        /** @var mixed $data */
        $data = json_decode($json, true);

        if (is_array($data)) {
            $this->processInput($data);
        } else {
            throw new Exception('Undecodable format for json.');
        }
    }

    /**
     * Generate the XML output.
     *
     * @return string
     */
    public function getXML(): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $schema = 'https://raw.githubusercontent.com/junit-team/'.
            'junit5/r5.5.1/platform-tests/src/test/resources/jenkins-junit.xsd';

        /** @var array $totals */
        $totals = $this->getTotals();

        $suites = $dom->createElement('testsuites');
        $testsuite = $dom->createElement('testsuite');
        $testsuite->setAttribute('failures', (string) $totals['errors']);
        $testsuite->setAttribute('warnings', (string) $totals['warnings']);
        $testsuite->setAttribute('name', 'psalm');
        $testsuite->setAttribute('tests', (string) $totals['tests']);
        $testsuite->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $testsuite->setAttribute('xsi:noNamespaceSchemaLocation', $schema);
        $suites->appendChild($testsuite);
        $dom->appendChild($suites);

        foreach ($this->data as $file => $report) {
            $this->createTestCase($dom, $testsuite, $file, $report);
        }

        return $dom->saveXML();
    }

    private function createTestCase(DOMDocument $dom, DOMElement $parent, string $file, array $report): void
    {
        $totalTests = $report['errors'] + $report['warnings'];
        if ($totalTests < 1) {
            $totalTests = 1;
        }

        $testcase = $dom->createElement('testcase');
        $testcase->setAttribute('name', $file);
        $testcase->setAttribute('file', $file);
        $testcase->setAttribute('tests', (string) $totalTests);
        $testcase->setAttribute('failures', (string) $report['errors']);
        $testcase->setAttribute('warnings', (string) $report['warnings']);

        $failuresByType = $this->groupByType($report['failures']);
        foreach ($failuresByType as $type => $data) {
            foreach ($data as $d) {
                $failure = $dom->createElement('failure');
                $failure->setAttribute('type', $type);
                $failure->nodeValue = json_encode($d, JSON_PRETTY_PRINT);

                $testcase->appendChild($failure);
            }
        }
        $parent->appendChild($testcase);
    }

    private function processInput(array $data): void
    {
        $ndata = [];

        foreach ($data as $error) {
            $fname = $error['file_name'];
            if (!isset($ndata[$fname])) {
                $ndata[$fname] = [
                    'errors'   => $error['severity'] === 'error' ? 1 : 0,
                    'warnings' => $error['severity'] !== 'error' ? 1 : 0,
                    'failures' => [
                        $this->createFailure($error),
                    ],
                ];
            } else {
                if ($error['severity'] == 'error') {
                    $ndata[$fname]['errors']++;
                } else {
                    $ndata[$fname]['warnings']++;
                }
                $ndata[$fname]['failures'][] = $this->createFailure($error);
            }
        }

        $this->data = $ndata;
    }

    private function createFailure(array $error): array
    {
        return [
            'type' => $error['type'],
            'data' => [
                'message'       => $error['message'],
                'type'          => $error['type'],
                'snippet'       => $error['snippet'],
                'selected_text' => $error['selected_text'],
                'line'          => $error['line_from'],
                'column_from'   => $error['column_from'],
                'column_to'     => $error['column_to'],
            ],
        ];
    }

    private function getTotals(): array
    {
        $totals = [
            'errors'   => 0,
            'warnings' => 0,
            'tests'    => 0,
        ];

        foreach ($this->data as $file => $error) {
            $totals['errors'] += $error['errors'];
            $totals['warnings'] += $error['warnings'];
            $totals['tests']++;
        }

        return $totals;
    }

    private function groupByType(array $failures): array
    {
        $nfailures = [];

        /** @var array $failure */
        foreach ($failures as $failure) {
            $nfailures[$failure['type']][] = $failure['data'];
        }

        return $nfailures;
    }
}
