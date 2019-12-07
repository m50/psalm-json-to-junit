<?php

namespace m50\PsalmJsonToJunit;

class JsonProcessor
{
    /** @var array $data */
    private $data;

    /**
     * JsonProcessor constructor.
     * @param string $json
     */
    public function __construct(string $json)
    {
        $this->data = json_decode($json, true);
    }
}
