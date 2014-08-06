<?php
namespace BDF2\Module\Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use BDF2\Controllers\AbstractController;
use BDF2\Module\Entity\Module;

class ModuleController extends AbstractController
{

	public function renderPositionAction($position) {
		$entityManager = $this->app['orm.em'];

		$modules = $entityManager->getRepository('BDF2\Module\Entity\Module')->findByPosition($position);

		return $this->render('module/position.html', array('modules' => $modules, ));
	}

	public function renderContentAction(Module $module) {
		return $this->render('module/content.html', array('module' => $module, ));
	}

}
