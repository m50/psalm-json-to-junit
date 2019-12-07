<?php

namespace Tests;

use m50\PsalmJsonToJunit\JsonConverter;
use PHPUnit\Framework\TestCase;

class JsonConverterTest extends TestCase
{
    /** @test */
    function it_converts_json_correctly()
    {
        $json = file_get_contents(__DIR__.'/example-input.json');
        $converter = new JsonConverter($json);
        $xml = $converter->getXML();

        $this->assertStringContainsString('xsi:noNamespaceSchemaLocation', $xml);
        $this->assertStringEqualsFile(__DIR__.'/example-output.xml', $xml);
    }

    /** @test */
    function it_creates_an_empty_report_on_success()
    {
        $json = '[]';
        $converter = new JsonConverter($json);
        $xml = $converter->getXML();

        $this->assertIsString($xml);
        $this->assertStringContainsString('<testcase name="psalm"/>', $xml);
    }
}
