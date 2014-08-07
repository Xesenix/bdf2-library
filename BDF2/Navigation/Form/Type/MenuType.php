<?php
namespace BDF2\Navigation\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MenuType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$form = $builder->add('id', 'hidden')
			->add('name');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array('data_class' => 'BDF2\Navigation\Entity\Menu', ));
	}

	public function getName() {
		return 'menu';
	}

}
