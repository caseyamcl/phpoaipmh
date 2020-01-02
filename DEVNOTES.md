## Update 2 Jan 2020

- Left off on `Record.php`: `Record::fromDomNode()` method

## To-do for v4:

- Refer to v3 code at: <https://github.com/caseyamcl/phpoaipmh/blob/v3.1/src>

1. Finish implementing data models and their tests.
2. Figure out how I'm going to refactor the `Endpoint` class
   * I don't think I can keep full compatibility with v3, but I can
     create a `EndpointV3Compatible` class or some-such