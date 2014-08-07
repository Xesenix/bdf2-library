<?php
namespace BDF2\Navigation\Entity;

/**
 * @Entity @Table(name="menu")
 **/
class Menu {
	
	/** @Id @Column(type="integer") @GeneratedValue **/
	public $id;

	/** @Column(type="string") **/
	public $name;
}
