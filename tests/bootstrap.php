<?php

require __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();
Tester\Dumper::$maxPathSegments = 0; // show full paths in call stack

define('TEMP_DIR', __DIR__. '/tmp/' . getmypid());
@mkdir(dirname(TEMP_DIR));
Tester\Helpers::purge(TEMP_DIR);

function createConfig()
{
	$dir = TEMP_DIR;
	$content = <<<"EOT"
availableDir = $dir/sites-available
enabledDir = $dir/sites-enabled
hostsFile = $dir/hosts
reloadCommand = 'true'
EOT;
	$config = new VhostsManager\Config(Tester\FileMock::create($content));
	mkdir($config->availableDir);
	mkdir($config->enabledDir);
	touch($config->hostsFile);
	return $config;
}
