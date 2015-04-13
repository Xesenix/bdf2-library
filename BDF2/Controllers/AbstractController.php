<?php
namespace BDF2\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AbstractController
{
	protected $app;

	public function __construct(Application $app) {
		$this->app = $app;
	}

	public function render($template, array $data = array()) {
		$this->app['dispatcher']->dispatch('twig:render');
		
		try {
			return new Response($this->app['twig']->render($template, $data), 200, array(
				'Cache-Control' => 's-maxage=5',
			));
		}
		catch(\Twig_Error_Loader $error)
		{
			$this->app->abort(404, "Strona nie istnieje.");
		}
	}
}
