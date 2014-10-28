<?php
/**
 * Example CLI application
 *
 * Run with: "php example_cli.php"
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * --------------------------------------------------
 */

namespace Phpoaipmh\Example;

use Phpoaipmh\Client;
use Phpoaipmh\Endpoint;

class Example
{
    private $endpoint;

    private $metadataIterator;

    public function __construct()
    {
        $oaiUrl   = 'http://nsdl.org/oai';
        $client = new Client($oaiUrl);
        $this->endpoint = new Endpoint($client);
    }

    public function getBasicInformation()
    {
        $data = array();

        $xml = $this->endpoint->identify();

        $data['Base URL'] = $xml->Identify->baseURL;
        $data['Repository Name'] = $xml->Identify->repositoryName;
        $data['Administrator'] = $xml->Identify->adminEmail;

        return $data;
    }

    public function getAvailableMetadataFormats()
    {
        $data = array();

        // List identifiers
        if (is_null($this->metadataIterator)) {
            $this->metadataIterator = $this->endpoint->listMetadataFormats();
        }

        $data = array(
            'header' => array('Metadata Prefix', 'Schema', 'Namespace'),
            'rows' => array(),
        );

        foreach ($this->metadataIterator as $rec) {
            $data['rows'][] = array(
                $rec->metadataPrefix,
                $rec->schema,
                $rec->metadataNamespace
            );
        }

        return $data;
    }

    public function getRecords()
    {
        $data = array();

        // List identifiers
        if (empty($this->metadataIterator)) {
            $this->metadataIterator = $this->endpoint->listMetadataFormats();
        } else {
            $this->metadataIterator->rewind(); // rewind the iterator
        }

        // Auto-determine a metadata prefix to use for getting records
        $mdPrefix = (string) $this->metadataIterator->current()->metadataPrefix;

        $data = array(
            'header' => array('Identifier', 'Title', 'usageDataResourceURL'),
            'rows' => array(),
        );

        // Iterate
        $recordIterator = $this->endpoint->listRecords($mdPrefix);
        for ($i = 0; $i < 10; $i++) {
            $rec = $recordIterator->next();
            $data['rows'][] = array(
                $rec->header->identifier,
                // Truncate title
                substr($rec->metadata->commParadata->paradataTitle->string->__toString(), 0, 30),
                $rec->metadata->commParadata->usageDataResourceURL
            );
        }

        return $data;
    }

    public function tryAnException()
    {
        try {
            $iterator = $this->endpoint->listRecords('foobardoesnotexist');
            $iterator->current();
        } catch (\Phpoaipmh\Exception\OaipmhException $e) {
            throw $e;
        }
    }
}
