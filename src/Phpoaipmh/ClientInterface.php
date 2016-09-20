<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 9/20/16
 * Time: 12:29 PM
 */

namespace Phpoaipmh;

use Phpoaipmh\Exception\OaipmhException;
use Phpoaipmh\Model\Record;
use Phpoaipmh\Model\RecordPage;
use Phpoaipmh\Model\RequestParameters;

interface ClientInterface
{
    /**
     * Get date granularity
     *
     * OAI-PMH endpoints define two levels of date granularity (Y-m-d or
     * Y-m-d H:i:sz).  These are usually provided by the Identify verb
     *
     * @param string $endpointUrl  URL of endpoint to detect date granularity
     * @param array  $extraParams  Any extra parameters to send to the Identify URI
     * @return DateGranularity
     */
    public function getDateGranularity($endpointUrl, array $extraParams = []);

    /**
     * @param RequestParameters $requestParameters
     * @return Record
     */
    public function getRecord(RequestParameters $requestParameters);

    /**
     * @param RequestParameters $requestParameters
     * @return Record[]
     * @throws OaipmhException  Thrown in the event an OAI-PMH error is generated
     */
    public function iterateRecords(RequestParameters $requestParameters);

    /**
     * Iterate over pages in a set
     *
     * @param RequestParameters $requestParameters
     * @return RecordPage[]
     * @throws OaipmhException  Thrown in the event an OAI-PMH error is generated
     */
    public function iteratePages(RequestParameters $requestParameters);

    /**
     * Get the total number of records in a set
     *
     * Returns NULL if the number of records is unspecified or unknown
     *
     * @param RequestParameters $requestParameters
     * @return int|null
     * @throws OaipmhException  Thrown in the event an OAI-PMH error is generated
     */
    public function getNumTotalRecords(RequestParameters $requestParameters);
}
