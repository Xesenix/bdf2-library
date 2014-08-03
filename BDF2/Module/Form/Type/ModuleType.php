<?php
namespace BDF2\Module\Form\Type
{
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\DataTransformerInterface;
	use Symfony\Component\Form\FormBuilderInterface;
	use Symfony\Component\OptionsResolver\OptionsResolverInterface;
	
	class ModuleType extends AbstractType {
		
		protected $dateTransformer = null;
		
		public function buildForm(FormBuilderInterface $builder, array $options)
		{
			$form = $builder
				->add('id', 'hidden')
				->add('name')
				->add('position')
				->add('content', 'textarea');
		}
		
		public function setDefaultOptions(OptionsResolverInterface $resolver)
	    {
			$resolver->setDefaults(array(
				'data_class' => 'BDF2\Module\Entity\Module',
			));
	    }
	
	    public function getName()
	    {
			return 'module';
	    }
	}
}
