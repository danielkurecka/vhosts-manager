<?php

use Symfony\Component\Console\Tester\CommandTester;
use Tester\Assert;
use VhostsManager\Application;

require __DIR__ . '/../bootstrap.php';

$config = createConfig();
$app = new Application($config);
$command = $app->find('add');

$commandTester = new CommandTester($command);
Assert::exception(function () use ($commandTester, $command) {
	$commandTester->execute([
		'command' => $command->getName(),
		'host-name' => 'foo.local',
		'document-root' => TEMP_DIR . '/missing',
	]);
}, 'Exception', "Document root '" . TEMP_DIR . "/missing' does not exists.");

Assert::false(is_file($config->availableDir . '/foo.local.conf'));
Assert::false(is_link($config->enabledDir . '/foo.local.conf'));
Assert::matchFile($config->hostsFile, '');
