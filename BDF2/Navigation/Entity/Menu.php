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
	public $id;

	/** 
	 * @ORM\Column(type="string")
	 */
	public $name;
}
