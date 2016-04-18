<?php

require __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use VhostsManager\HostsFile;

Assert::same([], HostsFile::decode(''));

Assert::same([
	['', [], '# foo # bar']
], HostsFile::decode('# foo # bar'));

Assert::same([
	['127.0.0.1', ['foo' => 1], '']
], HostsFile::decode('127.0.0.1 foo'));

Assert::same([
	['127.0.0.1', ['foo' => 1, 'bar' => 1, 'baz' => 1], '# foo']
], HostsFile::decode('127.0.0.1 foo bar baz # foo'));

Assert::same([
	['', [], '# foo'],
	['127.0.0.1', ['example.com' => 1], '# bar'],
	['', [], ''],
	['127.0.0.2', ['example.net' => 1, 'example.org' => 1], ''],
	['', [], ''],
], HostsFile::decode('# foo
127.0.0.1 example.com # bar

127.0.0.2 example.net example.org
'));
