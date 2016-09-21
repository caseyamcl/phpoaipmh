<?php

namespace Phpoaipmh\Model;

use Phpoaipmh\Exception\MalformedResponseException;
use Phpoaipmh\DateGranularity;

/**
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class RecordPage
{
    /**
     * @var array
     */
    private static $nodeNameMappings = [
        'ListMetadataFormats' => 'metadataFormat',
        'ListSets'            => 'set',
        'ListIdentifiers'     => 'header',
        'ListRecords'         => 'record'
    ];

    /**
     * @var Record[]
     */
    private $records;

    /**
     * @var RequestParameters
     */
    private $requestParameters;

    /**
     * @var PaginationInfo
     */
    private $paginationInfo;

    /**
     * Build object from raw XML
     *
     * @param \SimpleXMLElement $pageXml
     * @param RequestParameters $params
     * @param DateGranularity   $dateGranularity
     * @return RecordPage|static
     */
    public static function buildFromRawXml(\SimpleXMLElement $pageXml, RequestParameters $params, DateGranularity $dateGranularity)
    {
        // Parameters and node name mapping
        $verb     = $params->getVerb();
        $nodeName = static::$nodeNameMappings[$verb];

        //Result format error?
        if (! isset($pageXml->$verb->$nodeName)) {
            throw new MalformedResponseException(sprintf("Expected XML element list '%s' missing for verb '%s'", $nodeName, $verb));
        }

        // Set default pagination parameters
        $resumptionToken     = '';
        $completeRecordCount = null;
        $expirationDate      = null;

        // Derive pagination info for this page
        if (isset($pageXml->$verb->resumptionToken)) {
            $resumptionToken = (string) $pageXml->$verb->resumptionToken;

            if (isset($pageXml->$verb->resumptionToken['completeListSize'])) {
                $completeRecordCount = (int) $pageXml->$verb->resumptionToken['completeListSize'];
            }
            if (isset($pageXml->$verb->resumptionToken['expirationDate'])) {
                $expirationDateString = $pageXml->$verb->resumptionToken['expirationDate'];
                $expirationDate = $dateGranularity->createDateTimeObject($expirationDateString);
            }
        }
        $paginationInfo = new PaginationInfo($resumptionToken, $completeRecordCount, $expirationDate);

        // Build records
        $records = [];
        foreach ($pageXml->$verb->$nodeName as $node) {
            $records[] = $node;
        }

        // Build new object
        return new static($records, $paginationInfo, $params);
    }

    /**
     * RecordPage constructor.
     *
     * @param \Traversable|array|\SimpleXMLElement[] $records Record objects
     * @param PaginationInfo                         $paginationInfo
     * @param RequestParameters                      $params  OAI-PMH Request Parameters
     */
    public function __construct($records, PaginationInfo $paginationInfo, RequestParameters $params)
    {
        $this->records           = $records;
        $this->requestParameters = $params;
        $this->paginationInfo    = $paginationInfo;
    }

    /**
     * @return \SimpleXMLElement[]
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @return RequestParameters
     */
    public function getRequestParameters()
    {
        return $this->requestParameters;
    }

    /**
     * @return PaginationInfo
     */
    public function getPaginationInfo()
    {
        return $this->paginationInfo;
    }

    /**
     * @return int
     */
    public function countPageRecords()
    {
        return (is_array($this->records) OR $this->records instanceOf \Countable)
            ? count($this->records)
            : count(iterator_to_array($this->records));
    }
}
