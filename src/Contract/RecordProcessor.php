<?php

declare(strict_types=1);

namespace Phpoaipmh\Contract;

use Phpoaipmh\Exception\MalformedResponseException;

interface RecordProcessor
{
    /**
     * Process a record and return expected format back
     *
     * @param string $recordData
     * @return mixed
     * @throws MalformedResponseException  In the case that the record cannot be processed
     */
    public function process(string $recordData);
}