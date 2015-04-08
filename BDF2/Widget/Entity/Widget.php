<?php
namespace BDF2\Widget\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="widget")
 **/
class Widget
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/** 
	 * @ORM\Column(type="string")
	 */
	public $name;

	/** 
	 * @ORM\Column(type="string")
	 */
	public $position;

	/**
	 * @ORM\Column(type="text")
	 */
	public $content;

	public $route = 'widget:content';
	
	
	public function setId($id) {
		$this->id = $id;

		return $this;
	}

	public function getId() {
		return $this->id;
	}
}
