<?php

namespace BDF2\Content\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BDF2\Controllers\AbstractController;

class StaticController extends AbstractController {

	public function render($template, array $data = array()) {
		$this->app['dispatcher']->dispatch('static:render');
		
		return parent::render($template, $data);
	}
	
	public function page(Application $app)
	{
		$page = $this->app['request']->get('page');
		
		try {
			return $this->render('static/' . $page . '.html');
		}
		catch(Exception $ex)
		{
			$this->app->abort(404, "Podstrona $page nie istnieje.");
		}
	}
}