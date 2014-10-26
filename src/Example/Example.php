<?php

/**
 * PHPOAIPMH Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/phpoaipmh
 * @version 2.0
 * @package caseyamcl/phpoaipmh
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

/**
 * Example CLI application
 *
 * Run with: "php example_cli.php"
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * --------------------------------------------------
 */

// Params
$oaiUrl   = 'http://nsdl.org/oai';

// ----------------------------------------

// Check CLI
if (php_sapi_name() !== 'cli') {
    die("This example script can be run only from the command-line");
}

// Include autoloader and libraries
if ( ! file_exists(__DIR__ . '/../vendor/symfony/console')) {
    die("Cannot run example without development dependencies (run `composer install --dev`)");
}
require_once(__DIR__ . '/../vendor/autoload.php');

// ----------------------------------------

// Create client and endpoint
$client   = new Phpoaipmh\Client($oaiUrl);
$endpoint = new Phpoaipmh\Endpoint($client);

// Get basic information about the endpoint
$xml = $endpoint->identify();

// Print basic information
echo "\nBasic Information\n========================";
echo "\nURL: " . $oaiUrl;
echo "\nName: " . $xml->Identify->repositoryName;
echo "\nAdmin: " . $xml->Identify->adminEmail;
echo "\n";


// List identifiers
$metadataIterator = $endpoint->listMetadataFormats();

// Print metadata formats
echo "\nMetadata Formats\n=========================";
foreach ($metadataIterator as $rec) {
    echo "\nMetadata Prefix: " . $rec->metadataPrefix;
    echo "\nSchema: " . $rec->schema;
    echo "\nNamespace: " . $rec->metadataNamespace;
    echo "\n-------------------------";
}

// Get first 10 records from endpoint
echo "\n\nFirst 10 Record Identifiers\n=========================";

// Auto-determine a metadata prefix to use for getting records
$metadataIterator->rewind(); // rewind the iterator
$mdPrefix = (string) $metadataIterator->current()->metadataPrefix;

// Iterate
$recordIterator = $endpoint->listRecords($mdPrefix);
for ($i = 0; $i < 10; $i++) {
    $rec = $recordIterator->next();
    echo "\n" . $rec->header->identifier;
}

// Try an exception
try {
    $iterator = $endpoint->listRecords('foobardoesnotexist');
    $iterator->current();
}
catch (\Phpoaipmh\Exception\OaipmhException $e) {
    echo "\n\nHere's the exception we expected to get: " . $e->getMessage();
}

// All done
echo "\n";
exit(0);

/* EOF: example_cli.php */
