<?php
namespace BDF2\Resources\File
{
	use Symfony\Component\Filesystem\Filesystem; 
	use BDF2\Resources\PathHelper;
	
	class FileLocator {
		
		protected $paths;
		
		protected $pathHelper;
		
		public function __construct(array $paths, PathHelper $pathHelper)
		{
			$this->paths = array_reverse($paths);
			$this->pathHelper = $pathHelper;
			$this->fs = new Filesystem();
		}
		
		public function find($file)
		{
			foreach ($this->paths as $path)
			{
				$filePath = $this->pathHelper->joinPaths($path, $file);
				
				if ($this->fs->exists($filePath))
				{
					return $filePath;
				}
			}
			
			return null;
		}
	}
	
}
