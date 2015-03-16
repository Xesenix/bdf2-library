<?php
namespace BDF2\Content\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{

	protected $dateTransformer = null;

	public function __construct(DataTransformerInterface $dateTransformer) {
		$this->dateTransformer = $dateTransformer;
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$form = $builder->add('id', 'hidden')
			->add('slug')
			->add('title')
			->add('content', 'textarea', array('required' => false))
			->add('author', 'text', array('required' => false))
			->add('date', 'date', array('attr' => array('class' => 'datapicker',), 'required' => false, 'widget' => 'single_text'));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array('data_class' => 'BDF2\Content\Entity\Article', ));
	}

	public function getName() {
		return 'article';
	}

}
