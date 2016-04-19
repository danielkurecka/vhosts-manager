<?php

use Symfony\Component\Console\Tester\CommandTester;
use Tester\Assert;
use VhostsManager\Application;

require __DIR__ . '/../bootstrap.php';

$config = createConfig();
$app = new Application($config);
$command = $app->find('add');

$commandTester = new CommandTester($command);
$commandTester->execute([
	'command' => $command->getName(),
	'host-name' => 'foo.local',
	'document-root' => TEMP_DIR,
]);

Assert::exception(function () use ($commandTester, $command) {
	$commandTester->execute([
		'command' => $command->getName(),
		'host-name' => 'foo.local',
		'document-root' => TEMP_DIR,
	]);
}, 'Exception', "Virtual host already exists: $config->availableDir/foo.local.conf");
