<?php
namespace BDF2\Widget\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use BDF2\Controllers\AbstractController;
use BDF2\Widget\Entity\Widget;

class WidgetController extends AbstractController
{

	public function renderPositionAction($position) {
		$entityManager = $this->app['orm.em'];

		$widgets = $entityManager->getRepository('BDF2\Widget\Entity\Widget')->findByPosition($position);

		return $this->render('widget/position.html', array('widgets' => $widgets, ));
	}

	public function renderContentAction(Widget $widget) {
		return $this->render('widget/content.html', array('widget' => $widget, ));
	}

}
