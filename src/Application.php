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
		$host = $input->getArgument('host-name');
		$docRoot = $input->getArgument('document-root');

		if (is_file($this->getConfFileName($host))) {
			throw new \Exception("Virtual host already exists: " . $this->getConfFileName($host));
		}

		if (!$realPath = realpath($docRoot)) {
			throw new \Exception("Document root '$docRoot' does not exists.");
		}

		$output->writeln("Creating " . $this->getConfFileName($host));
		Helpers::filePutContents($this->getConfFileName($host), $this->getTemplateData($host, $realPath));

		$output->writeln("Creating symlink " . $this->getConfLinkName($host));
		Helpers::symlink($this->getConfFileName($host), $this->getConfLinkName($host));

		$hostsFile = $this->createHostsFile();

		if (!$hostsFile->has(self::LOCAL_IP, $host)) {
			$output->writeln("Adding $host to {$hostsFile->getPath()}");
			$hostsFile->add(self::LOCAL_IP, $host)->save();
		}

		$this->reload($output);
		$output->writeln("Virtual host url: http://$host");
	}


	public function commandRemove(InputInterface $input, OutputInterface $output)
	{
		$host = $input->getArgument('host-name');

		if (!is_file($this->getConfFileName($host))) {
			throw new \Exception("Virtual host does not exists: " . $this->getConfFileName($host));
		}

		if (is_file($this->getConfLinkName($host)) || is_link($this->getConfLinkName($host))) {
			$output->writeln("Removing " . $this->getConfLinkName($host));
			Helpers::unlink($this->getConfLinkName($host));
		}

		$output->writeln("Removing " . $this->getConfFileName($host));
		Helpers::unlink($this->getConfFileName($host));

		$hostsFile = $this->createHostsFile();

		if ($hostsFile->has(self::LOCAL_IP, $host)) {
			$output->writeln("Removing $host from {$hostsFile->getPath()}");
			$hostsFile->remove(self::LOCAL_IP, $host)->save();
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


	private function getConfFileName($host)
	{
		return $this->config->availableDir . "/$host.conf";
	}


	private function getConfLinkName($host)
	{
		return $this->config->enabledDir . "/$host.conf";
	}


	private function getTemplateData($host, $documentRoot)
	{
		$templateFile = __DIR__ . '/../config/template.conf.php';
		if (!is_file($templateFile)) {
			throw new \Exception("Virtual host template '$templateFile' does not exits.");
		}

		ob_start();
		require $templateFile;
		return ob_get_clean();
	}

}
