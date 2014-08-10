<?php
namespace BDF2\Content\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$form = $builder->add('id', 'hidden')
			->add('slug')
			->add('title');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array('data_class' => 'BDF2\Content\Entity\Category', ));
	}

	public function getName() {
		return 'category';
	}

}
