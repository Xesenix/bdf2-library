<?php
namespace BDF2\Navigation\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity 
 * @ORM\Table(name="menu")
 */
class Menu {
	
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
	
	
	public function setId($id) {
		$this->id = $id;

		return $this;
	}

	public function getId() {
		return $this->id;
	}
}
