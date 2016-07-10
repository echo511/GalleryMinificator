<?php

namespace Echo511\GalleryMinificator;

use LogicException;
use Nette\Utils\Strings;

/**
 * @author Nikolas Tsiongas <ntsiongas@gmail.com>
 */
class Path
{

	public static function upperDir($path)
	{
		preg_match('@(.*/)[^/]+@', $path, $matches);
		return $matches[1];
	}

	public static function isAbsolute($path)
	{
		return Strings::startsWith($path, '/');
	}

	public static function resolveFullPath($path, $workingDir = __DIR__)
	{
		if (self::isAbsolute($path)) {
			return self::normalizePath($path);
		} else {
			return self::normalizePath($workingDir . DIRECTORY_SEPARATOR . $path, DIRECTORY_SEPARATOR);
		}
	}

	public static function normalizePath($path, $separator = DIRECTORY_SEPARATOR)
	{
		// Remove any kind of funky unicode whitespace
		$normalized = preg_replace('#\p{C}+|^\./#u', '', $path);

		// Path remove self referring paths ("/./").
		$normalized = preg_replace('#/\.(?=/)|^\./|\./$#', '', $normalized);

		// Regex for resolving relative paths
		$regex = '#\/*[^/\.]+/\.\.#Uu';

		while (preg_match($regex, $normalized)) {
			$normalized = preg_replace($regex, '', $normalized);
		}

		if (preg_match('#/\.{2}|\.{2}/#', $normalized)) {
			throw new LogicException('Path is outside of the defined root, path: [' . $path . '], resolved: [' . $normalized . ']');
		}
		
		$normalized = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $normalized);

		return $normalized;
	}

}
