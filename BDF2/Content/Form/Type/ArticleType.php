<?php
namespace BDF2\Content\Form\Type
{
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\FormBuilderInterface;
	use Symfony\Component\OptionsResolver\OptionsResolverInterface;
	
	class ArticleType extends AbstractType {
		
		public function buildForm(FormBuilderInterface $builder, array $options)
		{
			$form = $builder
				->add('id', 'hidden')
				->add('slug')
				->add('title')
				->add('content', 'test')
				->add('author');
				//->add('date');
		}
		
		public function setDefaultOptions(OptionsResolverInterface $resolver)
	    {
			$resolver->setDefaults(array(
				'data_class' => 'BDF2\Content\Entity\Article',
			));
	    }
	
	    public function getName()
	    {
			return 'article';
	    }
	}
}
