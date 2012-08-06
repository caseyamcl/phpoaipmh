PHPOAIMH
========

A PHP OAI-PMH haveseter client library
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
2. Drop the src/ folder into your application and use a PSR-0 autoloader to include the files.


Usage
-----
Setup a new endpoint:
<pre>
    $myEndpoint = new \Phpoaipmh\Endpoint('http://some.service.com/oai');
</pre>

Getting basic information:
<pre>

    //Result will be a SimpleXMLElement
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
    //Recs will be a Phpoaipmh\ResponseList object
    $recs = $myEndpoint->listRecords('someMetaDataFormat');

    //NextItem will continue retrieving items even across HTTP requests,
    //you can conceivably keep running this loop through the *entire*
    //collection you are harvesting.
    while($rec = $recs->nextItem()) {
        var_dump($rec);
    }
</pre>

For a full list of public API methods, refer to the inline documetnation inside
of src/Phpoaipmh/Endpoint.php