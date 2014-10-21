PHPOAIPMH Changelog
===================

* v2.0
  - Bumped PHP requirement to v5.4 or newer
  - Updated to Guzzle v5
  - ResponseList object now implements the Iterator interface
  - Renamed `Phpoaipmh\Http` classes to `HttpAdapter` for clarity and sanity
  - Added `getTotalRecordsInCollection` method to `ResponseList`
  - Added `reset` method to `ResponseList`
  - Lots of minor PHPDoc comment improvements
  - Refactored Exception classes to be more sensible
  - `Endpoint` class now uses `DateTime` class instead of strings for temporal parameters
* v1.2.1 - 2014 Oct 21
  - Updated this changelog
* v1.2 - 2014 Oct 21
  - Cleaned up API comments
  - Fixed bug when the client class is instantiated without passing an HTTP Client
  - Added base exception
  - Fixed Guzzle include to only accept <v4.0 in the composer.json
* v1.1 - 2013 Jul 31
  - Fixed bug in GetRecord Endpoint call
  - Added additional tests
  - Added typehinting for request() method in Client class
* v1.0 - Initial Release