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
Assert::matchFile($config->availableDir . '/foo.local.conf', '<VirtualHost *:80>
	ServerName "foo.local"
	DocumentRoot "' . TEMP_DIR . '"

	<Directory "' . TEMP_DIR . '">
		Options Indexes FollowSymLinks
		AllowOverride All
		Require all granted
	</Directory>
</VirtualHost>
'
);

Assert::matchFile($config->hostsFile, $app::LOCAL_IP . ' foo.local');

Assert::same(<<<"EOT"
Creating $config->availableDir/foo.local.conf
Creating symlink $config->enabledDir/foo.local.conf
Adding foo.local to $config->hostsFile
Reloading apache configuration
Virtual host url: http://foo.local

EOT
	, $commandTester->getDisplay());
