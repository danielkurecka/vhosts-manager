<?php

require __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use Tester\FileMock;
use VhostsManager\HostsFile;

$hostsFile = new HostsFile(FileMock::create(''));
$hostsFile->add('127.0.0.1', 'foo');
$hostsFile->save();
Assert::same('127.0.0.1 foo', file_get_contents($hostsFile->getPath()));

$hostsFile = new HostsFile(FileMock::create(''));
$hostsFile->add('127.0.0.1', 'foo');
$hostsFile->save(TEMP_DIR . '/hosts');
Assert::same('127.0.0.1 foo', file_get_contents(TEMP_DIR . '/hosts'));
