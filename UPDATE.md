Upgrading from Version 1.x to 2.x
=================================

* The `Phpoaipmh\Http\Guzzle` and `Phpoaipmh\Http\Curl` classes have been renamed
  to `Phpoaipmh\HttpAdapter\GuzzleAdapter` and `Phpoaipmh\HttpAdapter\CurlAdapter`, respectively.
  Update any references to these classes.
* The `Phpoaipmh\Http\Client` interface has been renamed to `Phpoaipmh\HttpAdapter\HttpAdapterInteraface`.  If you
  are extending this class, use the new name.
* Change any typehinting or references for `Phpoaipmh\ResponseList` to `Phpoaipmh\RecordIterator`.
* Remove any usage of the `Phpoaipmh\Endpoint::processList()` method.  It is no longer necessary, since
  all methods now return an Iterator object by default.  If you absolutely must convert the iterator to an array, 
  use PHP's built-in `iterator_to_array()` function (not recommended, since most OAI-PMH endpoints contain a lot of data).
* If using Guzzle, ensure that you upgrade to Version 5 or later.
* @TODO: Exceptions