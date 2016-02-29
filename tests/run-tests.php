#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

$tester = escapeshellarg(__DIR__ . '/../vendor/bin/tester');
$testsDir = escapeshellarg(__DIR__ . '/cases');

Tester\Helpers::purge(__DIR__ . '/tmp');
passthru("$tester -o console -p php $testsDir");
