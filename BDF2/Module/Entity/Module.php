<?php
namespace BDF2\Module\Entity
{
	/**
	 * @Entity @Table(name="module")
	 **/
	class Module {
		
		/** @Id @Column(type="integer") @GeneratedValue **/
		public $id;
		
		/** @Column(type="string") **/
		public $name;
		
		/** @Column(type="string") **/
		public $position;
		
		/** @Column(type="text") **/
		public $content;
		
		
		public $route = 'module:content';
	}
}
