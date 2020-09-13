<?php

declare(strict_types=1);

namespace Phpoaipmh\Contract;

use Phpoaipmh\Exception\MalformedResponseException;

/**
 * Record processor interface (processes a record and returns the expected format back)
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
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
