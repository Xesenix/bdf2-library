<?php
namespace BDF2\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AbstractController
{
	protected $app;

	public function __construct(Application $app) {
		$this->app = $app;
	}

	public function render($template, array $data) {
		$this->app['dispatcher']->dispatch('twig:render');

		return $this->app['twig']->render($template, $data);
	}
}
