<?php

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Tester\Assert;
use VhostsManager\Application;


require __DIR__ . '/../bootstrap.php';

$config = createConfig();
$app = new Application($config);

$addCommand = $app->find('add');
$addCommand->run(new ArrayInput([
	'command' => $addCommand->getName(),
	'host-name' => 'foo.local',
	'document-root' => TEMP_DIR,
]), new NullOutput());

$removeCommand = $app->find('remove');
$commandTester = new CommandTester($removeCommand);
$commandTester->execute([
	'command' => $removeCommand->getName(),
	'host-name' => 'foo.local',
]);

Assert::false(is_file($config->availableDir . '/foo.local.conf'));
Assert::false(is_link($config->enabledDir . '/foo.local.conf'));
Assert::matchFile($config->hostsFile, '');

Assert::same(<<<"EOT"
Removing $config->enabledDir/foo.local.conf
Removing $config->availableDir/foo.local.conf
Removing foo.local from $config->hostsFile
Reloading apache configuration

EOT
	, $commandTester->getDisplay());
