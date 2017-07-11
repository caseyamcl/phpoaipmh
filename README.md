PHPOAIPMH
=========

A PHP OAI-PMH harvester client library
--------------------------------------

[![Latest Version](https://img.shields.io/github/release/caseyamcl/phpoaipmh.svg?style=flat-square?style=flat-square)](https://github.com/caseyamcl/phpoaipmh/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/caseyamcl/Phpoiapmh.png)](https://travis-ci.org/caseyamcl/Phpoiapmh.png)
[![Total Downloads](https://img.shields.io/packagist/dt/caseyamcl/Phpoaipmh.svg?style=flat-square)](https://packagist.org/packages/caseyamcl/Phpoaipmh)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/caseyamcl/phpoaipmh.svg?style=flat-square)](https://scrutinizer-ci.com/g/caseyamcl/phpoaipmh/)

This library provides an interface to harvest OAI-PMH metadata
from any [OAI 2.0 compliant endpoint](http://www.openarchives.org/OAI/openarchivesprotocol.html#ListMetadataFormats).

Features:
* PSR-0 thru PSR-2 Compliant
* Composer-compatible
* Unit-tested
* Prefers Guzzle for HTTP transport layer, but can fall back to cURL
* Easy, transparent iteration over records

Installation Options
--------------------
Install via [Composer](http://getcomposer.org/) by including the following in your composer.json file: 
 
    {
        "require": {
            "caseyamcl/phpoaipmh": "~3.0",
            "guzzlehttp/guzzle":   "~6.0"
        }
    }

*Note:* Guzzle v6 or newer is strongly recommended.  The library still includes Guzzle v5 support,
but that will be removed in Phpoaipmh v4.  If neither is installed, the library will use cURL.  If
none of these are available, a `\RuntimeExeption` will be thrown.

Alternatively, you can use a different HTTP client library by passing your own
implementation of the `Phpoaipmh\HttpAdapter\HttpAdapterInterface` to the `Phpoaipmh\Client` constructor.


Upgrading from Version 2 to Version 3
-------------------------------------

There are several backwards-incompatible API improvements in version 3.0.  See <UPGRADE.md> for
information about how to upgrade your code to use the new version.


Usage
-----
Setup a new endpoint client:

```php

use Phpoaipmh\Factory;

$client = Factory::client();
$myEndpoint = Factory::endpoint($client);
```

Get basic information:

```php
// Result will be a SimpleXMLElement object
$result = $myEndpoint->identify()->run();
var_dump($result);

// Results will be iterator of SimpleXMLElement objects
$results = $myEndpoint->listMetadataFormats()->run();
foreach($results as $item) {
    var_dump($item);
}
```

Get a lists of records:


```php
// Recs will be an iterator of SimpleXMLElement objects
$recs = $myEndpoint->listRecords->run('someMetaDataFormat');

// The iterator will continue retrieving items across multiple HTTP requests.
// You can keep running this loop through the *entire* collection you
// are harvesting.  All OAI-PMH and HTTP pagination logic is hidden neatly
// behind the API.
foreach($recs as $rec) {
    var_dump($rec);
}
```

Optionally, specify a date/time granularity level to use for date-based queries:

```php
use Phpoaipmh\Client,
    Phpoaipmh\Endpoint,
    Phpoaipmh\DateGranularity;

$client = new Client('http://some.service.com/oai');
$myEndpoint = new Endpoint($client, DateGranularity::dateAndTime());
```

If you do not manually specify the granularity, the library will attempt to 
query and cache it from the `identify` endpoint for the OAI-PMH service.

Handling Results
----------------
Depending on the verb you use, the library will send back either a `SimpleXMLELement`
or an iterator (instance of `\Generator`) to iterate `SimpleXMLElement` objects.

* For `identify` and `getRecord`, a `SimpleXMLElement` object is returned
* For `listMetadataFormats`, `listSets`, `listIdentifiers`, and `listRecords` a `\Generator` is returned

Iterating Pages instead of Records
----------------------------------
If you wish, you can iterate record pages instead of the records themselves:

```
use Phpoaipmh\Factory;

$endpoint = Factory::endpoint('http://endpoint-url.example.org/');
$request  = $endpoint->listRecords();
$pageIterator = $request->getClient()->iteratePages($request->getParameters());

foreach ($pageIterator as $page) {
   foreach ($page->getRecords() as $xmlDocument) {
      // Do something....
   }
}

```

Handling Errors
---------------

This library will throw different exceptions under different circumstances:

* Response body parsing issues (e.g. invalid XML) will generate a `Phpoaipmh\Exception\MalformedResponseException`
* OAI-PMH protocol errors (e.g. invalid verb or missing params) will generate a `Phpoaipmh\Exception\OaipmhException`

All exceptions extend the `Phpoaipmh\Exception\BaseoaipmhException` class.

*Note:* This library does not throw exceptions for HTTP transport errors 
(such as 404 or connect errors).  These are out-of-scope for PHP OAI-PMH, so your
client should handle them separately.  For example, if using Guzzle:

```php

try {
    $client->iteratePages($requestParameters)
} catch (MalformedResponseException $e) {
     // Invalid or no XML returned
} catch (OaiPmhException $e) {
     // An OAI-PMH error was generated
} catch (TransportException $e) {
     // LEFT OFF HERE....
}

```


Customizing Default Request Parameters
--------------------------------------

You can customize the default request parameters (for example, request timeout) for both cURL and Guzzle 
clients by building the adapter objects manually.

To customize cURL parameters, pass them in as an array of key/value items to `CurlAdapter::setCurlOpts()`:

```php
use Phpoaipmh\Client,
    Phpoaipmh\HttpAdapter\CurlAdapter;

$adapter = new CurlAdapter();
$adapter->setCurlOpts([CURLOPT_TIMEOUT => 120]);
$client = new Client('http://some.service.com/oai', $adapter);

$myEndpoint = new Endpoint($client);
```

If you're using Guzzle, you can set the parameters in a similar way:

```php
use Phpoaipmh\Client,
    Phpoaipmh\HttpAdapter\GuzzleAdapter;

$adapter = new GuzzleAdapter();
$adapter->getGuzzleClient()->setDefaultOption('timeout', 120);
$client = new Client('http://some.service.com/oai', $adapter);

$myEndpoint = new Endpoint($client);
```

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
$guzzleAdapter = new \Phpoaipmh\HttpAdapter\GuzzleAdapter();
$guzzleAdapter->getGuzzleClient()->getEmitter()->attach($retrySubscriber);

$client  = new \Phpoaipmh\Client('http://some.service.com/oai', $guzzleAdapter);
```

This will create a client that adheres to the rate-limiting rules enforced by the OAI-PMH record provider.


Sending Arbitrary Query Parameters
----------------------------------

If you wish to send arbitrary HTTP query parameters with your requests, you can
send them via the `\Phpoaipmh\Client` class:

    $client = new \Phpoaipmh\Client('http://some.service.com/oai');
    $client->request('Identify', ['some' => 'extra-param']);

Alternatively, if you wish to send arbitrary parameters while taking advantage of the
convenience of the `\Phpoaipmh\Endpoint` class, you can use the Guzzle event system:

```php
// Create a function or class to add parameters to a request
$addParamsListener = function(\GuzzleHttp\Event\BeforeEvent $event) {
   $req = $event->getRequest();
   $req->getQuery()->add('api_key', 'xyz123');

   // You could do other things to the request here, too, like adding a header..
   $req->addHeader('Some-Header', 'some-header-value');
};

// Manually create a Guzzle HTTP adapter
$guzzleAdapter = new \Phpoaipmh\HttpAdapter\GuzzleAdapter();
$guzzleAdapter->getGuzzleClient()->getEmitter()->on('before', $addParamsListener);

$client  = new \Phpoaipmh\Client('http://some.service.com/oai', $guzzleAdapter);
```

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
* [Sean Blommaert](https://github.com/sblommaert)
* [Valery Buchinsky](https://github.com/vbuc)
* [All Contributors](https://github.com/caseyamcl/phpoaipmh/contributors)

License
-------

MIT License; see [LICENSE](LICENSE) file for details
