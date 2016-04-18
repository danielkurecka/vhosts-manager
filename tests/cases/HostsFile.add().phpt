<?php

require __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use Tester\FileMock;
use VhostsManager\HostsFile;

$hostsFile = new HostsFile(FileMock::create(''));
$hostsFile->add('127.0.0.1', 'foo');
Assert::same("127.0.0.1 foo\n", (string) $hostsFile);

$hostsFile->add('127.0.0.1', 'bar');
Assert::same("127.0.0.1 foo bar\n", (string) $hostsFile);

$hostsFile->add('127.0.0.1', 'foo');
$hostsFile->add('127.0.0.2', 'foo2');
Assert::same(<<<'EOT'
127.0.0.1 foo bar
127.0.0.2 foo2

EOT
, (string) $hostsFile);
