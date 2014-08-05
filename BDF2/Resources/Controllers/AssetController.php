<?php
namespace BDF2\Resources\Controllers
{
	use Silex\Application;
	use Symfony\Component\HttpFoundation\Request;
	use BDF2\Controllers\AbstractController;
	use Symfony\Component\HttpFoundation\File\File;
	
	class AssetController extends AbstractController {
		
		public function generateAssetAction($file)
		{
			$loader = $this->app['resources.asset.loader'];
			
			$loader->publishName($file);
			
			$path = $loader->import($file);
			
			if ($path === null)
			{
				$this->app->abort(404, "Zasób $file nie istnieje.");
			}
			
			$assetFile = new File($path);
			
			return $this->app->sendFile($path, 200, array('Content-Type' => $assetFile->getMimeType()));
		}
		
		public function generateCssAction($file)
		{
			$loader = $this->app['resources.asset.loader'];
			
			$loader->publishName($file);
			
			$path = $loader->import($file);
			
			if ($path === null)
			{
				$this->app->abort(404, "Zasób $file nie istnieje.");
			}
			
			return $this->app->sendFile($path, 200, array('Content-Type' => 'text/css'));
		}
		
		public function generateJsAction($file)
		{
			$loader = $this->app['resources.asset.loader'];
			
			$loader->publishName($file);
			
			$path = $loader->import($file);
			
			if ($path === null)
			{
				$this->app->abort(404, "Zasób $file nie istnieje.");
			}
			
			return $this->app->sendFile($path, 200, array('Content-Type' => 'text/javascript'));
		}
		
		public function clearAction($path)
		{
			$asset = $this->app['resources.asset.loader'];
			
			$asset->unload($path);
			
			return '';
		}
	}
	
}
