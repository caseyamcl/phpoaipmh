PHPOAIPMH Changelog
===================

## v2.2 - 2015-03-02

### Fixed
  - Fixed Deprecated warning bug for `until` in `Endpoint` class (thanks @igor-kamil)
  - Fixed date/time format in new `Granularity` helper (thanks @scheb)
  
### Changed
  - Improved error handling; if well-formed OAI-PMH errors accompany HTTP error codes, a `OaiPmhException` is thrown instead of `HttpException` (thanks @scheb)

## v2.1 - 2015-02-19

### Fixed
  - Unset the `RecordIterator` resumption token if end of list is reached so that last page is not repeated indefinitely
  - Fixed syntax errors fixed in `Endpoint` class when using date/time constraints
  - Fixed installation instructions in `README.md` (use correct version in Composer)
  
### Added
  - `Endpoint` constructor now accepts optional `granularity` string parameter to specify date/time granularity for record retrieval
  - `Endpoint` will now attempt to automatically fetch date/time granularity from the OAI-PMH provider if not explicitly specified
  - Added `Granularity` helper class to define allowed date/time granularity levels
  - Added `.gitattributes`, `.scrutinizer`, and `CONTRIBUTING.md` files
  - Sending strings to `Endpoint` class for temporal (`from`, `until`) parameters will trigger a deprecation warning
  - Added additional unit tests
  
### Changed
  - Changed test code to use OpenScholarship repository (the previously-used NSDL endpoint is not working)
  - PSR-2 code style improvements
  - Removed `.idea` directory

## v2.0 - 2014-10-22

### Changed
  - Bumped PHP requirement to v5.4 or newer
  - Renamed `Phpoaipmh\Http` classes to `HttpAdapter` for better clarity
  - Updated  Guzzle HTTP adapter to use Guzzle version 5
  - Renamed `ResponseList` class to `RecordIterator` and made it implement the `Iterator` interface
  - Refactored Exception classes to make more sense
  - `Endpoint` class now uses `DateTime` objects instead of strings for temporal parameters
  - Lots of minor PHPDoc comment improvements
  - Improved documentation  
  
### Added
  - Added `getTotalRecordsInCollection` method in `RecordIterator`
  - Added `reset` method to `RecordIterator`
  - Added folder with example scripts
  
### Removed
  - Removed `processList` method in `Endpoint` class, since `RecordIterator` itself is now an iterator
   
## v1.2.1 - 2014-10-21

### Added
  - Added this changelog
  
## v1.2 - 2014-10-21

### Fixed
  - Fixed bug when the client class is instantiated without passing an HTTP Client
  - Fixed Guzzle include to only accept <v4.0 in the composer.json
  
### Added
  - Cleaned up API comments
  - Added base exception
  
## v1.1 - 2013-07-31

### Fixed
  - Fixed bug in GetRecord Endpoint call
  
### Added
  - Added additional tests
  - Added type-hinting for request() method in Client class
  
## v1.0 - Initial Release
