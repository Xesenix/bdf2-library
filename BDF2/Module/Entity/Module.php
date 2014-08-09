<?php
namespace BDF2\Module\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="module")
 **/
class Module
{

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

	/** 
	 * @ORM\Column(type="string")
	 */
	public $position;

	/**
	 * @ORM\Column(type="text")
	 */
	public $content;

	public $route = 'module:content';
}
