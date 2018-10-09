<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 9/20/16
 * Time: 1:45 PM
 */

namespace Phpoaipmh\Model;

/**
 * Class PaginationInfo
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class PaginationInfo
{
    const UNKNOWN = null;

    /**
     * @var string
     */
    private $resumptionToken;

    /**
     * @var \DateTimeInterface|null
     */
    private $expirationDate;

    /**
     * @var int|null
     */
    private $completeRecordCount;

    /**
     * PaginationInfo constructor.
     *
     * @param string                  $resumptionToken
     * @param int|null                $completeRecordCount
     * @param \DateTimeInterface|null $expirationDate
     */
    public function __construct($resumptionToken = '', $completeRecordCount = null, \DateTimeInterface $expirationDate = null)
    {
        $this->resumptionToken     = $resumptionToken;
        $this->expirationDate      = $expirationDate;
        $this->completeRecordCount = $completeRecordCount;
    }

    /**
     * @return bool
     */
    public function hasResumptionToken()
    {
        return (bool) $this->resumptionToken;
    }

    /**
     * @return bool
     */
    public function hasCompleteRecordCount()
    {
        return $this->completeRecordCount !== null;
    }

    /**
     * @return bool
     */
    public function hasExpirationDate()
    {
        return (bool) $this->expirationDate;
    }

    /**
     * @return string
     */
    public function getResumptionToken()
    {
        return $this->resumptionToken;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @return int|null
     */
    public function getCompleteRecordCount()
    {
        return $this->completeRecordCount;
    }
}
