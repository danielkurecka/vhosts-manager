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

Assert::true(is_file($config->availableDir . '/foo.local.conf'));
Assert::true(is_link($config->enabledDir . '/foo.local.conf'));
Assert::same(
	readlink($config->enabledDir . '/foo.local.conf'),
	$config->availableDir . '/foo.local.conf'
);

Assert::matchFile($config->hostsFile, $app::LOCAL_IP . ' foo.local');

Assert::same(<<<"EOT"
Creating $config->availableDir/foo.local.conf
Creating symlink $config->enabledDir/foo.local.conf
Adding foo.local to $config->hostsFile
Reloading apache configuration

EOT
	, $commandTester->getDisplay());
