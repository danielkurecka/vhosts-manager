<?php

namespace VhostsManager;

class HostsFile
{

	private $lines;
	private $path;


	public function __construct($path)
	{
		$this->path = $path;
		$this->lines = self::decode(Helpers::fileGetContents($path));
	}


	public function getPath()
	{
		return $this->path;
	}


	public function has($ip, $host)
	{
		$line = $this->findLineWithGivenIp($ip);
		return $line !== FALSE && isset($this->lines[$line][1][$host]);
	}


	public function add($ip, $host)
	{
		if ($this->has($ip, $host)) {
			return $this;
		}

		$line = $this->findLineWithGivenIp($ip);

		if ($line !== FALSE) {
			$this->lines[$line][1][$host] = 1;
		} else {
			$this->lines[] = [$ip, [$host => 1], ''];
		}
		return $this;
	}


	public function remove($ip, $host)
	{
		if (!$this->has($ip, $host)) {
			return $this;
		}

		$line = $this->findLineWithGivenIp($ip);
		unset($this->lines[$line][1][$host]);

		if (empty($this->lines[$line][1])) {
			unset($this->lines[$line]);
		}
		return $this;
	}


	public function save($path = NULL)
	{
		Helpers::filePutContents($path ?: $this->path, (string)$this);
		return $this;
	}


	public function __toString()
	{
		return self::encode($this->lines);
	}


	private function findLineWithGivenIp($ip)
	{
		foreach ($this->lines as $index => $line) {
			if ($line[0] === $ip) {
				return $index;
			}
		}

		return FALSE;
	}


	/**
	 * Parses hosts into an array of lines, where each line consists of ip, hosts and comment.
	 * @param string
	 * @return array of [
	 *     [ip, [host1 => 1, host2 => 1, ...], comment],
	 *     ...
	 * ]
	 */
	public static function decode($input)
	{
		if ($input === '') {
			return [];
		}

		$lines = [];

		foreach (explode("\n", $input) as $line) {
			$comment = '';
			$hashPos = strpos($line, '#');

			if ($hashPos !== FALSE) {
				$comment = substr($line, $hashPos);
				$line = substr($line, 0, $hashPos);
			}

			$parts = preg_split('~[\t ]+~', trim($line));
			$ip = (string)array_shift($parts);
			$hosts = array_fill_keys($parts, 1);

			$lines[] = [$ip, $hosts, $comment];
		}

		return $lines;
	}


	/**
	 * Converts parsed hosts into a string.
	 * @param array
	 * @return string
	 */
	public static function encode(array $lines)
	{
		if (!$lines) {
			return '';
		}

		$output = '';
		foreach ($lines as $line) {
			$output .= trim($line[0] . ' ' . implode(' ', array_keys($line[1])) . ' ' . $line[2]) . "\n";
		}

		return trim($output) . "\n";
	}

}
