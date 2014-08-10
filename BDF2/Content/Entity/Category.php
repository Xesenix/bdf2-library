<?php

namespace BDF2\Content\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\Loggable
 * @ORM\Entity
 * @ORM\Table(name="categories")
 */
class Category
{

	/** 
	 * @ORM\Id 
	 * @ORM\Column(type="integer") 
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/** 
	 * @Gedmo\Versioned
	 * @ORM\Column(type="string")
	 */
	protected $slug;

	/** 
	 * @Gedmo\Versioned
	 * @ORM\Column(type="string")
	 */
	protected $title;

	public function setId($id) {
		$this->id = $id;

		return $this;
	}

	public function getId() {
		return $this->id;
	}

	public function setSlug($slug) {
		$this->slug = $slug;

		return $this;
	}

	public function getSlug() {
		return $this->slug;
	}

	public function setTitle($title) {
		$this->title = $title;

		return $this;
	}

	public function getTitle() {
		return $this->title;
	}
}
