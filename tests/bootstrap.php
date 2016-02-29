<?php

require __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();
Tester\Dumper::$maxPathSegments = 0; // show full paths in call stack

define('TEMP_DIR', __DIR__. '/tmp/' . getmypid());
@mkdir(dirname(TEMP_DIR));
Tester\Helpers::purge(TEMP_DIR);
