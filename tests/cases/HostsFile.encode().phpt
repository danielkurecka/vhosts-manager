<?php

require __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use VhostsManager\HostsFile;

Assert::same('', HostsFile::encode([]));

Assert::same("# foo # bar\n", HostsFile::encode([
	['', [], '# foo # bar']
]));

Assert::same("127.0.0.1 foo\n", HostsFile::encode([
	['127.0.0.1', ['foo' => 1], '']
]));

Assert::same("127.0.0.1 foo bar baz # foo\n", HostsFile::encode([
	['127.0.0.1', ['foo' => 1, 'bar' => 1, 'baz' => 1], '# foo']
]));

Assert::same("# foo
127.0.0.1 example.com # bar

127.0.0.2 example.net example.org\n", HostsFile::encode([
	['', [], '# foo'],
	['127.0.0.1', ['example.com' => 1], '# bar'],
	['', [], ''],
	['127.0.0.2', ['example.net' => 1, 'example.org' => 1], ''],
]));
