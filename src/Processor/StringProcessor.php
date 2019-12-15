<?php

declare(strict_types=1);

namespace Phpoaipmh\Processor;

class StringProcessor
{
    /**
     * Pass-through processor just returns string
     *
     * @param string $recordData
     * @return string
     */
    public function process(string $recordData)
    {
        return $recordData;
    }
}