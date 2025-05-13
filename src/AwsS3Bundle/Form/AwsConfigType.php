<?php

namespace Klizer\AwsS3Bundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AwsConfigType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	    ->add('AWS_ACCESS_KEY_ID', TextType::class, [
		'label' => 'Access Key ID',
	    ])
	    ->add('AWS_SECRET_ACCESS_KEY', TextType::class, [
		'label' => 'Secret Access Key',
	    ])
	    ->add('AWS_REGION', TextType::class, [
		'label' => 'AWS Region',
	    ])
	    ->add('AWS_BUCKET_NAME', TextType::class, [
		'label' => 'Bucket Name',
	    ])
	    ->add('AWS_PREFIX', TextType::class, [
		'label' => 'File Path Prefix (optional)',
		'required' => false,
	    ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}

