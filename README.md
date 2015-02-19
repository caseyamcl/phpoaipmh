PHPOAIPMH
=========

A PHP OAI-PMH harvester client library
--------------------------------------

[![Latest Version](https://img.shields.io/github/release/caseyamcl/phpoaipmh.svg?style=flat-square?style=flat-square)](https://github.com/caseyamcl/phpoaipmh/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/caseyamcl/Phpoiapmh.png)](https://travis-ci.org/caseyamcl/Phpoiapmh.png)
[![Total Downloads](https://img.shields.io/packagist/dt/caseyamcl/Phpoaipmh.svg?style=flat-square)](https://packagist.org/packages/caseyamcl/Phpoaipmh)

This library provides an interface to harvest OAI-PMH metadata
from any [OAI 2.0 compliant endpoint](http://www.openarchives.org/OAI/openarchivesprotocol.html#ListMetadataFormats).

Features:
* PSR-0 thru PSR-2 Compliant
* Composer-compatible
* Unit-tested
* Prefers Guzzle for HTTP transport layer, but can fall back to cURL
* Easy-to-use iterator that hides all the HTTP junk necessary to get paginated records


Installation Options
--------------------
Install via [Composer](http://getcomposer.org/) by including the following in your composer.json file: 
 
    {
        "require": {
            "caseyamcl/phpoaipmh": "~2.0",
            "guzzlehttp/guzzle":   "~5.0"
        }
    }

Or, drop the `src` folder into your application and use a PSR-0 autoloader to include the files.

*Note:* Guzzle v5.0 or newer is strongly recommended, but if you choose not to use Guzzle, the
library will fall back to using the PHP cURL extension.  If neither is installed, the library will
throw an exception.  Alternatively, you can use a different HTTP client library by passing your own
implementation of the `Phpoaipmh\HttpAdapter\HttpAdapterInterface` to the `Phpoaipmh\Client` constructor.


Upgrading from Version 1 to Version 2
-------------------------------------

There are several backwards-incompatible API improvements in version 2.0.  See UPGRADE.md for
information about how to upgrade your code to use the new version.


Usage
-----
Setup a new endpoint client:

```php
$client = new \Phpoaipmh\Client('http://some.service.com/oai');
$myEndpoint = new \Phpoaipmh\Endpoint($client);
```

Get basic information:

```php
// Result will be a SimpleXMLElement object
$result = $myEndpoint->identify();
var_dump($result);

// Results will be iterator of SimpleXMLElement objects
$results = $myEndpoint->listMetadataFormats();
foreach($results as $item) {
    var_dump($item);
}
```

Get a lists of records:


```php
// Recs will be an iterator of SimpleXMLElement objects
$recs = $myEndpoint->listRecords('someMetaDataFormat');

// The iterator will continue retrieving items across multiple HTTP requests.
// You can keep running this loop through the *entire* collection you
// are harvesting.  All OAI-PMH and HTTP pagination logic is hidden neatly
// behind the iterator API.
foreach($recs as $rec) {
    var_dump($rec);
}
```

Optionally, specify a date/time granularity level to use for date-based queries:

```php
use Phpoaipmh\Client,
    Phpoaipmh\Endpoint,
    Phpoaipmh\Granularity;

$client = new Client('http://some.service.com/oai');
$myEndpoint = new Endpoint($client, Granularity::DATE_AND_TIME);
```

Handling Results
----------------
Depending on the verb you use, the library will send back either a `SimpleXMLELement`
or an iterator containing `SimpleXMLElement` objects.

* For `identify` and `getRecord`, a `SimpleXMLElement` object is returned
* For `listMetadataFormats`, `listSets`, `listIdentifiers`, and `listRecords` a `Phpoaipmh\ResponseIterator` is returned

The `Phpoaipmh\ResponseIterator` object encapsulates the logic to iterate through paginated sets of records.


Handling Errors
---------------

This library will throw different exceptions under different circumstances:

* HTTP request errors will generate a `Phpoaipmh\Exception\HttpException`
* Response body parsing issues (e.g. invalid XML) will generate a `Phpoaipmh\Exception\MalformedResponseException`
* OAI-PMH protocol errors (e.g. invalid verb or missing params) will generate a `Phpoaipmh\Exception\OaipmhException`

All exceptions extend the `Phpoaipmh\Exception\BaseoaipmhException` class.


Dealing with XML Namespaces
---------------------------

Many OAI-PMH XML documents make use of XML Namespaces.  For non-XML experts, it can be confusing to implement
these in PHP.  SitePoint has a brief but excellent [overview of how to use Namespaces in SimpleXML](http://www.sitepoint.com/simplexml-and-namespaces/).


Iterator Metadata
-----------------

The `Phpoaipmh\RecordIterator` iterator contains some helper methods:

* `getNumRequests()` - Returns the number of HTTP requests made thus far
* `getNumRetrieved()` - Returns the number of individual records retrieved
* `getTotalRecordsInCollection()` - Returns the total number of records in the collection
    * *Note* - This number should be treated as an estimate at best.  The number of records
      can change while the records are being retrieved, so it is not guaranteed to be accurate.
      Also, many OAI-PMH endpoints do not provide this information, in which case, this method will
      return `null`.
* `reset()` - Resets the iterator, which will restart the record retrieval from scratch.


Handling 503 `Retry-After` Responses
------------------------------------

Some OAI-PMH endpoints employ rate-limiting so that you can only make X number
of requests in a given time period.  These endpoints will return a `503 Retry-AFter`
HTTP status code if your code generates too many HTTP requests too quickly.

If you have installed [Guzzle](http://guzzlephp.org), then you can use the
[Retry-Subscriber](https://github.com/guzzle/retry-subscriber) to automatically
adhere to the OAI-PMH endpoint rate-limiting rules.

First, make sure you include the retry-subscriber as a dependency in your
`composer.json`:

    require: {
        /* ... */
       "guzzlehttp/retry-subscriber": "~2.0"
    }
    
Then, when loading the Phpoaipmh libraries, instantiate the Guzzle adapter
manually, and add the subscriber as indicated in the code below:

```php
// Create a Retry Guzzle Subscriber
$retrySubscriber = new \GuzzleHttp\Subscriber\Retry\RetrySubscriber([
    'delay' => function($numRetries, \GuzzleHttp\Event\AbstractTransferEvent $event) {
        $waitSecs = $event->getResponse()->getHeader('Retry-After') ?: '5';
        return ($waitSecs * 1000) + 1000; // wait one second longer than the server said to
    },
    'filter' => \GuzzleHttp\Subscriber\Retry\RetrySubscriber::createStatusFilter(),
]);

// Manually create a Guzzle HTTP adapter
$guzzleAdapter = new \Phpoaipmh\HttpAdapter\Guzzle();
$guzzleAdapter->getGuzzleClient()->getEmitter()->attach($retrySubscriber);

$client  = new \Phpoaipmh\Client('http://some.service.com/oai', $guzzleAdapter);
```

This will create a client that adheres to the rate-limiting rules enforced by the OAI-PMH record provider.


Implementation Tips
-------------------

Harvesting data from a OAI-PMH endpoint can be a time-consuming task, especially when there are lots of records.
Typically, this kind of task is done via a CLI script or background process that can run for a long time.
It is not normally a good idea to make it part of a web request.

Credits
-------

* [Casey McLaughlin](http://github.com/caseyamcl)
* [Christian Scheb](https://github.com/scheb)
* [Matthias Vandermaesen](https://github.com/netsensei)
* [All Contributors](https://github.com/caseyamcl/phpoaipmh/contributors)

License
-------

MIT License; see [LICENSE](LICENSE) file for details
