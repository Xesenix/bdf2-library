<?php

namespace BDF2\Content\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\Loggable
 * @ORM\Entity
 * @ORM\Table(name="articles")
 */
class Article
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

	/** 
	 * @Gedmo\Versioned
	 * @ORM\Column(type="text")
	 */
	protected $content;

	/** 
	 * @Gedmo\Versioned
	 * @ORM\Column(type="string")
	 */
	protected $author;

	/** 
	 * @ORM\Column(type="date") 
	 */
	protected $date;
	

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

	public function setContent($content) {
		$this->content = $content;

		return $this;
	}

	public function getContent() {
		return $this->content;
	}

	public function setAuthor($author) {
		$this->author = $author;

		return $this;
	}

	public function getAuthor() {
		return $this->author;
	}

	public function setDate($date) {
		$this->date = $date;

		return $this;
	}

	public function getDate() {
		return $this->date;
	}

}
