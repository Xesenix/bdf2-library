<?php
namespace BDF2\Resources\Loader
{
	use Symfony\Component\Config\FileLocatorInterface;
	use Symfony\Component\Config\Loader\FileLoader;
	use Symfony\Component\Filesystem\Filesystem;
	use BDF2\Resources\PathHelper;
	
	class AssetLoader extends FileLoader
	{
		protected $filelocator;
		
		protected $assetDirectory;
		
		protected $pathHelper;
		
		protected $fs;
		
		protected $publishMode = true;
		
		protected $publishName = null;
		
		/**
		 * @param FileLocatorInterface $filelocator helper for searching asset sources
		 * @param string $assetDirectory file system path to directory in which all assets are stored in public access area
		 */
		public function __construct(FileLocatorInterface $filelocator, $assetDirectory, PathHelper $pathHelper)
		{
			parent::__construct($filelocator);
			$this->assetDirectory = $assetDirectory;
			$this->pathHelper = $pathHelper;
			$this->fs = new Filesystem();
		}
		
		/**
		 * Whether or not copy resource files to public directory.
		 */
		public function publishMode($mode)
		{
			$this->publishMode = $mode;
			
			return $this;
		}
		
		/**
		 * File name for loaded asset.
		 */
		public function publishName($name)
		{
			$this->publishName = $name;
			
			return $this;
		}
		
		/**
		 * @param string $resource filesystem path to resource
		 */
		public function load($resource, $type = null)
		{
			if ($this->publishMode && $this->publishName !== null)
			{
				$assetsPath = $this->pathHelper->joinPaths($this->assetDirectory, $this->publishName);
				
				$this->fs->copy($resource, $assetsPath);
			}
			
			return $resource;
		}
		
		public function supports($resource, $type = null)
		{
			return true;
		}
		
		public function unload($path)
		{
			$assetsPath = $this->pathHelper->joinPaths($this->assetDirectory, $path);
			
			$this->fs->remove($assetsPath);
		}
	}
}
