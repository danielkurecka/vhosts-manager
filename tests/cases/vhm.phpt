<?php

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

exec(__DIR__ . '/../../bin/vhm', $output, $code);
Assert::same(0, $code);
