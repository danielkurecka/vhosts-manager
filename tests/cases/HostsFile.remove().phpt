<?php

require __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use Tester\FileMock;
use VhostsManager\HostsFile;

$hostsFile = new HostsFile(FileMock::create('127.0.0.1 foo'));
$hostsFile->remove('127.0.0.1', 'foo');
Assert::same('', (string)$hostsFile);

$hostsFile = new HostsFile(FileMock::create('127.0.0.1 foo bar baz'));
$hostsFile->remove('127.0.0.1', 'foo');
$hostsFile->remove('127.0.0.1', 'bar');
Assert::same("127.0.0.1 baz\n", (string)$hostsFile);
