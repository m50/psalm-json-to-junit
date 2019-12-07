<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use m50\PsalmJsonToJunit\JsonConverter;

class JsonConverterTest extends TestCase
{
    /** @test */
    function it_converts_json_correctly()
    {
        $json = file_get_contents(__DIR__ . '/example-input.json');
        $converter = new JsonConverter($json);
        $xml = $converter->getXML();
        $this->assertIsString($xml);
        $this->assertStringContainsString('xsi:noNamespaceSchemaLocation', $xml);
        $this->assertStringEqualsFile(__DIR__ . '/example-output.xml', $xml);
    }
}
