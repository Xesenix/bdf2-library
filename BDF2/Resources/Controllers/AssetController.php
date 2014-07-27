<?php
namespace BDF2\Resources\Controllers
{
	use Silex\Application;
	use Symfony\Component\HttpFoundation\Request;
	
	class AssetController {
		
		public function generateAssetAction(Application $app, Request $request)
		{
			$file = $request->get('file');
			
			$asset = $app['resources.asset.assets'];
			
			$path = $asset->publishAsset($file);
			
			if ($path === null)
			{
				$app->abort(404, "Zasób $file nie istnieje.");
			}
			
			return $app->sendFile($path, 200, array('Content-Type' => 'image/png'));
		}
		
		public function generateCssAction(Application $app, Request $request)
		{
			$file = $request->get('file');
			
			$asset = $app['resources.asset.css'];
			
			$path = $asset->publishAsset($file);
			
			if ($path === null)
			{
				$app->abort(404, "Zasób $file nie istnieje.");
			}
			
			return $app->sendFile($path, 200, array('Content-Type' => 'text/css'));
		}
		
		public function clearCssAction(Application $app, Request $request)
		{
			$path = $request->get('path');
			
			$asset = $app['resources.asset.css'];
			
			$asset->unpublishAssets($path);
			
			return '';
		}
		
		public function generateJsAction(Application $app, Request $request)
		{
			$file = $request->get('file');
			
			$asset = $app['resources.asset.js'];
			
			$path = $asset->publishAsset($file);
			
			if ($path === null)
			{
				$app->abort(404, "Zasób $file nie istnieje.");
			}
			
			return $app->sendFile($path, 200, array('Content-Type' => 'text/javascript'));
		}
		
		public function clearJsAction(Application $app, Request $request)
		{
			$path = $request->get('path');
			
			$asset = $app['resources.asset.js'];
			
			$asset->unpublishAssets($path);
			
			return '';
		}
	}
	
}
