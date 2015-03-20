<?php
namespace BDF2\Resources\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Filesystem\Filesystem;
use BDF2\Resources\PathHelper;

class AssetLoader extends Loader
{
	protected $filelocator;

	protected $assetDirectory;

	protected $pathHelper;

	protected $locator = null;

	protected $filesystem = null;

	protected $publishMode = true;

	protected $publishName = null;

	/**
	 * @param FileLocatorInterface $filelocator helper for searching asset sources
	 * @param string $assetDirectory file system path to directory in which all assets are stored in public access area
	 */
	public function __construct(FileLocatorInterface $filelocator, $assetDirectory, PathHelper $pathHelper) {
		$this->locator = $filelocator;
		$this->assetDirectory = $assetDirectory;
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
		$tmpPath = $this->pathHelper->joinPaths(sys_get_temp_dir(), $resource);
		
		$path = $this->locator->locate($resource);
		
		$fs = $this->getFilesystem();
		$fs->dumpFile($tmpPath, null);
		
		switch ($type)
		{
			case 'css':
				// minify only if it isnt already minified
				if (preg_match('/\.min\.css/', $resource))
				{
					$content = file_get_contents($path);
				}
				else {
					$content = \CssMin::minify(file_get_contents($path));
				}
				break;
			default:
				$content = file_get_contents($path);
		}
		
		file_put_contents($tmpPath, $content);

		if ($this->publishMode && $resource !== null)
		{
			$fs->copy($tmpPath, $publishPath);
		}

		return $tmpPath;
	}

	public function supports($resource, $type = null) {
		return true;
	}

	public function unload($resource, $type = null) {
		$assetsPath = $this->pathHelper->joinPaths($this->assetDirectory, $resource);

		$this->getFilesystem()->remove($assetsPath);
	}

}
