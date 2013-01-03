<?php

$checkFiles['autoload'] = __DIR__.'/../vendor/autoload.php';
$checkFiles[] = __DIR__.'/../vendor/mockery/mockery/library/Mockery.php';

foreach($checkFiles as $file) {

    if ( ! file_exists($file)) {
        die('Install dependencies with --dev option to run test suite (# composer.phar install --dev)' . "\n");
    }
}

//Away we go
$autoload = require_once $checkFiles['autoload'];

/* EOF: bootstrap.php */