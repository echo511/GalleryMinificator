<?php

namespace Echo511\GalleryMinificator;

use ArrayAccess;
use Countable;
use Iterator;
use Nette\Utils\Finder;

/**
 * @author Nikolas Tsiongas <ntsiongas@gmail.com>
 */
class StructureMap implements Countable, Iterator, ArrayAccess
{

	private $map = [];
	private $count;

	public function __construct($sourceDir, $destinationDir, $workingDir)
	{
		$sourceDir = Path::resolveFullPath($sourceDir, $workingDir);
		$destinationDir = Path::resolveFullPath($destinationDir, $workingDir);

		foreach (Finder::findFiles(['*.jpg', '*.JPG', '*.jpeg', '*.JPEG'])
			->from($sourceDir) as $source => $file) {

			$destination = Path::resolveFullPath($destinationDir . DIRECTORY_SEPARATOR .
					str_replace($sourceDir, '', $source)
					, $workingDir);

			$this[$source] = $destination;
		}

		$this->count = count($this->map);
	}

	public function getCount()
	{
		return $this->count;
	}

	// *** Iterator *** //

	public function current()
	{
		return current($this->map);
	}

	public function next()
	{
		next($this->map);
	}

	public function key()
	{
		return key($this->map);
	}

	public function valid()
	{
		return $this->offsetExists($this->key());
	}

	public function rewind()
	{
		reset($this->map);
	}

	// *** Countable *** //

	public function count()
	{
		return $this->getCount();
	}

	// *** ArrayAccess *** //

	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->map);
	}

	public function offsetGet($offset)
	{
		return $this->map[$offset];
	}

	public function offsetSet($offset, $value)
	{
		$this->map[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		unset($this->map[$offset]);
	}

}
