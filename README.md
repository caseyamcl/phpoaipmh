PHPOAIPMH
========

A PHP OAI-PMH harvester client library
--------------------------------------

[![Build Status](https://travis-ci.org/caseyamcl/Phpoiapmh.png)](https://travis-ci.org/caseyamcl/Phpoiapmh.png)

This library provides an interface to harvest OAI-PMH metadata
from any [OAI 2.0 compliant endpoint](http://www.openarchives.org/OAI/openarchivesprotocol.html#ListMetadataFormats).

Features:
* PSR-0 thru PSR-2 Compliant
* Composer/Packagist compatible
* Unit-tested
* Swappable HTTP library (in case you want to use Guzzle or something besides CURL)
* Easy-to-use iterator that hides all the HTTP junk necessary to get paginated records


Installation Options
--------------------
**Composer** - Install via [Composer](http://getcomposer.org/) by including the following in your composer.json file: 
 
    {
        "require": {
            "caseyamcl/phpoaipmh": "~1.1",
            ...
        }
    }

**Manually** - Download the source code, drop the `src` folder into your application, and use a PSR-0 autoloader to include the files.


Usage
-----
Setup a new endpoint:

    $client = new \Phpoaipmh\Client('http://some.service.com/oai');
    $myEndpoint = new \Phpoaipmh\Endpoint($client)


Getting basic information:


    //Result will be a SimpleXMLElement object
    $result = $myEndpoint->identify();
    var_dump($result);

    //Results will be an array of SimpleXMLElement objects
    $results = $myEndpoint->listMetadataFormats();
    foreach($results as $item) {
        var_dump($item);
    }


Getting lists of records:

    //recs will be a Phpoaipmh\ResponseList object
    $recs = $myEndpoint->listRecords('someMetaDataFormat');

    //nextItem will continue retrieving items even across HTTP requests.
    //You can keep running this loop through the *entire* collection you
    //are harvesting.  It returns a SimpleXMLElement object, or false when
    //there are no more records.
    while($rec = $recs->nextItem()) {
        var_dump($rec);
    }


Handling Results
----------------
Depending on the verb used, the library will send back one of three types of
variables:

* For `identify` and `getRecord`, a `SimpleXMLElement` object is returned
* For `listMetadataFormats` and `listSets`, an array of `SimpleXMLElement` objects is returned
* For `listIdentifiers` and `listRecords`, a `Phpoaipmh\ResponseList` object is returned

The `ResponseList` object encapsulates the logic needed to paginate through a large set of records over multiple HTTP requests.  You can extract a single record at a time from the object by calling the `nextItem()` method.  The `nextItem()` method returns a `\SimpleXMLElement` object, or `false` when there are no more records.


Handling Errors
---------------
* For any HTTP request errors, the library will throw a `Phpoaipmh\Http\RequestException`
* For any OAI-PMH errors (e.g. invalid verb or missing params), the library will throw a `Phpoaipmh\OaipmhRequestException`



More Info
---------
For a full list of public API methods, refer to the inline documentation inside of `src/Phpoaipmh/Endpoint.php`
