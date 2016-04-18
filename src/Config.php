<?php

namespace VhostsManager;

class Config
{

	public $availableDir;
	public $enabledDir;
	public $hostsFile;
	public $reloadCommand;


	public function __construct($fileName)
	{
		$content = Helpers::fileGetContents($fileName);
		$options = parse_ini_string($content);

		if ($options === FALSE) {
			throw new \Exception("Can not parse config file '$fileName'.");
		}

		foreach ($options as $option => $value) {
			if (!property_exists($this, $option)) {
				throw new \Exception("Invalid option '$option' in '$fileName'.");
			}

			$this->$option = $value;
		}
	}

}
