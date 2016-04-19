<?php

namespace VhostsManager;

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends ConsoleApplication
{

	const LOCAL_IP = '127.0.0.1';

	/** @var Config */
	private $config;


	public function __construct(Config $config)
	{
		parent::__construct('Vhosts Manager', 'dev');
		$this->config = $config;

		$this->register('add')
			->setDescription('Add new virtual host')
			->addArgument('host-name', InputArgument::REQUIRED)
			->addArgument('document-root', InputArgument::REQUIRED)
			->setCode([$this, 'commandAdd']);

		$this->register('remove')
			->setDescription('Remove virtual host')
			->addArgument('host-name', InputArgument::REQUIRED)
			->setCode([$this, 'commandRemove']);
	}


	public function commandAdd(InputInterface $input, OutputInterface $output)
	{
		$site = $input->getArgument('host-name');
		$docRoot = $input->getArgument('document-root');

		if (is_file($this->getConfFileName($site))) {
			throw new \Exception("Site already exists: " . $this->getConfFileName($site));
		}

		if (!$realPath = realpath($docRoot)) {
			throw new \Exception("Document root '$docRoot' does not exists.");
		}

		$output->writeln("Creating " . $this->getConfFileName($site));
		Helpers::filePutContents($this->getConfFileName($site), $this->getTemplateData($site, $realPath));

		$output->writeln("Creating symlink " . $this->getConfLinkName($site));
		Helpers::symlink($this->getConfFileName($site), $this->getConfLinkName($site));
		$hostsFile = $this->createHostsFile();

		if (!$hostsFile->has(self::LOCAL_IP, $site)) {
			$output->writeln("Adding $site to {$hostsFile->getPath()}");
			$hostsFile->add(self::LOCAL_IP, $site)->save();
		}

		$this->reload($output);
		$output->writeln("Site url: http://$site");
	}


	public function commandRemove(InputInterface $input, OutputInterface $output)
	{
		$site = $input->getArgument('host-name');

		if (!is_file($this->getConfFileName($site))) {
			throw new \Exception("Site does not exists: " . $this->getConfFileName($site));
		}

		if (is_file($this->getConfLinkName($site)) || is_link($this->getConfLinkName($site))) {
			$output->writeln("Removing " . $this->getConfLinkName($site));
			Helpers::unlink($this->getConfLinkName($site));
		}

		$output->writeln("Removing " . $this->getConfFileName($site));
		Helpers::unlink($this->getConfFileName($site));

		$hostsFile = $this->createHostsFile();

		if ($hostsFile->has(self::LOCAL_IP, $site)) {
			$output->writeln("Removing $site from {$hostsFile->getPath()}");
			$hostsFile->remove(self::LOCAL_IP, $site)->save();
		}

		$this->reload($output);
	}


	/** @return HostsFile */
	private function createHostsFile()
	{
		return new HostsFile($this->config->hostsFile);
	}


	private function reload(OutputInterface $output)
	{
		$output->writeln("Reloading apache configuration");
		exec($this->config->reloadCommand, $reloadOutput, $code);

		if ($code !== 0) {
			$output->writeln("Reload command finished with exit code $code and outputed:");
			$output->writeln(implode("\n", $reloadOutput));
		}
	}


	private function getConfFileName($site)
	{
		return $this->config->availableDir . "/$site.conf";
	}


	private function getConfLinkName($site)
	{
		return $this->config->enabledDir . "/$site.conf";
	}


	private function getTemplateData($site, $docRoot)
	{
		return <<<"EOT"
<VirtualHost *:80>
	ServerName "$site"
	DocumentRoot "$docRoot"

	<Directory "$docRoot">
		Options Indexes FollowSymLinks
		AllowOverride All
		Require all granted
	</Directory>
</VirtualHost>
EOT;

	}

}
