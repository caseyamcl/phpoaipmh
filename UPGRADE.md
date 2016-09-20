Upgrading from Version 2.x to 3.x
=================================

This upgrade provides a new paradigm for the PHP OAI-PMH Library.  Service
classes are now stateless, and generators are used instead of custom iterators.

* Ensure you are running at least `PHP 5.5` or newer.  This library is now 
  tested and runs on PHP7.
* If you are using the Guzzle HTTP adapter, consider upgrading to Guzzle v6.0.
  A Guzzle 5.x adapter is still included for backwards compatibility, but all 
  documentation has been updated for Guzzle 6.
* All calls to `Phpoaipmh\Endpoint` now return request objects.  Call 
  the `run()` method on these to execute the request; e.g. 
  `$endpoint->identify()` becomes `$endpoint->identify()->run()`.
* HTTP transport exceptions are no longer handled by this library.  You must catch 
  these exceptions in your own code.  The reason for this is that HTTP transport 
  exceptions are generally out-of-scope for the OAI-PMH protocol, and this library 
  should be un-opinionated on how to deal HTTP issues.  If you previously caught the 
  `Phpoaipmh\Exception\HttpException` exception, you would now catch either the
  `CurlHttpException` or a Guzzle Exception (depending on the adapter you use).  These
   exceptions do not extend the `Phpoaipmh\Exception\BaseOaipmhException` class.
* Use the `Phpoaipmh\Factory` class if you want to instantiate a `Phpoaipmh\Endpoint` class 
  without manually creating a HTTP adapter or client class.
* If you manually passed a date granularity string into the `Phpoaipmh\Endpoint` class, you should
  now instead pass a `Phpoaipmh\DateGranularity` instance into your `Phpoaipmh\Client` instance.
* The `Phpoaipmh\RecordIterator` has been removed.  Along with it, the following
  methods are no longer available when iterating records:
    * `Phpoaipmh\RecordIterator::getNumRequests()`  - Refer to the <README.md> to see how to derive this information
    * `Phpoaipmh\RecordIterator::getNumRetrieved()` - You track this information in your own libraries
    * `Phpoaipmh\RecordIterator::getTotalRecordsInCollection()` - Use `Phpoaipmh\Client::getNumTotalRecords()` instead 
    * `Phpoaipmh\RecordIterator::nextItem()` - This method has no equivalent in the new version.
* 


Upgrading from Version 1.x to 2.x
=================================

* Usages of `Phpoaipmh\Http\Guzzle` should now instead use `Phpoaipmh\HttpAdapter\GuzzleAdapter`.
* Usages of `Phpoaipmh\Http\Curl` should now instead use  `Phpoaipmh\HttpAdapter\CurlAdapter`.
* Any class that implemets the `Phpoaipmh\Http\Client` interface should now instead implement `Phpoaipmh\HttpAdapter\HttpAdapterInteraface`.
* Change typhints or references for `Phpoaipmh\ResponseList` to `Phpoaipmh\RecordIterator`.
* If using Guzzle, ensure that you upgrade to Version 5 or later.
* Remove any usage of the `Phpoaipmh\Endpoint::processList()` method.  It is no longer necessary, since
  all methods now return an iterator object by default.  
     * If you absolutely must convert the iterator to an array, use PHP's built-in `iterator_to_array()` function.  However,
      this is not recommended, since it may take a very long time to execute.
* Exception class names have changed:
     * `Phpoaipmh\OaipmhRequestException` is now `Phpoaipmh\Exception\OaipmhException`
     * `Phpoaipmh\Client\RequestException` is now `Phpoaipmh\Exception\HttpException`
     * `Phpoaipmh\Exception\OaipmhException` is now `Phpoaipmh\Exception\BaseOaipmhException`
     * Previously, malformed XML would throw a `Phpoaipmh\OaipmhRequestException`.  It now throws a
       `Phpoaipmh\Exception\MalformedResponseException`.
     * All exceptions extend the `Phpoaipmh\Exception\BaseOaipmhException`, so you can use that as a catch-all.
* Added example
