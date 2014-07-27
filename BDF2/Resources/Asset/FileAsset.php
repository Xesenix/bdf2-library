<?php
namespace BDF2\Resources\Asset
{
	use Symfony\Component\Filesystem\Filesystem;
	use BDF2\Resources\PathHelper;
	use BDF2\Resources\File\FileLocator;
	
	class FileAsset
	{
		protected $filelocator;
		
		protected $assetDirectory;
		
		protected $pathHelper;
		
		protected $fs;
		
		protected $devMode = false;
		
		/**
		 * @param FileLocator $filelocator helper for searching asset sources
		 * @param string $assetDirectory file system path to directory in which all assets are stored in public access area
		 */
		public function __construct(FileLocator $filelocator, $assetDirectory, PathHelper $pathHelper)
		{
			$this->filelocator = $filelocator;
			$this->assetDirectory = $assetDirectory;
			$this->pathHelper = $pathHelper;
			$this->fs = new Filesystem();
		}
		
		/**
		 * Whether or not copy resource files to public directory.
		 */
		public function setDevMode($mode)
		{
			$this->devMode = $mode;
			
			return $this;
		}
		
		public function getAsset($asset)
		{
			if (($filepath = $this->filelocator->find($asset)) !== null)
			{
				return \file_get_contents($filepath);
			}
			
			return null;
		}
		
		public function publishAsset($asset)
		{
			$assetsPath = $this->pathHelper->joinPaths($this->assetDirectory, $asset);
			
			if (($filepath = $this->filelocator->find($asset)) !== null)
			{
				if (!$this->devMode)
				{
					$this->fs->copy($filepath, $assetsPath);
				}
				
				return $filepath;
			}
			
			return null;
		}
		
		public function unpublishAssets($path)
		{
			$assetsPath = $this->pathHelper->joinPaths($this->assetDirectory, $path);
			
			$this->fs->remove($assetsPath);
		}
	}
}
