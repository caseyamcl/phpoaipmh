PHPOAIPMH Changelog
===================

* v2.0
  - Bumped PHP requirement to v5.4 or newer
  - Renamed `Phpoaipmh\Http` classes to `HttpAdapter` for better clarity
  - Updated  Guzzle HTTP adapter to use Guzzle version 5
  - Renamed `ResponseList` class to `RecordIterator` and made it implement the `Iterator` interface
  - Added `getTotalRecordsInCollection` method in `RecordIterator`
  - Added `reset` method to `RecordIterator`
  - Refactored Exception classes to make more sense
  - `Endpoint` class now uses `DateTime` objects instead of strings for temporal parameters
  - Removed `processList` method in `Endpoint` class, since `RecordIterator` itself is now an iterator
  - Lots of minor PHPDoc comment improvements
  - Improved documentation
  - Added folder with example scripts 
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