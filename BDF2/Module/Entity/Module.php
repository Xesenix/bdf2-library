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
		public $route;
		
		/** @Column(type="string") **/
		public $name;
		
		
	}
}
