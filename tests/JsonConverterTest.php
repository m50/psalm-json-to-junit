<?php

namespace Tests;

use m50\PsalmJsonToJunit\JsonConverter;
use PHPUnit\Framework\TestCase;

class JsonConverterTest extends TestCase
{
    /** @test */
    public function it_converts_json_correctly()
    {
        $json = file_get_contents(__DIR__.'/example-input.json');
        $converter = new JsonConverter($json);
        $xml = $converter->getXML();
        $this->assertIsString($xml);
        $this->assertStringContainsString('xsi:noNamespaceSchemaLocation', $xml);
        $this->assertStringEqualsFile(__DIR__.'/example-output.xml', $xml);
    }
}
