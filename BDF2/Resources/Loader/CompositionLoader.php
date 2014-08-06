<?php
namespace BDF2\Resources\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Filesystem\Filesystem;
use BDF2\Resources\PathHelper;

class CompositionLoader extends Loader
{
	protected $filelocator;

	protected $assetDirectory;

	protected $tmpDirectory;

	protected $pathHelper;

	protected $locator = null;

	protected $filesystem = null;

	protected $publishMode = true;

	protected $publishName = null;

	/**
	 * @param FileLocatorInterface $filelocator helper for searching asset sources
	 * @param string $assetDirectory file system path to directory in which all assets are stored in public access area
	 */
	public function __construct(array $compositions, FileLocatorInterface $filelocator, $assetDirectory, $tmpDirectory, PathHelper $pathHelper) {
		$this->compositions = $compositions;
		$this->locator = $filelocator;
		$this->assetDirectory = $assetDirectory;
		$this->tmpDirectory = $tmpDirectory;
		$this->pathHelper = $pathHelper;
	}

	/**
	 * Whether or not copy resource files to public directory.
	 */
	public function publishMode($mode) {
		$this->publishMode = $mode;

		return $this;
	}

	/**
	 * As it not part of LoaderInterface it shouldnt be public
	 */
	protected function getFilesystem() {
		if ($this->filesystem === null)
		{
			$this->filesyste = new Filesystem();
		}

		return $this->filesyste;
	}

	/**
	 *
	 */
	public function load($resource, $type = null) {
		$publishPath = $this->pathHelper->joinPaths($this->assetDirectory, $resource);
		$tmpPath = $this->pathHelper->joinPaths($this->tmpDirectory, $resource);
		$fs = $this->getFilesystem();
		
		$fs->dumpFile($tmpPath, "/* --- composition: $resource ---*/");
		
		$tmpFile = fopen($tmpPath, "a");
		
		foreach ($this->compositions[$resource] as $asset) {
			$path = $this->locator->locate($asset);
			fwrite($tmpFile, "\n\n/* --- asset: $asset ($path) ---*/\n\n" . file_get_contents($path));
		}
		
		fclose($tmpFile);

		if ($this->publishMode && $resource !== null)
		{
			$fs->copy($tmpPath, $publishPath);
		}

		return $tmpPath;
	}

	public function supports($resource, $type = null) {
		return !empty($this->compositions[$resource]);
	}
	
	public function unload($resource, $type = null) {
		$publishPath = $this->pathHelper->joinPaths($this->assetDirectory, $resource);
		$tmpPath = $this->pathHelper->joinPaths($this->tmpDirectory, $resource);

		$this->fs->remove($publishPath);
		$this->fs->remove($tmpPath);
	}

}
