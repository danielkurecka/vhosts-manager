<?php

require __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use Tester\FileMock;
use VhostsManager\HostsFile;

$hostsFile = new HostsFile(FileMock::create(''));
Assert::false($hostsFile->has('127.0.0.1', 'foo'));

$hostsFile = new HostsFile(FileMock::create('127.0.0.1 foo'));
Assert::true($hostsFile->has('127.0.0.1', 'foo'));

$hostsFile = new HostsFile(FileMock::create('127.0.0.1 foo bar baz'));
Assert::true($hostsFile->has('127.0.0.1', 'foo'));
Assert::true($hostsFile->has('127.0.0.1', 'bar'));
Assert::true($hostsFile->has('127.0.0.1', 'baz'));
