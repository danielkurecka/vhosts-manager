<?php

use Symfony\Component\Console\Tester\CommandTester;
use Tester\Assert;
use VhostsManager\Application;

require __DIR__ . '/../bootstrap.php';

$config = createConfig();
$app = new Application($config);

$command = $app->find('remove');
$commandTester = new CommandTester($command);

Assert::exception(function () use ($commandTester, $command) {
	$commandTester->execute([
		'command' => $command->getName(),
		'host-name' => 'foo.local',
	]);
}, 'Exception', "Site does not exists: $config->availableDir/foo.local.conf");
