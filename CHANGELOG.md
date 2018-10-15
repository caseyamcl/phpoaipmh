# PHPOAIPMH Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


## [v3.0] - 2018-10-15
### Changed
- Upgraded from PSR-0 to PSR-4 
- Require PHP5.5 or newer (hint: upgrade to PHP7, since PHP5 support is [being deprecated](http://php.net/supported-versions.php))
- Change all `DateTime` references and type-hints to `DateTimeInterface`
- Removed automatic 'short name' class detection in `OaiPmhException`
- Removed `bootstrap.php` from tests directory, in favor of simply using composer autoloader
- Renamed `httpClient` variables and proprties to `httpAdapter` for consistency in naming
- Improved README and documentation

### Added
- Added `composer test` command
- Automatic code coverage reports into `build` directory when  
- Object graph methods: `ClientInterface::getHttpAdapter()` and `RecordIterator::getClient()`
- Added PHP CodeSniffer to auto-correct PSR-2 issues
- Additional tests (`testGetRecordWithNamespaces`)

### Removed
- Removed `Endpoint::setUrl()` and `Client::setUrl()` methods.  URL in client should be immutable.  If you need to 
  change the Endpoint URL, best practice is to create a new Client and Endpoint instance.
- Removed `EndpointCurlTest` that performed HTTP calls against an actual OAI-PMH endpoint (slow and not useful)
- Removed `RecordIterator::getTotalRecordsInCollection()` in favor of `RecordIterator::getTotalRecordCount()`

## [2.6.1] - 2018-03-07
### Changed
 - Added Travis CI tests for PHP 7.2
 - Minor tweaks to `composer.json` dependencies

### Fixed
 - Ensure support for Symfony 4.x libraries in `composer.json`
 - Fixed `RcordIterator` bug for PHP 7.2 (thanks @mengidd)

## [2.6] - 2017-07-29
### Added
 - Added `Endpoint::build($url)` constructor for convenience
 - Added Travis CI tests for PHP 7.0 and 7.1
 - Documentation for `RecordIterator::getTotalRecordCount()`
 - `RecordIterator::getExpirationDate()` method to get record set expiration, if it is supplied by the server
 - `RecordIteratorInterface` and `ClientInterface`, and updated method signatures

### Changed
 - Added Symfony v3 library compatibility in `composer.json`
 - Made Guzzle v6 the default development dependency (v5 still supported)
 - Updated and fixed a whole bunch of stuff in the README file 
 - Changed constructor signatures to use new interfaces instead of concrete classes

### Deprecated
 - Deprecated `RecordIterator::getTotalRecordsInCollection()` in favor of `RecordIterator::getTotalRecordCount()`
 - `Endpoint::setUrl()` and `Client::setUrl()`.  These should be set in constructor and be immutable.  If URL needs
   to change, simply create a new instance.
 
### Removed
 - Removed tests for PHP v5.4 in Travis CI.  It should still work, but is no longer
   officially supported.

## [2.5.1] - 2016-09-17
### Fixed
- Fixed issue where XML namespace settings get lost in the iterator (thanks @vbuch)

## [2.5] - 2016-07-26
### Added
- Added new parameter to `Phpoaipmh\EndpointInterface` and `Phpoaipmh\RecordIterator` to allow passing resumption token (thanks @sblommaert)
  
### Fixed
- Fixed example OAI URL in example implementation code

## [2.4] - 2015-05-18
### Added
- Ability to pass custom request parameters at runtime for cURL adapter
- Documentation for customizing request parameters in README.md

## [2.3] - 2015-05-05
### Added
- Support arbitrary query parameters in `Client` class when making requests (thanks (@scheb)
- New `Phpoaipmh\EndpointInterface` to make decorating the Endpoint class easier
- Scrutinizer support

## [2.2] - 2015-03-02
### Fixed
- Fixed Deprecated warning bug for `until` in `Endpoint` class (thanks @igor-kamil)
- Fixed date/time format in new `Granularity` helper (thanks @scheb)
  
### Changed
- Improved error handling; if well-formed OAI-PMH errors accompany HTTP error codes, a `OaiPmhException` is thrown instead of `HttpException` (thanks @scheb)

## [2.1] - 2015-02-19
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

## [2.0] - 2014-10-22
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
 
## [1.2.1] - 2014-10-21
### Added
- Added this changelog
  
## [1.2] - 2014-10-21
### Fixed
- Fixed bug when the client class is instantiated without passing an HTTP Client
- Fixed Guzzle include to only accept <v4.0 in the composer.json
  
### Added
- Cleaned up API comments
- Added base exception
  
## [1.1] - 2013-07-31
### Fixed
- Fixed bug in GetRecord Endpoint call
  
### Added
- Added additional tests
- Added type-hinting for request() method in Client class
  
## [1.0] - Initial Release
- Hello, World.