<?php
namespace BDF2\Resources\Controllers
{
	use Silex\Application;
	use Symfony\Component\HttpFoundation\Request;
	
	class AssetController {
		
		public function generateCssAction(Application $app, Request $request)
		{
			$file = $request->get('file');
			
			$asset = $app['resources.asset.css'];
			
			$data = $asset->publishAsset($file);
			
			if ($data === null)
			{
				$app->abort(404, "Zasób $file nie istnieje.");
			}
			
			return $data;
		}
		
		public function clearCssAction(Application $app, Request $request)
		{
			$path = $request->get('path');
			
			$asset = $app['resources.asset.css'];
			
			$asset->unpublishAssets($path);
		}
		
		public function generateJsAction(Application $app, Request $request)
		{
			$file = $request->get('file');
			
			$asset = $app['resources.asset.js'];
			
			$data = $asset->publishAsset($file);
			
			if ($data === null)
			{
				$app->abort(404, "Zasób $file nie istnieje.");
			}
			
			return $data;
		}
		
		public function clearJsAction(Application $app, Request $request)
		{
			$path = $request->get('path');
			
			$asset = $app['resources.asset.js'];
			
			$asset->unpublishAssets($path);
		}
	}
	
}
