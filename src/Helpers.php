<?php

namespace VhostsManager;

class Helpers
{

	public static function fileGetContents($fileName)
	{
		return self::invokeSafe(function () use ($fileName) {
			return file_get_contents($fileName);
		});
	}


	public static function filePutContents($fileName, $data)
	{
		return self::invokeSafe(function () use ($fileName, $data) {
			return file_put_contents($fileName, $data);
		});
	}


	public static function unlink($fileName)
	{
		self::invokeSafe(function () use ($fileName) {
			unlink($fileName);
		});
	}


	public static function symlink($target, $link)
	{
		self::invokeSafe(function () use ($target, $link) {
			symlink($target, $link);
		});
	}


	public static function invokeSafe(\Closure $function)
	{
		$error = '';
		set_error_handler(function ($severity, $message) use (&$error) {
			$error = $message;
		});

		$result = $function();
		restore_error_handler();

		if ($error !== '') {
			throw new \Exception($error);
		}

		return $result;
	}

}
