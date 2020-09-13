<?php
declare(strict_types=1);

namespace Phpoaipmh\Fixture;

use Phpoaipmh\Behavior\RetrieveNodeTrait;

/**
 * Class TestRetrieveNodeFixture
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class RetrieveNodeFixture
{
    use RetrieveNodeTrait;

    protected static function getXMLDocumentName(): string
    {
        return 'Test XML Document';
    }
}
