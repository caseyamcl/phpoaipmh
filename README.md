PHPOAIMH
========

A PHP OAI-PMH harvester client library
--------------------------------------

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
1. Install via [Composer](http://getcomposer.org/) by including the following in your composer.json file: 
<pre>
    {
        "require": {
            "caseyamcl/Phpoaipmh": "dev-master",
            ...
        }
    }
</pre>
2. Drop the <var>src/</var> folder into your application and use a PSR-0 autoloader to include the files.


Usage
-----
Setup a new endpoint:
<pre>
    $myEndpoint = new \Phpoaipmh\Endpoint('http://some.service.com/oai');
</pre>

Getting basic information:
<pre>

    //Result will be a SimpleXMLElement object
    $result = $myEndpoint->identify();
    var_dump($result);

    //Results will be an array of SimpleXMLElement objects
    $results = $myEndpoint->listMetadataFormats();
    foreach($results as $item) {
        var_dump($item);
    }

</pre>

Getting lists of records:
<pre>
    //recs will be a Phpoaipmh\ResponseList object
    $recs = $myEndpoint->listRecords('someMetaDataFormat');

    //nextItem will continue retrieving items even across HTTP requests.
    //You can keep running this loop through the *entire* collection you
    //are harvesting.  It returns a SimpleXMLElement object, or false when
    //there are no more records.
    while($rec = $recs->nextItem()) {
        var_dump($rec);
    }
</pre>


Handling Results
----------------
Depending on the verb used, the library will send back one of three types of
variables:

* For <code>identify</code> and <code>getRecord</code>, a <var>SimpleXMLElement</var> object is returned
* For <code>listMetadataFormats</code> and <code>listSets</code>, an array of <var>SimpleXMLElement</var> objects is returned
* For <code>listIdentifiers</code> and <code>listRecords</code>, a <var>Phpoaipmh\ResponseList</var> object is returned

The <var>ResponseList</var> object encapsulates the logic needed to paginate through a large set of records over multiple HTTP requests.  You can extract a single record at a time from the object by calling the <code>nextItem()</code> method.  The nextItem() method returns a <var>SimpleXMLElement</var> object, or <code>false</code> when there are no more records.


Handling Errors
---------------
* For any HTTP request errors, the library will throw a <code>Phpoaipmh\Http\RequestException</code>
* For any OAI-PMH errors (e.g. invalid verb or missing params), the library will throw a <code>Phpoaipmh\OaipmhRequestException</code>


More Info
---------
For a full list of public API methods, refer to the inline documentation inside of <var>src/Phpoaipmh/Endpoint.php</var>